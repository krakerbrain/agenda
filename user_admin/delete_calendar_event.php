<?php
require_once dirname(__DIR__) . '/google_services/google_client.php';
require_once dirname(__DIR__) . '/classes/DatabaseSessionManager.php';
$manager = new DatabaseSessionManager();
// $manager->startSession();
// session_start();
$conn = $manager->getDB();

$client = getClient();
$data = json_decode(file_get_contents('php://input'), true);
$appointmentId = $data['appointmentID'];
$eventId = isset($data['calendarEventID']) ? $data['calendarEventID'] : "";

deleteCalendarEvent($conn, $client, $eventId, $appointmentId);
function deleteCalendarEvent($conn, $client, $eventId, $appointmentId)
{
    $calendarService = new Google_Service_Calendar($client);
    $calendarId = 'primary'; // ID de tu calendario

    try {
        if ($eventId != "") {
            $calendarService = new Google_Service_Calendar($client);
            $calendarId = 'primary'; // ID de tu calendario
            $calendarService->events->delete($calendarId, $eventId);
        }
        // Actualizar el estado de la cita en la base de datos
        $sql = $conn->prepare("DELETE FROM appointments WHERE id = :appointment_id");
        $sql->bindParam(':appointment_id', $appointmentId, PDO::PARAM_INT);
        $sql->execute();

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        throw new Exception('Failed to delete event: ' . $e->getMessage());
    }
}