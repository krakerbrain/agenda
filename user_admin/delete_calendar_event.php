<?php
require_once dirname(__DIR__) . '/google_services/google_client.php';
require_once dirname(__DIR__) . '/classes/Appointments.php';

session_start();

$client = getClient();
$data = json_decode(file_get_contents('php://input'), true);
$appointmentId = $data['appointmentID'];
$eventId = isset($data['calendarEventID']) ? $data['calendarEventID'] : "";

deleteCalendarEvent($client, $eventId, $appointmentId);
function deleteCalendarEvent($client, $eventId, $appointmentId)
{
    $calendarService = new Google_Service_Calendar($client);
    $calendarId = 'primary'; // ID de tu calendario

    $appointment = new Appointments();
    try {
        if ($eventId != "") {
            $calendarService = new Google_Service_Calendar($client);
            $calendarId = 'primary'; // ID de tu calendario
            $calendarService->events->delete($calendarId, $eventId);
        }

        $deletedRows = $appointment->delete_appointment($appointmentId);
        if ($deletedRows > 0) {
            echo json_encode(['success' => true, 'message' => 'Evento eliminado exitosamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se encontró la cita para eliminar']);
        }
    } catch (Exception $e) {
        throw new Exception('Failed to delete event: ' . $e->getMessage());
    }
}
