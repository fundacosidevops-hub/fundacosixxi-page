<?php require __DIR__ . '/includes/config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Política de privacidad | <?= e($site['short_name']) ?></title>
    <meta name="robots" content="noindex, follow">
    <link rel="icon" href="favicon.svg" type="image/svg+xml">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Plus+Jakarta+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css?v=2">
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased">
<main class="max-w-3xl mx-auto px-5 py-16 md:py-24">
    <a href="index.php" class="text-teal-700 text-sm font-semibold">&larr; Volver al inicio</a>
    <h1 class="font-serif text-3xl md:text-5xl font-bold mt-6 mb-8">Política de privacidad</h1>
    <div class="space-y-5 leading-relaxed text-slate-700">
        <!-- Texto base: debe ser revisado por el asesor legal de la institución -->
        <p><strong><?= e($site['name']) ?></strong> respeta la privacidad de las personas que nos contactan a través de este sitio web.</p>
        <h2 class="font-serif text-2xl font-bold pt-4">Datos que recopilamos</h2>
        <p>A través del formulario de quejas y sugerencias recopilamos nombre, correo electrónico, teléfono, el área donde fue atendido y el contenido de su mensaje.</p>
        <p>Si solicita una cita a través del asistente virtual, recopilamos su nombre, teléfono, la especialidad, la fecha preferida y, si lo indica, el motivo breve de la consulta.</p>
        <h2 class="font-serif text-2xl font-bold pt-4">Asistente virtual con inteligencia artificial</h2>
        <p>El asistente del sitio usa un servicio de inteligencia artificial de OpenAI para responder sus preguntas. Los mensajes que escribe en el chat se envían a ese proveedor para generar la respuesta. Le recomendamos no compartir información médica sensible en el chat. Las respuestas del asistente son orientativas y no sustituyen una consulta médica.</p>
        <h2 class="font-serif text-2xl font-bold pt-4">Para qué los usamos</h2>
        <p>Usamos estos datos únicamente para dar seguimiento a su mensaje o solicitud de cita y para mejorar nuestros servicios. No los vendemos ni los compartimos con terceros con fines comerciales.</p>
        <h2 class="font-serif text-2xl font-bold pt-4">Sus derechos</h2>
        <p>Puede solicitar el acceso, la corrección o la eliminación de sus datos, conforme a la Ley n.º 172-13 sobre protección de datos personales de la República Dominicana, escribiendo a <a class="text-teal-700 underline" href="mailto:<?= e($site['email']) ?>"><?= e($site['email']) ?></a> o llamando al <a class="text-teal-700 underline" href="tel:<?= e($site['phone_e164']) ?>"><?= e($site['phone']) ?></a>.</p>
        <h2 class="font-serif text-2xl font-bold pt-4">Servicios de terceros</h2>
        <p>Este sitio usa servicios externos como Google Maps, Google Fonts, WhatsApp y OpenAI, que pueden recopilar información según sus propias políticas.</p>
    </div>
</main>
</body>
</html>
