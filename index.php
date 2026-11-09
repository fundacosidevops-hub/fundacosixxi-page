<?php
session_start();
require __DIR__ . '/includes/config.php';

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf'];
$year = date('Y');

$slides = [
    ['img' => 'Imagenes/mama',  'alt' => 'Grupo de mujeres con camisetas rosadas en una jornada de concienciación sobre el cáncer de mama',
     'tag' => 'Atención Primaria', 'title' => 'Tu salud es nuestra <span class="text-teal-400 italic">máxima prioridad</span>',
     'desc' => 'Servicios médicos integrales con profesionales comprometidos con tu bienestar y el de tu familia.'],
    ['img' => 'Imagenes/carpa', 'alt' => 'Pacientes esperando atención bajo una carpa durante un operativo médico',
     'tag' => 'Operativos Comunitarios', 'title' => 'Llevamos la salud <span class="text-teal-400 italic">a tu comunidad</span>',
     'desc' => 'Realizamos jornadas y operativos médicos para acercar la atención a quienes más la necesitan.'],
    ['img' => 'Imagenes/calle', 'alt' => 'Personas haciendo fila frente a la unidad de atención primaria',
     'tag' => 'Compromiso Humano', 'title' => 'Un equipo dedicado a <span class="text-teal-400 italic">cuidar de ti</span>',
     'desc' => 'Nuestro personal te acompaña en cada paso con una atención cálida y personalizada.'],
    ['img' => 'Imagenes/frente','alt' => 'Fachada de la unidad de atención primaria con área de emergencia',
     'tag' => 'Nuestras Instalaciones', 'title' => 'Salud accesible para <span class="text-teal-400 italic">toda la comunidad</span>',
     'desc' => 'Instalaciones preparadas para brindarte atención primaria de calidad en Villa Mella.'],
];

// Clases por color (deben existir en el safelist de tailwind.config.js)
function svc_classes(string $c): array {
    return [
        'bg'     => "bg-$c-600",
        'hover'  => "hover:bg-$c-700",
        'text'   => "text-$c-600",
        'shadow' => "shadow-$c-600/30",
        'blob'   => "bg-$c-100",
    ];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unidad de Atención Primaria Fundacosi XXI | Villa Mella, Santo Domingo Norte</title>
    <meta name="description" content="<?= e($site['description']) ?>">
    <link rel="canonical" href="<?= e($site['url']) ?>/">
    <meta name="theme-color" content="#315e6c">

    <!-- Open Graph / redes sociales -->
    <meta property="og:type" content="website">
    <meta property="og:locale" content="es_DO">
    <meta property="og:site_name" content="<?= e($site['short_name']) ?>">
    <meta property="og:title" content="<?= e($site['name']) ?>">
    <meta property="og:description" content="<?= e($site['description']) ?>">
    <meta property="og:url" content="<?= e($site['url']) ?>/">
    <meta property="og:image" content="<?= e($site['url']) ?>/Imagenes/frente.webp">
    <meta name="twitter:card" content="summary_large_image">

    <link rel="icon" href="favicon.ico" sizes="48x48">
    <link rel="icon" href="favicon-32.png" type="image/png" sizes="32x32">
    <link rel="apple-touch-icon" href="apple-touch-icon.png">
    <link rel="manifest" href="site.webmanifest">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/styles.css?v=2">
    <link rel="preload" as="image" href="Imagenes/mama-800.webp" imagesrcset="Imagenes/mama-800.webp 800w, Imagenes/mama.webp 1600w" imagesizes="100vw">

    <script type="application/ld+json">
    <?= json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'MedicalClinic',
        'name' => $site['name'],
        'url' => $site['url'],
        'image' => $site['url'] . '/Imagenes/frente.webp',
        'logo' => $site['url'] . '/Imagenes/logo/logo.png',
        'telephone' => $site['phone_e164'],
        'email' => $site['email'],
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => 'Av. Ida Koenig',
            'addressLocality' => 'Villa Mella, Santo Domingo Norte',
            'addressRegion' => 'Santo Domingo',
            'addressCountry' => 'DO',
        ],
        'openingHoursSpecification' => array_map(fn($h) => [
            '@type' => 'OpeningHoursSpecification',
            'dayOfWeek' => $h['days'], 'opens' => $h['opens'], 'closes' => $h['closes'],
        ], $site['hours']),
        'medicalSpecialty' => ['PrimaryCare', 'Cardiovascular', 'Neurologic', 'Dentistry', 'Radiography'],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?>
    </script>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased">

<a href="#contenido" class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-[100] focus:bg-white focus:px-4 focus:py-2 focus:rounded-lg focus:shadow-lg">Saltar al contenido</a>

