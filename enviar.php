<?php
/**
 * Procesa el formulario de quejas y sugerencias.
 * - Valida los datos en el servidor
 * - Guarda cada mensaje en data/mensajes.csv (carpeta bloqueada al público)
 * - Intenta enviar un correo a $site['form_to']
 */
session_start();
require __DIR__ . '/includes/config.php';

$wantsJson = stripos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false;

function respond(bool $ok, string $msg, int $code = 200) {
    global $wantsJson;
    http_response_code($code);
    if ($wantsJson) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => $ok, 'message' => $msg], JSON_UNESCAPED_UNICODE);
    } else {
        // Sin JavaScript: página simple de respuesta
        header('Content-Type: text/html; charset=utf-8');
        echo '<!DOCTYPE html><html lang="es"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">'
           . '<title>Formulario</title><link rel="icon" href="favicon.ico"><body style="font-family:sans-serif;max-width:560px;margin:15vh auto;padding:0 20px;text-align:center"><img src="Imagenes/logo/logo-256.webp" alt="Fundacosi XXI" width="120" height="120">'
           . '<p style="font-size:18px">' . e($msg) . '</p><p><a href="index.php#contacto">Volver al sitio</a></p></body></html>';
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(false, 'Método no permitido.', 405);
}

// Anti-spam: campo trampa
if (!empty($_POST['website'])) {
    respond(true, '¡Gracias! Recibimos tu mensaje.');
}

// Token CSRF
if (empty($_POST['csrf']) || empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
    respond(false, 'La sesión expiró. Recarga la página e intenta de nuevo.', 400);
}

// Límite: 1 envío cada 60 s por sesión
if (!empty($_SESSION['last_submit']) && time() - $_SESSION['last_submit'] < 60) {
    respond(false, 'Ya recibimos un mensaje hace poco. Espera un minuto antes de enviar otro.', 429);
}

$clean = fn($k, $max) => mb_substr(trim(strip_tags((string)($_POST[$k] ?? ''))), 0, $max);

$nombre   = $clean('nombre', 120);
$email    = $clean('email', 160);
$telefono = $clean('telefono', 20);
$area     = $clean('area', 80);
$tipo     = $clean('tipo', 20);
$mensaje  = $clean('mensaje', 3000);
$consent  = !empty($_POST['consentimiento']);

$validAreas = array_merge(array_column($services, 'title'), ['Recepción / Atención al usuario', 'Otro']);
$validTipos = ['Queja', 'Sugerencia', 'Felicitación'];

$errors = [];
if (mb_strlen($nombre) < 3)                       $errors[] = 'nombre';
if (!filter_var($email, FILTER_VALIDATE_EMAIL))    $errors[] = 'correo';
if (!preg_match('/^[0-9+\-\s()]{7,20}$/', $telefono)) $errors[] = 'teléfono';
if (!in_array($area, $validAreas, true))           $errors[] = 'área';
if (!in_array($tipo, $validTipos, true))           $errors[] = 'tipo de mensaje';
if (mb_strlen($mensaje) < 10)                      $errors[] = 'mensaje (mínimo 10 caracteres)';
if (!$consent)                                     $errors[] = 'aceptación de la política de privacidad';

if ($errors) {
    respond(false, 'Revisa los siguientes campos: ' . implode(', ', $errors) . '.', 422);
}

$fecha = date('Y-m-d H:i:s');

// 1) Guardar en CSV
$dir = __DIR__ . '/data';
if (!is_dir($dir)) { @mkdir($dir, 0750, true); }
$file = $dir . '/mensajes.csv';
$isNew = !file_exists($file);
$saved = false;
if ($fh = @fopen($file, 'a')) {
    if (flock($fh, LOCK_EX)) {
        if ($isNew) { fwrite($fh, "\xEF\xBB\xBF"); fputcsv($fh, ['Fecha', 'Tipo', 'Área', 'Nombre', 'Correo', 'Teléfono', 'Mensaje']); }
        // Evita inyección de fórmulas al abrir en Excel
        $safe = fn($v) => preg_match('/^[=+\-@]/', $v) ? "'" . $v : $v;
        fputcsv($fh, array_map($safe, [$fecha, $tipo, $area, $nombre, $email, $telefono, $mensaje]));
        flock($fh, LOCK_UN);
        $saved = true;
    }
    fclose($fh);
}

// 2) Enviar correo
$subject = "=?UTF-8?B?" . base64_encode("[$tipo] Nuevo mensaje desde la web – $area") . "?=";
$body = "Fecha: $fecha\nTipo: $tipo\nÁrea: $area\nNombre: $nombre\nCorreo: $email\nTeléfono: $telefono\n\nMensaje:\n$mensaje\n";
$host = preg_replace('/[^a-z0-9.\-]/i', '', $_SERVER['SERVER_NAME'] ?? 'fundacosixxi.com');
$headers = implode("\r\n", [
    'From: Sitio web <no-reply@' . $host . '>',
    'Reply-To: ' . $email,
    'Content-Type: text/plain; charset=UTF-8',
]);
$mailed = @mail($site['form_to'], $subject, $body, $headers);

if (!$saved && !$mailed) {
    respond(false, 'No pudimos registrar tu mensaje en este momento. Por favor escríbenos por WhatsApp.', 500);
}

$_SESSION['last_submit'] = time();
respond(true, '¡Gracias por tu mensaje! Nuestro equipo lo revisará y te contactará si es necesario.');
