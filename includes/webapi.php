<?php
/**
 * Cliente de webapi.fundacosixxi.com (proyecto webapi-app) para el chatbot.
 * Usa las rutas /v1/web/* con la clave X-Web-Key (includes/secrets.php → 'webapi_key').
 * Las respuestas de lectura se guardan unos minutos en data/cache/ para no
 * consultar la API en cada mensaje.
 */

function webapi_key(): string {
    static $key = null;
    if ($key === null) {
        $secrets = is_file(__DIR__ . '/secrets.php') ? (include __DIR__ . '/secrets.php') : [];
        $key = getenv('WEBAPI_KEY') ?: (string)($secrets['webapi_key'] ?? '');
        if (strpos($key, 'pega-') !== false) $key = '';
    }
    return $key;
}

function webapi_enabled(): bool {
    global $webapi;
    return !empty($webapi['enabled']) && webapi_key() !== '';
}

/** @return array{0:int,1:?array} [código HTTP, cuerpo JSON] */
function webapi_request(string $method, string $path, array $data = []): array {
    global $webapi;
    $url = rtrim($webapi['base_url'], '/') . '/' . ltrim($path, '/');
    if ($method === 'GET' && $data) $url .= '?' . http_build_query($data);
    $headers = ['Accept: application/json', 'X-Web-Key: ' . webapi_key()];
    $body = null;
    if ($method !== 'GET') {
        $headers[] = 'Content-Type: application/json';
        $body = json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST => $method, CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => (int)$webapi['timeout'], CURLOPT_CONNECTTIMEOUT => 5,
        ]);
        if ($body !== null) curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        $resp = curl_exec($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
    } else {
        $ctx = stream_context_create(['http' => [
            'method' => $method, 'header' => implode("\r\n", $headers), 'content' => $body ?? '',
            'timeout' => (int)$webapi['timeout'], 'ignore_errors' => true,
        ]]);
        $resp = @file_get_contents($url, false, $ctx);
        $code = isset($http_response_header[0]) && preg_match('/\s(\d{3})\s/', $http_response_header[0], $m) ? (int)$m[1] : 0;
    }
    if ($resp === false || $code === 0) {
        error_log("[webapi] Sin respuesta de $method $path");
        return [0, null];
    }
    if ($code >= 500 || $code === 401) error_log("[webapi] HTTP $code en $method $path: " . substr((string)$resp, 0, 200));
    return [$code, json_decode((string)$resp, true)];
}

/** GET con caché en archivo. Devuelve null si la API no responde y no hay caché. */
function webapi_cached_get(string $path, array $query, int $ttl): ?array {
    $dir = dirname(__DIR__) . '/data/cache';
    if (!is_dir($dir)) @mkdir($dir, 0750, true);
    $file = $dir . '/' . md5($path . '?' . http_build_query($query)) . '.json';

    if (is_file($file) && time() - filemtime($file) < $ttl) {
        $data = json_decode((string)file_get_contents($file), true);
        if (is_array($data)) return $data;
    }
    [$code, $data] = webapi_request('GET', $path, $query);
    if ($code === 200 && is_array($data) && !empty($data['isSuccess'])) {
        @file_put_contents($file, json_encode($data, JSON_UNESCAPED_UNICODE), LOCK_EX);
        return $data;
    }
    // Si falla, se usa la última copia guardada aunque esté vieja (mejor que nada).
    if (is_file($file)) {
        $data = json_decode((string)file_get_contents($file), true);
        if (is_array($data)) return $data;
    }
    return null;
}

/** Servicios con médicos y seguros aceptados. */
function webapi_info(): ?array {
    global $webapi;
    return webapi_enabled() ? webapi_cached_get('web/info', [], (int)$webapi['cache_info']) : null;
}

/** Médicos que atienden hoy o mañana. */
function webapi_schedule(string $day = 'today', ?int $serviceId = null): ?array {
    global $webapi;
    if (!webapi_enabled()) return null;
    $q = ['date' => $day === 'tomorrow' ? 'tomorrow' : 'today'];
    if ($serviceId) $q['service_id'] = $serviceId;
    return webapi_cached_get('web/schedule', $q, (int)$webapi['cache_schedule']);
}

/** Busca un servicio por nombre (sin importar tildes ni mayúsculas). */
function webapi_find_service(?array $info, string $name): ?array {
    if (!$info || $name === '') return null;
    $norm = fn($s) => strtoupper(trim(preg_replace('/\s+/', ' ', iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s) ?: $s)));
    $n = $norm($name);
    foreach ($info['services'] ?? [] as $s) if ($norm($s['name']) === $n) return $s;
    foreach ($info['services'] ?? [] as $s) if (strpos($norm($s['name']), $n) !== false || strpos($n, $norm($s['name'])) !== false) return $s;
    return null;
}

/** Busca un médico por nombre dentro de un servicio (o en todos). */
function webapi_find_doctor(?array $info, string $name, ?array $service = null): ?array {
    if (!$info || trim($name) === '') return null;
    $norm = fn($s) => strtoupper(trim(preg_replace('/^(DR|DRA|DOCTOR|DOCTORA)\.?\s+/i', '', iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s) ?: $s)));
    $n = $norm($name);
    $pool = $service ? [$service] : ($info['services'] ?? []);
    foreach ($pool as $s) {
        foreach ($s['doctors'] ?? [] as $d) {
            $dn = $norm($d['name']);
            if ($dn === $n || strpos($dn, $n) !== false) return $d;
        }
    }
    return null;
}
