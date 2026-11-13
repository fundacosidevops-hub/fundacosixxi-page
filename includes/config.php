<?php
/**
 * Configuración central del sitio.
 * Edita aquí los datos de contacto, redes y servicios: se reflejan en toda la página.
 */

$site = [
    'name'        => 'Unidad de Atención Primaria Fundacosi XXI',
    'short_name'  => 'Fundacosi XXI',
    'url'         => 'https://fundacosixxi.com',
    'description' => 'Unidad de Atención Primaria Fundacosi XXI en Villa Mella, Santo Domingo Norte. Medicina general, cardiología, neurología, laboratorio, odontología, rayos X y sonografía.',

    // Contacto
    'address'      => 'Av. Ida Koenig, Villa Mella, Santo Domingo Norte, República Dominicana',
    'address_short'=> 'Av. Ida Koenig, Villa Mella, Santo Domingo Norte',
    'phone'        => '809-701-4000',
    'phone_e164'   => '+18097014000',
    'email'        => 'info@fundacosixxi.com',
    'whatsapp'     => '18093430034',   // solo dígitos, con código de país
    'whatsapp_msg' => 'Hola, deseo solicitar información para agendar una cita médica.',
    'maps_query'   => 'Av. Ida Koenig, Villa Mella, Santo Domingo Norte',

    // Horario (texto y formato schema.org)
    'hours' => [
        ['label' => 'Lunes a viernes', 'time' => '6:00 a. m. – 5:00 p. m.', 'days' => ['Monday','Tuesday','Wednesday','Thursday','Friday'], 'opens' => '06:00', 'closes' => '17:00'],
        ['label' => 'Sábados',         'time' => '8:00 a. m. – 12:00 p. m.', 'days' => ['Saturday'], 'opens' => '08:00', 'closes' => '12:00'],
    ],

    // Redes sociales: deja la URL vacía ('') para ocultar el icono.
    'social' => [
        'facebook'  => '',
        'instagram' => '',
        'linkedin'  => '',
    ],

    // Correo que recibe las quejas y sugerencias del formulario
    'form_to' => 'info@fundacosixxi.com',
];

// Asistente virtual (chatbot con IA)
$chatbot = [
    'enabled'      => true,
    'name'         => 'Asistente virtual Fundacosi',

    // ===== PROVEEDOR DE IA: cambia esta línea para elegir =====
    //   'openai'    → ChatGPT (OpenAI)      clave: 'openai_api_key' en secrets.php
    //   'anthropic' → Claude (Anthropic)    clave: 'anthropic_api_key' en secrets.php
    'provider'     => 'openai',

    'providers' => [
        'openai' => [
            'api_base' => 'https://api.openai.com/v1',
            'model'    => 'gpt-6-luna',      // modelos: https://developers.openai.com/api/docs/models
            'reasoning_effort' => 'none',    // necesario para usar herramientas en Chat Completions
            'max_tokens' => 600,
        ],
        'anthropic' => [
            'api_base' => 'https://api.anthropic.com/v1',
            'model'    => 'claude-haiku-5-5', // modelos: https://platform.claude.com/docs/en/about-claude/models/overview
            'version'  => '2026-10-21',       // cabecera anthropic-version
            'effort'   => 'low',              // low = más rápido y económico
            'max_tokens' => 1024,
        ],
    ],

    'timezone'     => 'America/Santo_Domingo',
    // Turnos: los médicos que empiezan antes de esta hora son del turno de la MAÑANA;
    // desde esta hora, del turno de la TARDE. Desde esta hora, hoy solo se mencionan los de la tarde.
    'corte_tarde'  => '11:00',
    'max_history'  => 16,   // mensajes recordados por conversación
    'max_input'    => 600,  // caracteres por mensaje del paciente
    'limit_session_hour' => 40,  // mensajes por visitante por hora
    'limit_ip_hour'      => 120, // mensajes por IP por hora
    'max_citas_session'  => 3,   // solicitudes de cita por conversación
];

// Conexión con webapi.fundacosixxi.com (proyecto webapi-app).
// La clave va en includes/secrets.php ('webapi_key') y debe estar en WEB_CHAT_KEYS del .env de la API.
$webapi = [
    'enabled'        => true,
    'base_url'       => 'https://webapi.fundacosixxi.com/api/v1',
    'timeout'        => 8,    // segundos
    'cache_info'     => 600,  // servicios, médicos y seguros: 10 minutos
    'cache_schedule' => 60,   // médicos del día y cupos: 1 minuto
];