<!-- ============ ENCABEZADO ============ -->
<header class="glass-header fixed inset-x-0 top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3 md:py-4 flex justify-between items-center gap-4">
        <a href="#inicio" class="flex items-center gap-3 min-w-0" aria-label="Ir al inicio">
            <img src="Imagenes/logo/logo-mark.webp" alt="Logo Fundacosi XXI" width="311" height="298" class="h-10 sm:h-11 w-auto flex-shrink-0">
            <span class="min-w-0">
                <span class="block text-[15px] sm:text-lg font-bold leading-tight tracking-tight font-serif">Unidad de Atención Primaria</span>
                <span class="block text-[11px] uppercase tracking-[0.2em] text-teal-700 font-semibold">Fundacosi XXI</span>
            </span>
        </a>

        <nav class="hidden lg:flex items-center gap-8" aria-label="Navegación principal">
            <a href="#inicio" class="nav-link text-sm font-medium">Inicio</a>
            <a href="#nosotros" class="nav-link text-sm font-medium">Nosotros</a>
            <a href="#servicios" class="nav-link text-sm font-medium">Servicios</a>
            <a href="#impacto" class="nav-link text-sm font-medium">Impacto</a>
            <a href="#noticias" class="nav-link text-sm font-medium">Noticias</a>
            <a href="#contacto" class="bg-slate-900 text-white px-6 py-2.5 rounded-full text-sm font-semibold hover:bg-teal-700 transition-colors">Contacto</a>
        </nav>

        <button id="menu-toggle" type="button" class="lg:hidden w-11 h-11 flex items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-900 flex-shrink-0"
                aria-label="Abrir menú" aria-expanded="false" aria-controls="mobile-menu">
            <i class="fa-solid fa-bars text-lg" aria-hidden="true"></i>
        </button>
    </div>

    <!-- Menú móvil -->
    <nav id="mobile-menu" class="lg:hidden hidden border-t border-slate-200 bg-white" aria-label="Navegación móvil">
        <div class="px-4 py-4 flex flex-col gap-1">
            <a href="#inicio" class="mobile-link">Inicio</a>
            <a href="#nosotros" class="mobile-link">Nosotros</a>
            <a href="#servicios" class="mobile-link">Servicios</a>
            <a href="#impacto" class="mobile-link">Impacto</a>
            <a href="#noticias" class="mobile-link">Noticias</a>
            <a href="#contacto" class="mobile-link">Contacto</a>
            <a href="<?= e(wa_link($site)) ?>" target="_blank" rel="noopener" class="mt-3 flex items-center justify-center gap-2 bg-emerald-600 text-white py-3 rounded-xl font-semibold">
                <i class="fa-brands fa-whatsapp text-lg" aria-hidden="true"></i> Escríbenos por WhatsApp
            </a>
        </div>
    </nav>
</header>

<main id="contenido">

<!-- ============ HERO / SLIDER ============ -->
<section id="inicio" class="relative min-h-[100svh] flex items-center justify-center overflow-hidden bg-slate-900" aria-roledescription="carrusel" aria-label="Presentación">
    <div class="absolute inset-0 z-0" id="slider-images">
        <?php foreach ($slides as $i => $s): ?>
        <img src="<?= e($s['img']) ?>-800.webp"
             srcset="<?= e($s['img']) ?>-800.webp 800w, <?= e($s['img']) ?>.webp 1600w" sizes="100vw"
             alt="<?= e($s['alt']) ?>"
             class="slide-img absolute inset-0 w-full h-full object-cover transition-opacity duration-1000 <?= $i === 0 ? 'opacity-40' : 'opacity-0' ?>"
             <?= $i === 0 ? 'fetchpriority="high"' : 'loading="lazy"' ?> decoding="async" width="1600" height="1000">
        <?php endforeach; ?>
        <div class="absolute inset-0 bg-gradient-to-r from-slate-900/90 via-slate-900/60 to-slate-900/80"></div>
    </div>

    <div class="relative z-10 max-w-5xl mx-auto px-5 sm:px-6 text-center text-white pt-24 pb-36 md:pb-32">
        <div id="slider-text" class="transition-opacity duration-500" aria-live="polite">
            <span id="slider-tag" class="inline-block px-4 py-1.5 rounded-full bg-teal-500/20 text-teal-300 text-[11px] sm:text-xs font-bold uppercase tracking-[0.2em] mb-5 sm:mb-6"><?= e($slides[0]['tag']) ?></span>
            <h1 id="slider-title" class="text-4xl sm:text-5xl md:text-7xl font-bold mb-5 sm:mb-6 leading-tight max-w-4xl mx-auto font-serif"><?= $slides[0]['title'] ?></h1>
            <p id="slider-desc" class="text-slate-300 text-base sm:text-lg md:text-xl max-w-2xl mx-auto mb-8 sm:mb-10 leading-relaxed"><?= e($slides[0]['desc']) ?></p>
        </div>
        <div class="flex flex-col sm:flex-row justify-center items-center gap-3 sm:gap-4">
            <a href="<?= e(wa_link($site)) ?>" target="_blank" rel="noopener" data-open-chat="Quiero agendar una cita" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-teal-600 hover:bg-teal-700 text-white px-8 py-4 rounded-full font-bold text-base transition-all shadow-lg shadow-teal-600/30">
                <i class="fa-solid fa-calendar-check text-lg" aria-hidden="true"></i> Agenda tu cita
            </a>
            <a href="#servicios" class="w-full sm:w-auto border border-white/30 bg-white/5 hover:bg-white/10 text-white px-8 py-4 rounded-full font-bold text-base transition-all backdrop-blur-sm">
                Nuestros servicios
            </a>
        </div>
    </div>

    <div class="absolute bottom-6 md:bottom-10 left-1/2 -translate-x-1/2 z-20 flex items-center gap-3 sm:gap-4">
        <button type="button" id="slide-prev" class="slider-btn hidden sm:flex" aria-label="Diapositiva anterior"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i></button>
        <div id="slider-dots" class="flex gap-2 sm:gap-3">
            <?php foreach ($slides as $i => $s): ?>
            <button type="button" class="slider-dot <?= $i === 0 ? 'is-active' : '' ?>" data-index="<?= $i ?>" aria-label="Ir a la diapositiva <?= $i + 1 ?>" <?= $i === 0 ? 'aria-current="true"' : '' ?>></button>
            <?php endforeach; ?>
        </div>
        <button type="button" id="slide-next" class="slider-btn hidden sm:flex" aria-label="Diapositiva siguiente"><i class="fa-solid fa-arrow-right" aria-hidden="true"></i></button>
        <button type="button" id="slide-pause" class="slider-btn" aria-label="Pausar presentación"><i class="fa-solid fa-pause" aria-hidden="true"></i></button>
    </div>
