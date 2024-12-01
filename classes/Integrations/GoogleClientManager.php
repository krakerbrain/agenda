<?php
// GoogleClientManager.php
use Google_Client;
use Google_Service_Calendar;

class GoogleClientManager
{
    private $client;
    private $integrationManager;

    public function __construct(IntegrationManager $integrationManager)
    {
        $this->client = new Google_Client();
        $this->client->setAuthConfig(dirname(__DIR__, 2) . $_ENV['GOOGLE_APPLICATION_CREDENTIALS_PATH']);
        $this->client->setAccessType('offline');
        $this->client->setPrompt('consent');
        $this->integrationManager = $integrationManager;
    }

    public function initializeClient($companyId)
    {
        $redirect_uri = $_ENV['HTTP'] . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
        $this->client->setRedirectUri($redirect_uri);
        $this->client->addScope(Google_Service_Calendar::CALENDAR);

        // Obtener datos de integración de Google Calendar
        $tokenData = $this->integrationManager->getGoogleCalendarIntegration($companyId);

        if ($tokenData === null || !$tokenData['enabled']) {
            // Si no hay integración o no está habilitada, redirigir a la autenticación
            return false;
        }

        // Verificar y actualizar el token si es necesario
        if ($this->isTokenValid($tokenData['integration_data'])) {
            $this->client->setAccessToken($tokenData['integration_data']['access_token']);
        } else {
            $this->refreshAccessToken($companyId, $tokenData);
        }

        return true;
    }

    private function isTokenValid($integrationData)
    {
        return isset($integrationData['access_token']) &&
            $integrationData['access_token'] !== null &&
            $integrationData['expires_at'] > time();
    }

    private function refreshAccessToken($companyId, $tokenData)
    {
        $this->client->setAccessToken($tokenData['integration_data']['access_token']);

        if ($this->client->isAccessTokenExpired() && !empty($tokenData['integration_data']['refresh_token'])) {
            $newToken = $this->client->fetchAccessTokenWithRefreshToken($tokenData['integration_data']['refresh_token']);
            $tokenData['integration_data']['access_token'] = $newToken['access_token'];
            $tokenData['integration_data']['expires_at'] = time() + $newToken['expires_in'];

            // Guardar el nuevo token en la base de datos
            $this->integrationManager->saveGoogleCalendarIntegration($companyId, $tokenData['integration_data']);
        } else {
            throw new Exception('El token de acceso es inválido y no hay refresh token disponible.');
        }
    }

    public function getClient()
    {
        return $this->client;
    }

    public function getAuthUrl()
    {
        return $this->client->createAuthUrl();
    }
}
