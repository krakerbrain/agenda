<?php
header('Content-Type: application/json');
require_once dirname(__DIR__) . '/configs/init.php'; // Cargar dependencias
require_once dirname(__DIR__) . '/classes/ConfigUrl.php';
require_once dirname(__DIR__) . '/classes/IntegrationManager.php';
require_once dirname(__DIR__) . '/classes/Integrations/GoogleIntegrationManager.php';
require_once dirname(__DIR__) . '/access-token/seguridad/JWTAuth.php';

// Crear instancia para manejar la autenticación JWT
$auth = new JWTAuth();
$datosUsuario = $auth->validarTokenUsuario();

// Obtener el ID de la compañía
$company_id = $datosUsuario['company_id'];

try {
    // Obtener la URL base
    $baseUrl = ConfigUrl::get();

    // Instancia de IntegrationManager
    $integrationManager = new IntegrationManager();
    $googleIntegrationManager = new GoogleIntegrationManager($company_id);

    // Obtener un cliente de Google configurado
    $client = $googleIntegrationManager->getConfiguredGoogleClient();

    // Obtener datos de integración de Google Calendar para la compañía
    $googleCalendarData = $integrationManager->getGoogleCalendarIntegration($company_id);

    // Verificar si hay datos de integración y si la integración está habilitada
    if ($googleCalendarData !== null && $googleCalendarData['enabled'] && !isset($_GET['code'])) {
        $googleCalendarIntegrationData = $googleCalendarData['integration_data'];

        if (!empty($googleCalendarIntegrationData['access_token'])) {
            $client->setAccessToken($googleCalendarIntegrationData['access_token']);

            // Verificar si el token ha expirado
            if ($client->isAccessTokenExpired()) {
                if (!empty($googleCalendarIntegrationData['refresh_token'])) {
                    // Renueva el token utilizando el refresh_token
                    $newToken = $client->fetchAccessTokenWithRefreshToken($googleCalendarIntegrationData['refresh_token']);

                    if (!isset($newToken['error'])) {
                        // Actualizar el token y guardar en la base de datos
                        $googleCalendarIntegrationData['access_token'] = $newToken['access_token'];
                        $googleCalendarIntegrationData['expires_at'] = time() + $newToken['expires_in'];

                        $integrationManager->saveGoogleCalendarIntegration($company_id, $googleCalendarIntegrationData);
                    } else {
                        // Error al renovar el token, redirigir a la autenticación
                        redirectToAuth($client);
                    }
                } else {
                    // No hay refresh_token, redirigir a la autenticación
                    redirectToAuth($client);
                }
            }
        } else {
            redirectToAuth($client);
        }
    } else {
        // No hay token o la integración no es válida, redirigir a la autenticación
        if (!isset($_GET['code'])) {
            redirectToAuth($client);
        }

        if (isset($_GET['code'])) {
            // Intercambiar el código de autorización por un token de acceso
            $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
            if (isset($token['error'])) {
                throw new Exception('Error al obtener el token: ' . $token['error']);
            }

            // Guardar los tokens en la base de datos
            $googleCalendarData = [
                'access_token' => $token['access_token'],
                'refresh_token' => $token['refresh_token'] ?? null,
                'expires_at' => time() + $token['expires_in']
            ];

            $integrationManager->saveGoogleCalendarIntegration($company_id, $googleCalendarData);

            // Redirigir al dashboard después de la autenticación
            $redirect_uri = $_ENV['HTTP'] . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
            header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
            exit();
        }
    }

    // Redirigir al dashboard después de la autenticación exitosa
    header("Location: " . $baseUrl . "user_admin/index.php");
    exit();
} catch (Exception $e) {
    // Manejo de errores
    echo 'Error: ' . $e->getMessage();
}

/**
 * Redirige a la URL de autenticación de Google
 * 
 * @param Google_Client $client
 */
function redirectToAuth($client)
{
    $auth_url = $client->createAuthUrl();
    header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
    exit();
}