</section>

<!-- ============ NOSOTROS ============ -->
<section id="nosotros" class="py-20 md:py-32 px-4 sm:px-6 bg-white overflow-hidden relative">
    <div class="absolute top-0 left-0 w-96 h-96 bg-teal-100 rounded-full blur-3xl opacity-30 -translate-x-1/2 -translate-y-1/2" aria-hidden="true"></div>

    <div class="max-w-7xl mx-auto relative z-10">
        <div class="text-center mb-12 md:mb-20">
            <span class="eyebrow">Nuestra Institución</span>
            <h2 class="section-title">Comprometidos con la <span class="text-teal-600 italic">salud y el bienestar</span></h2>
            <p class="section-lead">Trabajamos cada día para brindar servicios médicos humanizados, accesibles y de calidad para toda la comunidad.</p>
        </div>

        <div class="grid lg:grid-cols-2 gap-12 md:gap-16 items-center mb-16 md:mb-20">
            <div class="relative">
                <img src="Imagenes/nosotros-800.webp" srcset="Imagenes/nosotros-800.webp 800w, Imagenes/nosotros.webp 1600w" sizes="(min-width:1024px) 50vw, 100vw"
                     class="rounded-3xl md:rounded-[40px] shadow-2xl relative z-10 w-full object-cover aspect-[16/10]" width="1600" height="984"
                     alt="Equipo directivo y médico frente a la unidad de atención primaria" loading="lazy" decoding="async">
                <div class="relative md:absolute mt-4 md:mt-0 md:-bottom-10 md:-right-6 bg-slate-900 text-white px-6 py-5 md:p-8 rounded-3xl shadow-2xl z-20 flex md:block items-center gap-4">
                    <p class="text-3xl md:text-4xl font-bold text-teal-400 md:mb-2 font-serif">16+</p>
                    <p class="text-slate-300 text-sm uppercase tracking-widest">Años de experiencia</p>
                </div>
            </div>

            <div class="space-y-6 md:space-y-8">
                <article class="bg-gradient-to-br from-teal-50 to-white border border-teal-100 rounded-3xl md:rounded-[32px] p-6 sm:p-10 shadow-xl shadow-slate-200/40">
                    <div class="flex flex-col sm:flex-row items-start gap-5 sm:gap-6">
                        <div class="w-14 h-14 rounded-2xl bg-teal-600 text-white flex items-center justify-center text-2xl shadow-lg shadow-teal-600/30 flex-shrink-0"><i class="fa-solid fa-bullseye" aria-hidden="true"></i></div>
                        <div>
                            <h3 class="text-2xl font-bold mb-3 font-serif">Misión</h3>
                            <p class="text-slate-600 leading-relaxed sm:text-lg">Contribuir al bienestar de la población ofreciendo servicios de salud accesibles, sostenibles y de calidad, con un trato humano, digno y comprometido con cada paciente.</p>
                        </div>
                    </div>
                </article>
                <article class="bg-gradient-to-br from-cyan-50 to-white border border-cyan-100 rounded-3xl md:rounded-[32px] p-6 sm:p-10 shadow-xl shadow-slate-200/40">
                    <div class="flex flex-col sm:flex-row items-start gap-5 sm:gap-6">
                        <div class="w-14 h-14 rounded-2xl bg-cyan-600 text-white flex items-center justify-center text-2xl shadow-lg shadow-cyan-600/30 flex-shrink-0"><i class="fa-solid fa-eye" aria-hidden="true"></i></div>
                        <div>
                            <h3 class="text-2xl font-bold mb-3 font-serif">Visión</h3>
                            <p class="text-slate-600 leading-relaxed sm:text-lg">Ser un referente nacional en atención integral de salud, reconocido por la excelencia médica, la innovación, la eficiencia y la humanización de nuestros servicios.</p>
                        </div>
                    </div>
                </article>
            </div>
        </div>

        <div class="bg-slate-900 rounded-3xl md:rounded-[40px] p-6 sm:p-10 md:p-16 text-white shadow-2xl overflow-hidden relative">
            <div class="absolute top-0 right-0 w-72 h-72 bg-teal-500/10 rounded-full blur-3xl" aria-hidden="true"></div>
            <div class="relative z-10">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 md:gap-6 mb-10 md:mb-12">
                    <div>
                        <p class="text-teal-400 uppercase tracking-[0.3em] text-xs font-bold">Principios Institucionales</p>
                        <h3 class="text-3xl md:text-5xl font-bold mt-3 font-serif">Nuestros valores</h3>
                    </div>
                    <p class="text-slate-400 max-w-xl leading-relaxed">Cada acción que realizamos está guiada por principios éticos y humanos que fortalecen la confianza y el bienestar de nuestros pacientes.</p>
                </div>
                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
                    <?php foreach ([
                        ['fa-handshake', 'teal', 'Respeto', 'Tratamos a cada persona con dignidad, empatía y consideración.'],
                        ['fa-shield-heart', 'cyan', 'Compromiso', 'Trabajamos con responsabilidad y entrega hacia nuestros pacientes.'],
                        ['fa-users', 'emerald', 'Solidaridad', 'Promovemos la inclusión y el apoyo a las comunidades vulnerables.'],
                        ['fa-star', 'rose', 'Excelencia', 'Buscamos calidad, innovación y mejora continua en cada servicio.'],
                    ] as [$icon, $c, $t, $d]): ?>
                    <div class="bg-white/5 border border-white/10 rounded-3xl p-6 transition-colors duration-300 hover:bg-white/10">
                        <i class="fa-solid <?= $icon ?> text-3xl mb-5 text-<?= $c ?>-400" aria-hidden="true"></i>
                        <h4 class="text-xl font-bold mb-2"><?= $t ?></h4>
                        <p class="text-slate-300 text-sm leading-relaxed"><?= $d ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============ SERVICIOS ============ -->
