<?php
/**
 * Capa común para hablar con el proveedor de IA elegido en config.php:
 *   $chatbot['provider'] = 'openai'  → OpenAI (Chat Completions)
 *   $chatbot['provider'] = 'anthropic' → Claude (Messages API)
 *
 * llm_run() recibe las instrucciones, el historial (user/assistant) y las
 * herramientas en un formato neutro, ejecuta las llamadas a herramientas que
 * pida el modelo y devuelve el texto final.
 */

function llm_provider(): string {
    global $chatbot;
    return ($chatbot['provider'] ?? 'openai') === 'anthropic' ? 'anthropic' : 'openai';
}

function llm_api_key(): string {
    $secrets = is_file(__DIR__ . '/secrets.php') ? (include __DIR__ . '/secrets.php') : [];
    $key = llm_provider() === 'anthropic'
        ? (getenv('ANTHROPIC_API_KEY') ?: ($secrets['anthropic_api_key'] ?? ''))
        : (getenv('OPENAI_API_KEY') ?: ($secrets['openai_api_key'] ?? ''));
    return (strpos((string)$key, 'pega-') !== false) ? '' : (string)$key;
}

/**
 * @param string   $system   Instrucciones del asistente
 * @param array    $history  [['role'=>'user'|'assistant','content'=>string], ...]
 * @param array    $tools    [['name'=>..., 'description'=>..., 'parameters'=>JSON Schema], ...]
 * @param callable $exec     fn(string $name, array $args): array  → resultado de la herramienta
 * @return string|null       Texto final, o null si la IA no respondió
 */
function llm_run(string $system, array $history, array $tools, callable $exec, int $maxRounds = 4): ?string {
    return llm_provider() === 'anthropic'
        ? llm_run_anthropic($system, $history, $tools, $exec, $maxRounds)
        : llm_run_openai($system, $history, $tools, $exec, $maxRounds);
}

/* ---------------- OpenAI ---------------- */

function llm_run_openai(string $system, array $history, array $tools, callable $exec, int $maxRounds): ?string {
    global $chatbot;
    $cfg = $chatbot['providers']['openai'];
    $messages = array_merge([['role' => 'system', 'content' => $system]], $history);
    $oaTools = array_map(fn($t) => ['type' => 'function', 'function' => $t], $tools);

    for ($i = 0; $i < $maxRounds; $i++) {
        $payload = [
            'model' => $cfg['model'],
            'messages' => $messages,
            'tools' => $oaTools,
            'max_completion_tokens' => (int)($cfg['max_tokens'] ?? 600),
        ];
        // Los modelos de razonamiento solo usan herramientas en Chat Completions con reasoning_effort = none.
        if (!empty($cfg['reasoning_effort'])) $payload['reasoning_effort'] = $cfg['reasoning_effort'];

        $res = llm_http(rtrim($cfg['api_base'], '/') . '/chat/completions',
            ['Authorization: Bearer ' . llm_api_key()], $payload);
        // Si el modelo no acepta algún parámetro opcional, se reintenta sin él.
        if (!empty($res['_bad_param'])) {
            unset($payload[$res['_bad_param']]);
            $res = llm_http(rtrim($cfg['api_base'], '/') . '/chat/completions',
                ['Authorization: Bearer ' . llm_api_key()], $payload);
        }
        if (empty($res['choices'][0]['message'])) return null;

        $msg = $res['choices'][0]['message'];
        if (empty($msg['tool_calls'])) return trim((string)($msg['content'] ?? ''));

        $messages[] = $msg;
        foreach ($msg['tool_calls'] as $call) {
            $args = json_decode($call['function']['arguments'] ?? '{}', true) ?: [];
            $result = $exec($call['function']['name'] ?? '', $args);
            $messages[] = ['role' => 'tool', 'tool_call_id' => $call['id'], 'content' => json_encode($result, JSON_UNESCAPED_UNICODE)];
        }
    }
    return '';
}

/* ---------------- Claude (Anthropic) ---------------- */

