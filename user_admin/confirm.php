<?php
require_once dirname(__DIR__) . '/classes/DatabaseSessionManager.php';
require_once dirname(__DIR__) . '/classes/EmailTemplate.php';
$manager = new DatabaseSessionManager();
// $manager->startSession();
// session_start();
$conn = $manager->getDB();

require_once dirname(__DIR__) . '/google_services/google_client.php';
require_once dirname(__DIR__) . '/google_services/calendar_service.php';
include 'send_email.php';
include '../db/db_functions.php';


$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'];

try {
    // Iniciar una transacción
    $conn->beginTransaction();

    // Obtener la cita desde la base de datos
    $appointment = getAppointment($conn, $id);

    if (!$appointment) {
        throw new Exception('Cita no encontrada.');
    }

    // Formatear las fechas para Google Calendar
    // list($startDateTimeFormatted, $endDateTimeFormatted) = formatDateTime($appointment['date'], $appointment['start_time'], $appointment['end_time']);


    // Configurar cliente de Google
    $client = getClient();

    $timeZone = getUserTimeZone($client);
    list($startDateTimeFormatted, $endDateTimeFormatted) = formatDateTime($appointment['date'], $appointment['start_time'], $appointment['end_time'], $timeZone);
    createCalendarEvent($client, $appointment['name'], $appointment['service'], $startDateTimeFormatted, $endDateTimeFormatted, $appointment['id'], $conn);

    $emailTemplateBuilder = new EmailTemplate();
    $emailContent = $emailTemplateBuilder->buildEmail($appointment['company_id'], 'Confirmación', $appointment['id_service'], $appointment['name'], $appointment['date'], $appointment['start_time']);
    // Enviar confirmación por correo electrónico
    sendEmail($appointment['mail'], $emailContent, 'Confirmación');

    // Marcar la cita como confirmada en la base de datos
    confirmAppointment($conn, $id);

    // Confirmar la transacción
    $conn->commit();

    echo json_encode(['message' => 'Cita reservada y confirmación enviada exitosamente!', 'success' => true]);
} catch (Exception $e) {
    // Revertir la transacción en caso de error
    $conn->rollBack();
    echo json_encode(['message' => 'Error al confirmar la cita: ' . $e->getMessage()]);
    http_response_code(500);
} finally {
    $conn = null;
}