<section id="servicios" class="py-20 md:py-32 px-4 sm:px-6 bg-gradient-to-b from-slate-50 to-white overflow-hidden relative">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-12 md:mb-20">
            <span class="eyebrow">Atención Especializada</span>
            <h2 class="section-title">Servicios médicos de <span class="text-teal-600 italic">calidad</span></h2>
            <p class="section-lead">Atención integral con especialistas capacitados y un enfoque humano centrado en cada paciente.</p>
        </div>

        <div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-5 md:gap-8">
            <?php foreach ($services as $key => $s): $c = svc_classes($s['color']); ?>
            <article class="relative bg-white p-6 md:p-8 rounded-3xl md:rounded-[32px] shadow-xl shadow-slate-200/50 border border-slate-100 md:hover:-translate-y-2 hover:shadow-2xl transition-all duration-500 overflow-hidden flex flex-col">
                <div class="absolute top-0 right-0 w-40 h-40 <?= $c['blob'] ?> rounded-full blur-3xl opacity-40" aria-hidden="true"></div>
                <div class="relative z-10 flex flex-col h-full">
                    <div class="w-16 h-16 md:w-20 md:h-20 rounded-2xl md:rounded-3xl <?= $c['bg'] ?> text-white flex items-center justify-center text-3xl md:text-4xl mb-6 md:mb-8 shadow-lg <?= $c['shadow'] ?>">
                        <i class="fa-solid <?= e($s['icon']) ?>" aria-hidden="true"></i>
                    </div>
                    <h3 class="text-xl md:text-2xl font-bold mb-3 font-serif"><?= e($s['title']) ?></h3>
                    <p class="text-slate-500 leading-relaxed mb-6 md:mb-8"><?= e($s['short']) ?></p>
                    <button type="button" data-service="<?= e($key) ?>" class="open-service mt-auto w-full <?= $c['bg'] ?> <?= $c['hover'] ?> text-white py-3.5 md:py-4 rounded-2xl font-semibold transition-colors">
                        Más detalles<span class="sr-only"> sobre <?= e($s['title']) ?></span>
                    </button>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============ IMPACTO ============ -->
<section id="impacto" class="py-20 md:py-32 px-4 sm:px-6 bg-slate-900 text-white relative overflow-hidden">
    <div class="absolute bottom-0 right-0 w-96 h-96 bg-teal-500/10 rounded-full blur-3xl" aria-hidden="true"></div>
    <div class="max-w-7xl mx-auto relative">
        <div class="grid lg:grid-cols-2 gap-12 md:gap-16 items-center">
            <div>
                <span class="eyebrow-dark">Impacto Humano</span>
                <h2 class="text-3xl sm:text-5xl md:text-6xl font-bold mb-6 leading-tight font-serif">Resultados que <span class="text-teal-400 italic">transforman vidas</span></h2>
                <p class="text-slate-400 text-base sm:text-lg leading-relaxed mb-8">Damos seguimiento al impacto de nuestras jornadas y consultas para asegurar que la salud de calidad llegue a cada rincón de nuestra comunidad.</p>
                <!-- TODO: verificar estas cifras con datos reales antes de publicar -->
                <div class="grid grid-cols-2 gap-4 sm:gap-6">
                    <div class="bg-white/5 border border-white/10 rounded-2xl p-5 sm:p-6">
                        <p class="text-3xl font-bold text-teal-400 mb-1 font-serif">12K+</p>
                        <p class="text-slate-400 text-xs uppercase tracking-wider">Pacientes al año</p>
                    </div>
                    <div class="bg-white/5 border border-white/10 rounded-2xl p-5 sm:p-6">
                        <p class="text-3xl font-bold text-cyan-400 mb-1 font-serif">98%</p>
                        <p class="text-slate-400 text-xs uppercase tracking-wider">Satisfacción</p>
                    </div>
                </div>
            </div>
            <div class="bg-white/5 border border-white/10 rounded-3xl md:rounded-[32px] p-5 sm:p-8 shadow-2xl">
                <h3 class="text-lg sm:text-xl font-bold mb-6 text-center text-slate-200">Pacientes atendidos por trimestre</h3>
                <div class="relative h-56 sm:h-64">
                    <canvas id="impactChart" role="img" aria-label="Gráfico de pacientes atendidos por trimestre: T1 2,600; T2 3,100; T3 2,900; T4 3,400"></canvas>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============ NOTICIAS ============ -->
