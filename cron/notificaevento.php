<?php
require_once dirname(__DIR__) . '/configs/init.php';
require_once dirname(__DIR__) . '/classes/UniqueEvents.php';
require_once dirname(__DIR__) . '/classes/EmailTemplate.php';
require_once dirname(__DIR__) . '/user_admin/send_wsp.php';

// Crear instancia de las clases necesarias
$events = new UniqueEvents();
$emailTemplateBuilder = new EmailTemplate();
date_default_timezone_set('UTC');
try {
    error_log("INFO: Inicio del cron de notificaciones de eventos " . date('Y-m-d H:i:s') . PHP_EOL, 3,  __DIR__ . '/log/avisoreserva.log');

    // Tipos de notificaciones: 'reserva' y 'confirmacion'
    $notificationTypes = ['reserva', 'confirmacion'];

    foreach ($notificationTypes as $type) {
        // Obtener eventos pendientes según el tipo
        $unconfirmedEvents = $events->getUnconfirmedEvent($type);

        foreach ($unconfirmedEvents as $event) {
            // Preparar el contenido del correo según el tipo
            $emailContent = $emailTemplateBuilder->buildEventMail($event, $type);

            $templateName = $type === 'reserva'
                ? 'registro_reserva'
                : 'confirmar_reserva';

            $wspStatusCode = sendWspReserva(
                $templateName,
                $event['phone'],
                $event['participant_name'],
                formatearFecha($appointment['date']),
                $event['start_time'],
                $emailContent['company_name'],
                $event['event_token'],
                ucwords($event['event_name']),
            );
            //para pruebas
            $wspStatusCode = 200;

            if ($wspStatusCode == 200 || $wspStatusCode == 201) {
                // Marcar como notificado
                $events->markAsNotified($event['inscription_id'], $type);
            } else {
                error_log("ERROR: Fallo al enviar notificación ($type) para el evento ID: " . $event['inscription_id'] . " Código de estado: " . $wspStatusCode . PHP_EOL, 3, __DIR__ . '/log/avisoreserva.log');
            }
        }
    }
} catch (Exception $e) {
    error_log("ERROR: Error en el cron de notificaciones: " . $e->getMessage(), 3, __DIR__ . '/log/avisoreserva.log');
} finally {
    error_log("INFO: Cron finalizado " . date('Y-m-d H:i:s') . PHP_EOL, 3,  __DIR__ . '/log/avisoreserva.log');
    $events = null;
    $emailTemplateBuilder = null;
}
function formatearFecha($fecha)
{
    // Definir los días de la semana y los meses en español
    $dias = [
        'domingo',
        'lunes',
        'martes',
        'miércoles',
        'jueves',
        'viernes',
        'sábado',
    ];

    $meses = [
        'enero',
        'febrero',
        'marzo',
        'abril',
        'mayo',
        'junio',
        'julio',
        'agosto',
        'septiembre',
        'octubre',
        'noviembre',
        'diciembre'
    ];

    // Convertir la fecha en formato 'Y-m-d' a un objeto DateTime
    $fecha_obj = new DateTime($fecha);

    // Obtener el día de la semana (0=domingo, 1=lunes, ..., 6=sábado)
    $dia_semana = $fecha_obj->format('w');

    // Obtener el día, mes y año de la fecha
    $dia = $fecha_obj->format('d');
    $mes = $fecha_obj->format('m'); // Mes en formato numérico (01-12)
    $anio = $fecha_obj->format('Y');

    // Construir la fecha en el formato deseado: "viernes, 18 de abril de 2025"
    $fecha_formateada = ucfirst($dias[$dia_semana]) . ', ' . $dia . ' de ' . $meses[$mes - 1] . ' de ' . $anio;

    return $fecha_formateada;
}
