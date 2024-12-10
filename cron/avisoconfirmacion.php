<?php
require_once dirname(__DIR__) . '/configs/init.php';
require_once dirname(__DIR__) . '/classes/Appointments.php';
require_once dirname(__DIR__) . '/classes/EmailTemplate.php';
require_once dirname(__DIR__) . '/user_admin/send_wsp.php';

$appointments = new Appointments();
$emailTemplateBuilder = new EmailTemplate();

try {
    error_log("INFO: Inicio notificación reserva " . date('Y-m-d H:i:s') . PHP_EOL, 3, __DIR__ . '/log/avisoconfirmacion.log');

    // Obtener citas que ya están confirmadas pero aún no se han enviado mensajes
    $confirmedAppointments = $appointments->getUnconfirmedAppointment();

    foreach ($confirmedAppointments as $appointment) {

        $emailContent = $emailTemplateBuilder->buildEmail($appointment, 'confirmacion');

        // Enviar mensaje de WhatsApp
        $wspStatusCode = sendWspReserva(
            "confirmar_reserva",
            $appointment['phone'],
            $appointment['name'],
            $appointment['date'],
            $appointment['start_time'],
            $emailContent['company_name'],
            $appointment['appointment_token'],
            $appointment['service_name']
        );

        //para pruebas
        // $wspStatusCode = 200;
        // Si los mensajes se envían correctamente, actualizar el estado de notificación
        if ($wspStatusCode == 200 || $wspStatusCode == 201) {
            $appointments->markAsConfirmed($appointment['id'], 'confirmacion');
            error_log("INFO: Mensaje enviado para cita ID " . $appointment['id'] . PHP_EOL, 3, __DIR__ . '/log/avisoconfirmacion.log');
        } else {
            error_log("ERROR: Fallo al enviar mensaje para cita ID " . $appointment['id'] . PHP_EOL, 3, __DIR__ . '/log/avisoconfirmacion.log');
        }
    }
} catch (Exception $e) {
    error_log("ERROR: " . $e->getMessage() . PHP_EOL, 3, __DIR__ . '/log/avisoconfirmacion.log');
} finally {
    // Liberar la instancia de Appointments y EmailTemplate
    $appointments = null;
    $emailTemplateBuilder = null;
}