<section id="noticias" class="py-20 md:py-28 px-4 sm:px-6 bg-slate-950 text-white">
    <div class="max-w-7xl mx-auto">
        <div class="mb-10 md:mb-12">
            <span class="eyebrow-dark">Noticias</span>
            <h2 class="text-3xl md:text-4xl font-bold font-serif">Actividades y novedades</h2>
            <p class="text-slate-400 mt-2">Jornadas médicas, avances de nuestra unidad y consejos de salud.</p>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
            <?php foreach ([
                ['img' => 'Noticias/operativo-800.webp', 'alt' => 'Personal de salud atendiendo a madres y niños durante un operativo médico', 'tag' => 'Comunidad', 'tc' => 'bg-teal-600',
                 'date' => '2026-05-22', 'date_txt' => '22 de mayo de 2026', 'title' => 'Jornada de operativos médicos',
                 'text' => 'Realizamos un operativo integral enfocado en el despistaje cardiovascular y la atención pediátrica sin costo.'],
                // TODO: reemplazar por fotos reales de la unidad
                ['img' => 'https://images.unsplash.com/photo-1579165466741-7f35e4755660?q=75&w=800&auto=format&fit=crop', 'alt' => 'Equipo de diagnóstico médico', 'tag' => 'Equipamiento', 'tc' => 'bg-cyan-600',
                 'date' => '2026-05-15', 'date_txt' => '15 de mayo de 2026', 'title' => 'Modernización de equipos de radiología y sonografía',
                 'text' => 'Incorporamos nuevas unidades de ultrasonido de alta resolución para diagnósticos más rápidos y precisos.'],
                ['img' => 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?q=75&w=800&auto=format&fit=crop', 'alt' => 'Médico revisando información de salud', 'tag' => 'Salud', 'tc' => 'bg-rose-600',
                 'date' => '2026-05-08', 'date_txt' => '8 de mayo de 2026', 'title' => 'Prevención cardiovascular en el adulto mayor',
                 'text' => 'Compartimos nuestra guía de cuidado preventivo del corazón, disponible gratis en nuestra unidad.'],
            ] as $n): ?>
            <article class="bg-white/5 border border-white/10 rounded-3xl overflow-hidden">
                <div class="aspect-[16/9] overflow-hidden relative">
                    <img src="<?= e($n['img']) ?>" class="w-full h-full object-cover" alt="<?= e($n['alt']) ?>" loading="lazy" decoding="async" width="800" height="450">
                    <span class="absolute top-4 left-4 <?= $n['tc'] ?> text-white text-xs font-bold px-3 py-1 rounded-full"><?= e($n['tag']) ?></span>
                </div>
                <div class="p-6">
                    <time datetime="<?= $n['date'] ?>" class="text-xs text-slate-400 font-medium block mb-2"><?= e($n['date_txt']) ?></time>
                    <h3 class="text-lg sm:text-xl font-bold mb-3 text-slate-100"><?= e($n['title']) ?></h3>
                    <p class="text-slate-400 text-sm leading-relaxed"><?= e($n['text']) ?></p>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============ CONTACTO ============ -->
