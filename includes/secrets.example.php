<?php
/**
 * PLANTILLA DE CLAVES SECRETAS
 * 1. Copia este archivo como  includes/secrets.php
 * 2. Pega tus claves (solo hace falta la del proveedor elegido en config.php)
 * 3. NUNCA subas secrets.php a GitHub (ya está en .gitignore)
 *
 * Alternativa: variables de entorno OPENAI_API_KEY, ANTHROPIC_API_KEY y WEBAPI_KEY.
 */
return [
    // ChatGPT (OpenAI): https://platform.openai.com/api-keys
    'openai_api_key' => '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',

    // Claude (Anthropic): https://platform.claude.com/settings/keys
    'anthropic_api_key' => 'pega-tu-clave-de-anthropic-aqui',

    // Clave del sitio para webapi.fundacosixxi.com. Debe ser la misma que pongas
    // en WEB_CHAT_KEYS del .env de webapi-app (mínimo 24 caracteres).
    // Genera una con:  php -r "echo bin2hex(random_bytes(24));"
    'webapi_key' => 'b7528b67443c9257f356ea1d156f17197ae1da6bbcdb3c28',
];