$services = [
    'general' => [
        'title' => 'Medicina General', 'icon' => 'fa-user-doctor', 'color' => 'rose',
        'short' => 'Consultas integrales y atención familiar.',
        'desc'  => 'Consultas médicas generales enfocadas en la prevención y el bienestar familiar.',
        'items' => ['Consultas médicas', 'Chequeos generales', 'Atención familiar'],
        'image' => 'https://images.unsplash.com/photo-1584515933487-779824d29309?q=75&w=800&auto=format&fit=crop',
    ],
    'cardio' => [
        'title' => 'Cardiología', 'icon' => 'fa-heart-pulse', 'color' => 'teal',
        'short' => 'Diagnóstico y tratamiento cardiovascular.',
        'desc'  => 'Diagnóstico y atención especializada para enfermedades cardiovasculares.',
        'items' => ['Electrocardiogramas', 'Evaluación cardiovascular', 'Monitoreo cardíaco'],
        'image' => 'https://images.unsplash.com/photo-1628348070889-cb656235b4eb?q=75&w=800&auto=format&fit=crop',
    ],
    'neuro' => [
        'title' => 'Neurología', 'icon' => 'fa-brain', 'color' => 'cyan',
        'short' => 'Atención especializada del sistema nervioso.',
        'desc'  => 'Atención neurológica para enfermedades del sistema nervioso central y periférico.',
        'items' => ['Evaluación neurológica', 'Control médico', 'Atención integral'],
        'image' => 'https://images.unsplash.com/photo-1559757148-5c350d0d3c56?q=75&w=800&auto=format&fit=crop',
    ],
    'lab' => [
        'title' => 'Laboratorio Clínico', 'icon' => 'fa-microscope', 'color' => 'emerald',
        'short' => 'Análisis clínicos confiables.',
        'desc'  => 'Laboratorio para análisis clínicos rápidos, seguros y confiables.',
        'items' => ['Análisis clínicos', 'Pruebas especiales', 'Resultados digitales'],
        'image' => 'https://images.unsplash.com/photo-1579165466741-7f35e4755660?q=75&w=800&auto=format&fit=crop',
    ],
    'odonto' => [
        'title' => 'Odontología', 'icon' => 'fa-tooth', 'color' => 'sky',
        'short' => 'Salud bucal preventiva para toda la familia.',
        'desc'  => 'Servicios odontológicos para el cuidado integral de la salud bucal de niños y adultos.',
        'items' => ['Limpieza dental', 'Extracciones', 'Evaluación bucal', 'Tratamientos preventivos'],
        'image' => 'https://images.unsplash.com/photo-1606811841689-23dfddce3e95?q=75&w=800&auto=format&fit=crop',
    ],
    'rayosx' => [
        'title' => 'Rayos X', 'icon' => 'fa-x-ray', 'color' => 'indigo',
        'short' => 'Radiografías digitales con resultados precisos.',
        'desc'  => 'Imágenes diagnósticas digitales para evaluaciones rápidas, seguras y precisas.',
        'items' => ['Radiografías digitales', 'Diagnóstico por imágenes', 'Evaluación ósea'],
        'image' => 'https://images.unsplash.com/photo-1581595219315-a187dd40c322?q=75&w=800&auto=format&fit=crop',
    ],
    'sono' => [
        'title' => 'Sonografía', 'icon' => 'fa-wave-square', 'color' => 'purple',
        'short' => 'Ultrasonidos no invasivos y seguros.',
        'desc'  => 'Evaluaciones ultrasonográficas de alta resolución para visualizar estructuras internas del cuerpo.',
        'items' => ['Sonografía abdominal', 'Pélvica y obstétrica', 'Partes blandas'],
        'image' => 'https://images.unsplash.com/photo-1516549655169-df83a0774514?q=75&w=800&auto=format&fit=crop',
    ],
];

function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function wa_link(array $site, string $msg = null): string {
    return 'https://wa.me/' . $site['whatsapp'] . '?text=' . rawurlencode($msg ?? $site['whatsapp_msg']);
}
