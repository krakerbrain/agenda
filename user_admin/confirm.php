<?php

require_once dirname(__DIR__) . '/classes/EmailTemplate.php';
require_once dirname(__DIR__) . '/classes/Appointments.php';
require_once dirname(__DIR__) . '/google_services/google_client.php';
require_once dirname(__DIR__) . '/google_services/calendar_service.php';
include 'send_wsp.php';

$appointments = new Appointments();
$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'];

try {
    // Iniciar una transacción
    $appointments->beginTransaction();
    // Obtener la cita desde la base de datos
    $appointment = $appointments->get_appointment($id);

    if (!$appointment) {
        throw new Exception('Cita no encontrada.');
    }

    // Configurar cliente de Google
    $client = getClient();

    $timeZone = getUserTimeZone($client);
    list($startDateTimeFormatted, $endDateTimeFormatted) = formatDateTime($appointment['date'], $appointment['start_time'], $appointment['end_time'], $timeZone);
    $eventId = createCalendarEvent($client, $appointment['name'], $appointment['service'], $startDateTimeFormatted, $endDateTimeFormatted, $appointment['id']);

    $appointments->update_event($eventId, $appointment['id']);

    // Construir el contenido del correo
    $confirmData = [
        'company_id'    => $appointment['company_id'],
        'name'          => $appointment['name'],
        'id_service'    => $appointment['id_service'],
        'date'          => $appointment['date'],
        'start_time'    => $appointment['start_time'],
        'mail'          => $appointment['mail']
    ];

    $emailTemplateBuilder = new EmailTemplate();
    $emailContent = $emailTemplateBuilder->buildEmail($confirmData, 'confirmacion');

    // Enviar mensaje de WhatsApp
    $wspStatusCode = sendWspReserva("confirmar_reserva", $appointment['phone'], $appointment['name'], $appointment['date'], $appointment['start_time'], $emailContent['company_name'], $appointment['appointment_token']);
    // Para pruebas locales
    // $wspStatusCode = 200;
    // Verificar si el mensaje de WhatsApp fue enviado correctamente
    if ($wspStatusCode == 200 || $wspStatusCode == 201) {
        // Confirmar la cita
        $row_count = $appointments->update_appointment($id);
        if ($row_count > 0) {
            // Confirmar la transacción
            $appointments->endTransaction();
            echo json_encode(['message' => 'Cita reservada y confirmación enviada exitosamente!', 'success' => true]);
            http_response_code(200);
        } else {
            throw new Exception('Error al actualizar la cita.');
        }
    } else {
        // Si falla el envío de WhatsApp, revertir la transacción
        throw new Exception('Error al enviar el mensaje de WhatsApp. Código de estado: ' . $wspStatusCode);
    }
} catch (Exception $e) {
    // Revertir la transacción en caso de error
    $appointments->cancelTransaction();
    echo json_encode(['message' => 'Error al confirmar la cita: ' . $e->getMessage()]);
    http_response_code(500);
} finally {
    // Cerrar la conexión
    $appointments = null;
}
