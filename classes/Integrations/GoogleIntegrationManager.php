<?php
header('Content-Type: application/json');
require_once dirname(__DIR__, 2) . '/classes/IntegrationManager.php';

class GoogleIntegrationManager
{
    private $client;
    private $calendarService;
    private $integrationManager;

    public function __construct($companyId)
    {
        $this->integrationManager = new IntegrationManager();
        $this->client = $this->getConfiguredGoogleClient(); // Usa el método para configurar el cliente
        $this->initializeClient($companyId); // Inicializa el cliente con los datos de la compañía
        $this->calendarService = new Google_Service_Calendar($this->client);
    }

    /**
     * Devuelve un cliente de Google configurado
     * 
     * @return Google_Client
     */
    public function getConfiguredGoogleClient()
    {
        $client = new Google_Client();
        $client->setAuthConfig(dirname(__DIR__, 2) . $_ENV['GOOGLE_APPLICATION_CREDENTIALS_PATH']);
        $client->setAccessType('offline');
        $client->setPrompt('consent');
        $redirect_uri = $_ENV['HTTP'] . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
        $client->setRedirectUri($redirect_uri);
        $client->addScope(Google_Service_Calendar::CALENDAR);

        return $client;
    }

    // Inicializa el cliente de Google y maneja la autenticación
    private function initializeClient($companyId)
    {
        // Configura la autenticación del cliente de Google
        $this->client->setAuthConfig(dirname(__DIR__, 2) . $_ENV['GOOGLE_APPLICATION_CREDENTIALS_PATH']);

        // Obtiene los datos de integración de Google Calendar
        $tokenData = $this->integrationManager->getGoogleCalendarIntegration($companyId);

        // Si no se encuentra la integración de Google Calendar
        if ($tokenData === null) {
            throw new Exception('No se encontró la integración de Google Calendar para esta compañía.');
        }

        // Verifica si el token de acceso existe y no ha expirado
        if (isset($tokenData['integration_data']['access_token']) && $tokenData['integration_data']['access_token'] !== null) {
            if ($tokenData['integration_data']['expires_at'] > time()) {
                // Si el token en la BD es válido, lo establecemos
                $this->client->setAccessToken($tokenData['integration_data']['access_token']);
            } else {
                // Token expirado, verificar con Google
                $this->client->setAccessToken($tokenData['integration_data']['access_token']);

                if ($this->client->isAccessTokenExpired()) {
                    // Si el token está expirado, actualizarlo
                    $this->refreshAccessToken($companyId, $tokenData);
                }
            }
        }
    }


    // Refresca el access token si ha expirado
    private function refreshAccessToken($companyId, $tokenData)
    {
        if (!empty($tokenData['integration_data']['refresh_token'])) {
            $newToken = $this->client->fetchAccessTokenWithRefreshToken($tokenData['integration_data']['refresh_token']);

            if (isset($newToken['error'])) {
                throw new Exception('Error al renovar el token: ' . $newToken['error']);
            }

            // Guardar el nuevo token en la base de datos
            $this->integrationManager->saveGoogleCalendarIntegration($companyId, [
                'access_token' => $newToken['access_token'],
                'refresh_token' => $newToken['refresh_token'] ?? $tokenData['integration_data']['refresh_token'],
                'expires_at' => time() + $newToken['expires_in']
            ]);

            $this->client->setAccessToken($newToken['access_token']);
        } else {
            throw new Exception('No hay refresh_token disponible para renovar el access_token.');
        }
    }

    // Crear un evento en Google Calendar
    public function createEvent($summary, $startDateTime, $endDateTime, $timeZone = 'America/Santiago')
    {
        $event = new Google_Service_Calendar_Event([
            'summary' => $summary,
            'start' => [
                'dateTime' => $startDateTime,
                'timeZone' => $timeZone,
            ],
            'end' => [
                'dateTime' => $endDateTime,
                'timeZone' => $timeZone,
            ]
        ]);

        try {
            $createdEvent = $this->calendarService->events->insert('primary', $event);
            return $createdEvent->getId();
        } catch (Exception $e) {
            throw new Exception('Error al crear el evento en Google Calendar: ' . $e->getMessage());
        }
    }

    // Eliminar un evento en Google Calendar
    public function deleteEvent($eventId)
    {
        try {
            $this->calendarService->events->delete('primary', $eventId);
        } catch (Exception $e) {
            throw new Exception('Error al eliminar el evento en Google Calendar: ' . $e->getMessage());
        }
    }

    // Obtener la zona horaria del usuario
    public function getUserTimeZone()
    {
        $calendar = $this->calendarService->calendars->get('primary');
        return $calendar->getTimeZone();
    }
}
