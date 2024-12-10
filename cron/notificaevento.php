<?php
require_once dirname(__DIR__) . '/configs/init.php';
require_once dirname(__DIR__) . '/classes/UniqueEvents.php';
require_once dirname(__DIR__) . '/classes/EmailTemplate.php';
require_once dirname(__DIR__) . '/user_admin/send_wsp.php';

// Crear instancia de las clases necesarias
$events = new UniqueEvents();
$emailTemplateBuilder = new EmailTemplate();

try {
    error_log("INFO: Inicio del cron de notificaciones de eventos " . date('Y-m-d H:i:s') . PHP_EOL, 3,  __DIR__ . '/log/avisoreserva.log');

    // Tipos de notificaciones: 'reserva' y 'confirmacion'
    $notificationTypes = ['reserva', 'confirmacion'];

    foreach ($notificationTypes as $type) {
        // Obtener eventos pendientes según el tipo
        $unconfirmedEvents = $events->getUnconfirmedEvent($type);

        foreach ($unconfirmedEvents as $event) {
            // Preparar el contenido del correo según el tipo
            $emailContent = $type === 'reserva'
                ? $emailTemplateBuilder->buildEventMail($event, 'reserva')
                : $emailTemplateBuilder->buildEventMail($event, 'confirmacion');

            $wspStatusCode = sendWspReserva(
                "registro_reserva",
                $event['phone'],
                $event['participant_name'],
                $event['date'],
                $event['start_time'],
                $emailContent['company_name'],
                $event['event_token'],
                $event['event_name'],
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
