<?php
require_once dirname(__DIR__) . '/configs/init.php';
require_once dirname(__DIR__) . '/classes/Appointments.php';
require_once dirname(__DIR__) . '/classes/EmailTemplate.php';
require_once dirname(__DIR__) . '/user_admin/send_wsp.php';

// Crear instancia de la clase Appointments
$appointments = new Appointments();
// Crear una única instancia de EmailTemplate
$emailTemplateBuilder = new EmailTemplate();

try {
    error_log("INFO: Inicio notificación confirmación " . date('Y-m-d H:i:s') . PHP_EOL, 3,  __DIR__ . '/log/avisoreserva.log');
    // Obtener citas no confirmadas
    $unconfirmedReservas = $appointments->getUnconfirmedReserva();

    foreach ($unconfirmedReservas as $appointment) {
        // Preparar datos para el envío de correo y WhatsApp
        $emailContent = $emailTemplateBuilder->buildEmail($appointment, 'reserva');

        $wspStatusCode = sendWspReserva(
            "registro_reserva",
            $appointment['phone'],
            $appointment['name'],
            $appointment['date'],
            $appointment['start_time'],
            $emailContent['company_name'],
            $appointment['appointment_token'],
            $appointment['service_name'],
        );
        //para pruebas
        $wspStatusCode = 200;
        // Enviar notificación
        if ($wspStatusCode == 200 || $wspStatusCode == 201) {
            // Marcar como confirmada
            $appointments->markAsConfirmed($appointment['id']);
        } else {
            error_log("ERROR: Error al enviar el mensaje de WhatsApp para la cita ID: " . $appointment['id'] . " Código de estado: " . $wspStatusCode . PHP_EOL, 3, __DIR__ . '/log/avisoreserva.log');
        }
    }
} catch (Exception $e) {
    error_log("ERROR: Error en el cron job de envío de notificaciones: " . $e->getMessage(), 3, __DIR__ . '/log/avisoreserva.log');
} finally {
    error_log("INFO: Tarea finalizada en el cron job " . date('Y-m-d H:i:s') . PHP_EOL, 3,  __DIR__ . '/log/avisoreserva.log');
    // Liberar la instancia de Appointments y EmailTemplate
    $appointments = null;
    $emailTemplateBuilder = null;
}