function llm_run_anthropic(string $system, array $history, array $tools, callable $exec, int $maxRounds): ?string {
    global $chatbot;
    $cfg = $chatbot['providers']['anthropic'];
    // La conversación debe empezar con un mensaje del usuario.
    while ($history && $history[0]['role'] !== 'user') array_shift($history);
    $messages = array_values($history);
    $anTools = array_map(fn($t) => [
        'name' => $t['name'], 'description' => $t['description'], 'input_schema' => $t['parameters'],
    ], $tools);

    for ($i = 0; $i < $maxRounds; $i++) {
        $payload = [
            'model' => $cfg['model'],
            'max_tokens' => (int)($cfg['max_tokens'] ?? 1024),
            'system' => $system,
            'messages' => $messages,
            'tools' => $anTools,
        ];
        if (!empty($cfg['effort'])) $payload['output_config'] = ['effort' => $cfg['effort']];

        $headers = [
            'Authorization: Bearer ' . llm_api_key(),
            'anthropic-version: ' . ($cfg['version'] ?? '2026-10-21'),
        ];
        $url = rtrim($cfg['api_base'], '/') . '/messages';
        $res = llm_http($url, $headers, $payload);
        if (!empty($res['_bad_param'])) {
            unset($payload[$res['_bad_param']]);
            $res = llm_http($url, $headers, $payload);
        }
        if (!isset($res['content']) || !is_array($res['content'])) return null;

        $toolUses = array_values(array_filter($res['content'], fn($b) => ($b['type'] ?? '') === 'tool_use'));
        if (!$toolUses) {
            $text = implode("\n", array_map(fn($b) => $b['text'], array_filter($res['content'], fn($b) => ($b['type'] ?? '') === 'text')));
            return trim($text);
        }

        // Se devuelve el turno completo del asistente (incluye bloques de razonamiento) y luego los resultados.
        $messages[] = ['role' => 'assistant', 'content' => $res['content']];
        $results = [];
        foreach ($toolUses as $tu) {
            $result = $exec($tu['name'] ?? '', is_array($tu['input'] ?? null) ? $tu['input'] : []);
            $results[] = ['type' => 'tool_result', 'tool_use_id' => $tu['id'], 'content' => json_encode($result, JSON_UNESCAPED_UNICODE)];
        }
        $messages[] = ['role' => 'user', 'content' => $results];
    }
    return '';
}

/* ---------------- HTTP ---------------- */

/** POST JSON. Devuelve el cuerpo decodificado, null si falló, o ['_bad_param'=>x] si un parámetro opcional fue rechazado. */
function llm_http(string $url, array $headers, array $payload): ?array {
    $headers[] = 'Content-Type: application/json';
    $body = json_encode($payload, JSON_UNESCAPED_UNICODE);

    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true, CURLOPT_POSTFIELDS => $body, CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 40, CURLOPT_CONNECTTIMEOUT => 10,
        ]);
        $resp = curl_exec($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);
    } else {
        $ctx = stream_context_create(['http' => [
            'method' => 'POST', 'header' => implode("\r\n", $headers), 'content' => $body,
            'timeout' => 40, 'ignore_errors' => true,
        ]]);
        $resp = @file_get_contents($url, false, $ctx);
        $code = isset($http_response_header[0]) && preg_match('/\s(\d{3})\s/', $http_response_header[0], $m) ? (int)$m[1] : 0;
        $err = $resp === false ? 'sin respuesta' : '';
    }

    if ($resp !== false && $code >= 200 && $code < 300) return json_decode($resp, true);

    if ($code === 400 && is_string($resp)) {
        foreach (['reasoning_effort', 'output_config', 'max_completion_tokens'] as $p) {
            if (stripos($resp, $p) !== false && isset($payload[$p])) return ['_bad_param' => $p];
        }
    }
    error_log('[chatbot] Error IA (' . llm_provider() . ") HTTP $code $err " . substr((string)$resp, 0, 300));
    return null;
}
