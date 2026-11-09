<?php
/**
 * BASE DE CONOCIMIENTO DEL CHATBOT
 * ------------------------------------------------------------------
 * Todo lo que el asistente sabe sale de aquí y de includes/config.php
 * (servicios, horario, contacto). Si un dato no está, el bot dirá que
 * no lo tiene y ofrecerá llamar o escribir por WhatsApp.
 * Mantén esta información actualizada: el bot NO inventa datos.
 */

/* ===== DOCTORES =====
 * Los médicos, sus especialidades, quién atiende hoy/mañana y los seguros
 * se leen AUTOMÁTICAMENTE de webapi.fundacosixxi.com (webapi-app).
 * Esta lista solo se usa si la API no responde. Sirve también para
 * agregar datos que la API no tiene (por ejemplo, días fijos de consulta).
 *
 * Copia el bloque de ejemplo una vez por cada doctor y quita las barras //.
 * - 'especialidad' debe coincidir con un servicio de config.php
 *   (Medicina General, Cardiología, Neurología, Laboratorio Clínico,
 *    Odontología, Rayos X, Sonografía).
 * - 'horario': texto libre, por ejemplo 'Lunes y miércoles, 8:00 a. m. – 12:00 m.'
 * - 'notas': opcional (idiomas, si atiende niños, si requiere cita previa, etc.)
 */
$doctores = [
    // [
    //     'nombre'       => 'Dra. María Pérez',
    //     'especialidad' => 'Cardiología',
    //     'horario'      => 'Lunes y miércoles, 8:00 a. m. – 12:00 m.',
    //     'notas'        => 'Atiende solo con cita previa.',
    // ],
];

/* ===== PREGUNTAS FRECUENTES =====
 * Agrega o edita pares pregunta => respuesta. Deja fuera lo que no sepas con certeza.
 */
$faq = [
    '¿Cómo agendo una cita?' =>
        'Puedes pedirla aquí mismo en el chat: el asistente toma tus datos y nuestro personal te llama para confirmar el día y la hora. También puedes llamar o escribir por WhatsApp.',
    '¿Qué debo llevar a la consulta?' =>
        'Tu documento de identidad (cédula o pasaporte) y, si los tienes, estudios o recetas anteriores relacionados con tu consulta.',
    '¿Atienden emergencias?' =>
        'Ante una emergencia que ponga en riesgo la vida, llama al 9-1-1 o acude de inmediato a la emergencia más cercana.',
    // '¿Aceptan seguros médicos?' => 'Completar con la lista de ARS aceptadas.',
    // '¿Cuánto cuesta una consulta?' => 'Completar con precios o indicar que se informan por teléfono.',
    // '¿Los resultados de laboratorio se entregan en línea?' => 'Completar.',
];

/* ===== INFORMACIÓN ADICIONAL =====
 * Texto libre con cualquier otro dato útil (parqueo, accesibilidad, operativos próximos...).
 */
$info_extra = <<<TXT
- La unidad está en Villa Mella, Santo Domingo Norte.
- Realizamos jornadas y operativos médicos comunitarios periódicamente; las fechas se anuncian en la sección de noticias del sitio.
TXT;