<section id="contacto" class="py-20 md:py-32 px-4 sm:px-6 bg-white relative">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-12 md:mb-20">
            <span class="eyebrow">Contacto</span>
            <h2 class="section-title">Estamos para <span class="text-teal-600 italic">atenderte</span></h2>
            <p class="section-lead">Escríbenos por WhatsApp para informarte sobre consultas y citas, o déjanos tu opinión para seguir mejorando.</p>
        </div>

        <div class="grid lg:grid-cols-12 gap-8 md:gap-12 items-start">
            <div class="lg:col-span-5 space-y-6 md:space-y-8">
                <div class="bg-slate-900 text-white rounded-3xl md:rounded-[32px] p-6 sm:p-8 shadow-xl">
                    <h3 class="text-2xl font-bold mb-6 font-serif">Información de contacto</h3>
                    <ul class="space-y-6">
                        <li class="flex gap-4 items-start">
                            <span class="contact-icon"><i class="fa-solid fa-location-dot" aria-hidden="true"></i></span>
                            <div><h4 class="font-bold text-slate-200">Ubicación</h4>
                                <a href="https://www.google.com/maps/search/?api=1&query=<?= rawurlencode($site['maps_query']) ?>" target="_blank" rel="noopener" class="text-slate-300 text-sm mt-1 block hover:text-teal-300 underline-offset-2 hover:underline"><?= e($site['address_short']) ?></a></div>
                        </li>
                        <li class="flex gap-4 items-start">
                            <span class="contact-icon"><i class="fa-solid fa-phone" aria-hidden="true"></i></span>
                            <div><h4 class="font-bold text-slate-200">Teléfono</h4>
                                <a href="tel:<?= e($site['phone_e164']) ?>" class="text-slate-300 text-sm mt-1 block hover:text-teal-300"><?= e($site['phone']) ?></a></div>
                        </li>
                        <li class="flex gap-4 items-start">
                            <span class="contact-icon"><i class="fa-solid fa-envelope" aria-hidden="true"></i></span>
                            <div><h4 class="font-bold text-slate-200">Correo</h4>
                                <a href="mailto:<?= e($site['email']) ?>" class="text-slate-300 text-sm mt-1 block hover:text-teal-300 break-all"><?= e($site['email']) ?></a></div>
                        </li>
                        <li class="flex gap-4 items-start">
                            <span class="contact-icon"><i class="fa-solid fa-clock" aria-hidden="true"></i></span>
                            <div><h4 class="font-bold text-slate-200">Horario de atención</h4>
                                <p class="text-slate-300 text-sm mt-1"><?php foreach ($site['hours'] as $h): ?><?= e($h['label']) ?>: <?= e($h['time']) ?><br><?php endforeach; ?></p></div>
                        </li>
                    </ul>
                </div>

                <div class="bg-emerald-50 border border-emerald-200 rounded-3xl md:rounded-[32px] p-6 sm:p-8 shadow-xl">
                    <div class="flex flex-col sm:flex-row items-center gap-5 sm:gap-6 text-center sm:text-left">
                        <div class="w-16 h-16 rounded-full bg-emerald-500 text-white flex items-center justify-center text-3xl shadow-lg shadow-emerald-500/30 flex-shrink-0"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i></div>
                        <div>
                            <h3 class="text-xl font-bold text-emerald-900 mb-1">Citas por WhatsApp</h3>
                            <p class="text-emerald-800 text-sm leading-relaxed mb-4">Coordina tu cita o resuelve tus dudas directamente con nuestro equipo.</p>
                            <a href="<?= e(wa_link($site)) ?>" target="_blank" rel="noopener" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-xl font-bold text-sm transition-colors shadow-md">
                                <i class="fa-brands fa-whatsapp text-lg" aria-hidden="true"></i> Chatear ahora
                            </a>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl overflow-hidden shadow-xl border border-slate-100 aspect-[4/3]">
                    <iframe title="Mapa de ubicación de la unidad" src="https://www.google.com/maps?q=<?= rawurlencode($site['maps_query']) ?>&output=embed"
                            class="w-full h-full border-0" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
                </div>
            </div>

            <!-- Formulario de quejas y sugerencias -->
            <div class="lg:col-span-7 bg-slate-50 border border-slate-100 rounded-3xl md:rounded-[40px] p-6 sm:p-8 md:p-10 shadow-xl">
                <h3 class="text-2xl font-bold mb-2 text-slate-800 font-serif">Quejas y sugerencias</h3>
                <p class="text-slate-500 text-sm mb-8">Ayúdanos a seguir mejorando. Cuéntanos cómo fue tu experiencia y en qué área fuiste atendido.</p>

                <form id="feedback-form" action="enviar.php" method="post" class="space-y-5 sm:space-y-6" novalidate>
                    <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
                    <!-- Campo trampa anti-spam: los humanos no lo ven -->
                    <div class="hidden" aria-hidden="true"><label for="website">No llenar</label><input type="text" id="website" name="website" tabindex="-1" autocomplete="off"></div>

                    <div class="grid sm:grid-cols-2 gap-5 sm:gap-6">
                        <div>
                            <label for="f-nombre" class="form-label">Nombre completo</label>
                            <input id="f-nombre" name="nombre" type="text" required maxlength="120" autocomplete="name" class="form-input">
                        </div>
                        <div>
                            <label for="f-email" class="form-label">Correo electrónico</label>
                            <input id="f-email" name="email" type="email" required maxlength="160" autocomplete="email" class="form-input">
                        </div>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-5 sm:gap-6">
                        <div>
                            <label for="f-telefono" class="form-label">Teléfono</label>
                            <input id="f-telefono" name="telefono" type="tel" required maxlength="20" autocomplete="tel" inputmode="tel" placeholder="809-000-0000" class="form-input">
                        </div>
                        <div>
                            <label for="f-area" class="form-label">Área donde fuiste atendido</label>
                            <select id="f-area" name="area" required class="form-input">
                                <option value="">Selecciona un área</option>
                                <?php foreach ($services as $s): ?><option value="<?= e($s['title']) ?>"><?= e($s['title']) ?></option><?php endforeach; ?>
                                <option value="Recepción / Atención al usuario">Recepción / Atención al usuario</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label for="f-tipo" class="form-label">Tipo de mensaje</label>
                        <select id="f-tipo" name="tipo" required class="form-input">
                            <option value="Queja">Queja</option>
                            <option value="Sugerencia">Sugerencia</option>
                            <option value="Felicitación">Felicitación</option>
                        </select>
                    </div>
                    <div>
                        <label for="f-mensaje" class="form-label">Cuéntanos en qué podemos mejorar</label>
                        <textarea id="f-mensaje" name="mensaje" rows="5" required minlength="10" maxlength="3000" class="form-input"></textarea>
                    </div>
                    <div class="flex items-start gap-3">
                        <input id="f-consent" name="consentimiento" type="checkbox" value="1" required class="mt-1 w-5 h-5 accent-teal-600 flex-shrink-0">
                        <label for="f-consent" class="text-sm text-slate-600">Acepto que mis datos sean usados únicamente para dar seguimiento a este mensaje, según la <a href="privacidad.php" class="text-teal-700 underline">política de privacidad</a>.</label>
                    </div>

                    <div id="form-status" class="hidden rounded-xl px-4 py-3 text-sm" role="status" aria-live="polite"></div>

                    <button type="submit" class="w-full bg-slate-900 hover:bg-teal-700 disabled:opacity-60 text-white py-4 rounded-xl font-bold transition-colors text-base shadow-lg shadow-slate-900/10">
                        Enviar mensaje
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

