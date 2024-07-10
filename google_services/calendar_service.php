<?php

require_once dirname(__DIR__) . '/classes/DatabaseSessionManager.php';
$manager = new DatabaseSessionManager();
$manager->startSession();
function createCalendarEvent($client, $name, $service, $startDateTimeFormatted, $endDateTimeFormatted, $appointmentId, $conn)
{
    // Establecer el token de acceso del usuario autenticado
    if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
        $client->setAccessToken($_SESSION['access_token']);
    }

    // Verificar si el token de acceso está expirado y renovarlo si es necesario
    if ($client->isAccessTokenExpired()) {
        if (isset($_SESSION['refresh_token'])) {
            $client->fetchAccessTokenWithRefreshToken($_SESSION['refresh_token']);
            $_SESSION['access_token'] = $client->getAccessToken();
        } else {
            throw new Exception('El token de acceso ha expirado y no hay un token de actualización disponible.');
        }
    }

    $calendarService = new Google_Service_Calendar($client);

    $event = new Google_Service_Calendar_Event(array(
        'summary' => $service . " con " . $name,
        'start' => array(
            'dateTime' => $startDateTimeFormatted,
            'timeZone' => 'America/Santiago',
        ),
        'end' => array(
            'dateTime' => $endDateTimeFormatted,
            'timeZone' => 'America/Santiago',
        )
    ));

    try {
        // Obtén el ID del calendario principal del usuario autenticado
        $calendarId = 'primary';

        // Crear el evento en el calendario del usuario
        $event = $calendarService->events->insert($calendarId, $event);
        $eventId = $event->getId();

        // Guardar el ID del evento en la tabla appointments
        $sql = $conn->prepare("UPDATE appointments SET event_id = :event_id WHERE id = :appointment_id");
        $sql->bindParam(':event_id', $eventId);
        $sql->bindParam(':appointment_id', $appointmentId);
        $sql->execute();
    } catch (Exception $e) {
        throw new Exception('Failed to create event: ' . $e->getMessage());
    }
}


function formatDateTime($date, $startTime, $endTime)
{
    $startDateTime = new DateTime("$date $startTime", new DateTimeZone('America/Santiago'));
    $endDateTime = new DateTime("$date $endTime", new DateTimeZone('America/Santiago'));

    $startDateTimeFormatted = $startDateTime->format('Y-m-d\TH:i:sP');
    $endDateTimeFormatted = $endDateTime->format('Y-m-d\TH:i:sP');

    return [$startDateTimeFormatted, $endDateTimeFormatted];
}
