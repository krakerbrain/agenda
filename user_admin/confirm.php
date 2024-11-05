<?php
require_once dirname(__DIR__) . '/classes/EmailTemplate.php';
require_once dirname(__DIR__) . '/classes/Appointments.php';
require_once dirname(__DIR__) . '/google_services/google_client.php';
require_once dirname(__DIR__) . '/google_services/calendar_service.php';

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

    list($startDateTimeFormatted, $endDateTimeFormatted) = formatDateTime(
        $appointment['date'],
        $appointment['start_time'],
        $appointment['end_time'],
        $timeZone
    );

    // Crear evento en Google Calendar
    $eventId = createCalendarEvent(
        $client,
        $appointment['name'],
        $appointment['service'],
        $startDateTimeFormatted,
        $endDateTimeFormatted,
        $appointment['id']
    );

    // Actualizar el ID del evento en la base de datos
    // $appointments->update_event($eventId, $appointment['id']);
    $appointments->updateAppointment($id, 1, $eventId);

    // Confirmar la transacción
    $appointments->endTransaction();
    echo json_encode(['message' => 'Cita confirmada exitosamente y evento creado en Google Calendar.', 'success' => true]);
    http_response_code(200);
} catch (Exception $e) {
    // Revertir la transacción en caso de error
    $appointments->cancelTransaction();
    echo json_encode(['message' => 'Error al confirmar la cita: ' . $e->getMessage()]);
    http_response_code(500);
} finally {
    // Cerrar la conexión
    $appointments = null;
}