</main>

<!-- ============ PIE DE PÁGINA ============ -->
<footer class="bg-slate-950 text-slate-400 pt-14 pb-24 md:pb-10 px-4 sm:px-6 border-t border-white/5 text-sm">
    <div class="max-w-7xl mx-auto grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
        <div class="lg:col-span-2">
            <div class="flex items-center gap-4">
                <img src="Imagenes/logo/logo-256.webp" alt="Logo Unidad de Atención Primaria Fundacosi XXI" width="256" height="256" loading="lazy" class="w-20 h-20 rounded-full bg-white flex-shrink-0">
                <p class="text-white font-bold text-lg font-serif"><?= e($site['name']) ?></p>
            </div>
            <p class="mt-3 max-w-md leading-relaxed">Servicios de salud accesibles, humanos y de calidad para la comunidad de Villa Mella y Santo Domingo Norte.</p>
            <?php $socials = array_filter($site['social']); if ($socials): ?>
            <div class="flex gap-4 text-lg mt-5">
                <?php foreach ($socials as $net => $url): ?>
                <a href="<?= e($url) ?>" target="_blank" rel="noopener" class="hover:text-teal-400 transition-colors" aria-label="<?= e(ucfirst($net)) ?>"><i class="fa-brands fa-<?= e($net) ?>" aria-hidden="true"></i></a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
        <div>
            <p class="text-white font-semibold mb-3">Contacto</p>
            <ul class="space-y-2">
                <li><?= e($site['address_short']) ?></li>
                <li><a href="tel:<?= e($site['phone_e164']) ?>" class="hover:text-teal-400"><?= e($site['phone']) ?></a></li>
                <li><a href="mailto:<?= e($site['email']) ?>" class="hover:text-teal-400 break-all"><?= e($site['email']) ?></a></li>
            </ul>
        </div>
        <div>
            <p class="text-white font-semibold mb-3">Horario</p>
            <ul class="space-y-2">
                <?php foreach ($site['hours'] as $h): ?><li><?= e($h['label']) ?><br><?= e($h['time']) ?></li><?php endforeach; ?>
            </ul>
        </div>
    </div>
    <div class="max-w-7xl mx-auto mt-10 pt-6 border-t border-white/5 flex flex-col sm:flex-row justify-between gap-3">
        <p>&copy; <?= $year ?> <?= e($site['name']) ?>. Todos los derechos reservados.</p>
        <a href="privacidad.php" class="hover:text-teal-400">Política de privacidad</a>
    </div>
</footer>

<!-- ============ ASISTENTE VIRTUAL (CHATBOT) ============ -->
<button type="button" id="chat-launcher" aria-label="Abrir asistente virtual" aria-expanded="false" aria-controls="chat-panel"
        class="fixed bottom-5 right-5 z-[70] h-14 pl-4 pr-5 rounded-full bg-teal-600 hover:bg-teal-700 text-white flex items-center gap-2 shadow-xl shadow-teal-700/40 transition-colors">
    <i class="fa-solid fa-comment-medical text-2xl" aria-hidden="true"></i>
    <span class="font-semibold text-sm hidden sm:inline">¿Te ayudamos?</span>
    <span class="absolute -top-1 -right-1 w-3.5 h-3.5 rounded-full bg-emerald-400 ring-2 ring-white" aria-hidden="true"></span>
</button>

