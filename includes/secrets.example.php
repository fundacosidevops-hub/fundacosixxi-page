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
    'openai_api_key' => 'pega-tu-clave-de-openai-aqui',

    // Claude (Anthropic): https://platform.claude.com/settings/keys
    'anthropic_api_key' => 'pega-tu-clave-de-anthropic-aqui',

    // Clave del sitio para webapi.fundacosixxi.com. Debe ser la misma que pongas
    // en WEB_CHAT_KEYS del .env de webapi-app (mínimo 24 caracteres).
    // Genera una con:  php -r "echo bin2hex(random_bytes(24));"
    'webapi_key' => 'pega-la-clave-del-sitio-aqui',
];
