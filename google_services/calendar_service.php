<?php
session_start();

function getUserTimeZone($client)
{
    $calendarService = new Google_Service_Calendar($client);
    $calendar = $calendarService->calendars->get('primary');
    return $calendar->getTimeZone();
}

function createCalendarEvent($client, $name, $service, $startDateTimeFormatted, $endDateTimeFormatted, $appointmentId)
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

    // Obtener la zona horaria del calendario principal del usuario
    $userTimeZone = getUserTimeZone($client);

    $event = new Google_Service_Calendar_Event(array(
        'summary' => $service . " con " . $name,
        'start' => array(
            'dateTime' => $startDateTimeFormatted,
            'timeZone' => $userTimeZone, // Asegurar que coincida la zona horaria
        ),
        'end' => array(
            'dateTime' => $endDateTimeFormatted,
            'timeZone' => $userTimeZone, // Asegurar que coincida la zona horaria
        )
    ));


    try {
        // Obtén el ID del calendario principal del usuario autenticado
        $calendarId = 'primary';
        // Crear el evento en el calendario del usuario
        $event = $calendarService->events->insert($calendarId, $event);
        $eventId = $event->getId();
        return $eventId;
    } catch (Exception $e) {
        throw new Exception('Failed to create event: ' . $e->getMessage());
    }
}

function formatDateTime($date, $startTime, $endTime, $timeZone = 'America/Santiago')
{
    $startDateTime = new DateTime("$date $startTime", new DateTimeZone($timeZone));
    $endDateTime = new DateTime("$date $endTime", new DateTimeZone($timeZone));

    $startDateTimeFormatted = $startDateTime->format(DateTime::RFC3339);  // Incluye la zona horaria
    $endDateTimeFormatted = $endDateTime->format(DateTime::RFC3339);  // Incluye la zona horaria

    return [$startDateTimeFormatted, $endDateTimeFormatted];
}