<section id="chat-panel" role="dialog" aria-modal="false" aria-labelledby="chat-title" hidden
         class="chat-panel fixed z-[80] inset-0 sm:inset-auto sm:bottom-24 sm:right-5 sm:w-[390px] sm:h-[620px] sm:max-h-[calc(100vh-8rem)] bg-white sm:rounded-3xl shadow-2xl flex-col overflow-hidden border border-slate-200">
    <header class="bg-slate-900 text-white px-4 py-3 flex items-center gap-3 flex-shrink-0">
        <span class="w-10 h-10 rounded-full bg-white flex items-center justify-center flex-shrink-0 p-1"><img src="Imagenes/logo/logo-mark.webp" alt="" width="311" height="298" class="w-full h-auto"></span>
        <div class="min-w-0 flex-1">
            <h2 id="chat-title" class="font-semibold text-sm leading-tight truncate"><?= e($chatbot['name']) ?></h2>
            <p class="text-xs text-teal-300 flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-400"></span> En línea · responde al instante</p>
        </div>
        <button type="button" id="chat-reset" class="w-9 h-9 rounded-full hover:bg-white/10 flex items-center justify-center" aria-label="Nueva conversación" title="Nueva conversación"><i class="fa-solid fa-rotate-right" aria-hidden="true"></i></button>
        <button type="button" id="chat-close" class="w-9 h-9 rounded-full hover:bg-white/10 flex items-center justify-center" aria-label="Cerrar asistente"><i class="fa-solid fa-xmark text-lg" aria-hidden="true"></i></button>
    </header>

    <div id="chat-messages" class="flex-1 overflow-y-auto px-4 py-4 space-y-3 bg-slate-50" aria-live="polite" aria-relevant="additions"></div>

    <div id="chat-chips" class="px-3 pt-2 pb-1 flex gap-2 overflow-x-auto flex-shrink-0 bg-white border-t border-slate-100">
        <button type="button" class="chat-chip" data-q="Quiero agendar una cita">📅 Agendar cita</button>
        <button type="button" class="chat-chip" data-q="¿Cuál es el horario de atención?">🕒 Horario</button>
        <button type="button" class="chat-chip" data-q="¿Qué servicios ofrecen?">🩺 Servicios</button>
        <button type="button" class="chat-chip" data-q="¿Qué doctores atienden en la unidad?">👩‍⚕️ Doctores</button>
        <button type="button" class="chat-chip" data-q="¿Dónde están ubicados?">📍 Ubicación</button>
    </div>

    <form id="chat-form" class="px-3 pb-2 pt-1 flex items-end gap-2 bg-white flex-shrink-0">
        <label for="chat-input" class="sr-only">Escribe tu pregunta</label>
        <textarea id="chat-input" rows="1" maxlength="<?= (int)$chatbot['max_input'] ?>" placeholder="Escribe tu pregunta…" autocomplete="off"
                  class="flex-1 resize-none max-h-28 bg-slate-100 rounded-2xl px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-teal-500/40"></textarea>
        <button type="submit" id="chat-send" class="w-12 h-12 rounded-full bg-teal-600 hover:bg-teal-700 disabled:opacity-50 text-white flex items-center justify-center flex-shrink-0" aria-label="Enviar">
            <i class="fa-solid fa-paper-plane" aria-hidden="true"></i>
        </button>
    </form>
    <footer class="px-4 pb-3 bg-white text-[11px] leading-snug text-slate-500 flex-shrink-0">
        Asistente con IA: puede equivocarse y no sustituye una consulta. Emergencias: <a href="tel:911" class="font-semibold text-red-600">9-1-1</a>.
        <a href="<?= e(wa_link($site, 'Hola, quisiera hablar con una persona del equipo.')) ?>" target="_blank" rel="noopener" class="inline-flex items-center gap-1 font-semibold text-emerald-700 hover:underline">
            <i class="fa-brands fa-whatsapp" aria-hidden="true"></i> Hablar con una persona
        </a>
    </footer>
</section>

<!-- Ventana de detalles del servicio -->
<div id="serviceModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden items-end sm:items-center justify-center z-[60] sm:p-6" role="dialog" aria-modal="true" aria-labelledby="modal-title">
    <div class="bg-white w-full sm:max-w-md max-h-[90vh] overflow-y-auto rounded-t-3xl sm:rounded-[28px] shadow-2xl relative">
        <button type="button" id="modal-close" class="absolute top-4 right-4 w-11 h-11 rounded-full bg-white/90 shadow-lg hover:bg-red-500 hover:text-white transition-colors z-20 text-lg flex items-center justify-center" aria-label="Cerrar">
            <i class="fa-solid fa-xmark" aria-hidden="true"></i>
        </button>
        <div id="modal-content"></div>
    </div>
</div>

<script>
    window.SITE = {
        slides: <?= json_encode(array_map(fn($s) => ['tag' => $s['tag'], 'title' => $s['title'], 'desc' => $s['desc']], $slides), JSON_UNESCAPED_UNICODE) ?>,
        services: <?= json_encode(array_map(fn($s) => $s + ['classes' => svc_classes($s['color'])], $services), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
        whatsapp: <?= json_encode('https://wa.me/' . $site['whatsapp']) ?>,
        csrf: <?= json_encode($csrf) ?>,
        chatbotName: <?= json_encode($chatbot['name'], JSON_UNESCAPED_UNICODE) ?>
    };
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>
<script src="assets/js/main.js?v=3" defer></script>
<script src="assets/js/chat.js?v=1" defer></script>
</body>
</html>
