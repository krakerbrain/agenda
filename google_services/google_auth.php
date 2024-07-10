<?php
session_start();
require_once dirname(__DIR__) . '/vendor/autoload.php'; // Cargar dependencias
require_once dirname(__DIR__) . '/classes/ConfigUrl.php';

use Dotenv\Dotenv;
use Google_Client;
use Google_Service_Calendar;

try {
    // Cargar variables de entorno desde el archivo .env
    $dotenv = Dotenv::createImmutable(dirname(__DIR__));
    $dotenv->load();

    $baseUrl = ConfigUrl::get();

    $client = new Google_Client();
    $client->setAuthConfig($_ENV['GOOGLE_APPLICATION_CREDENTIALS_PATH']); // Ruta al archivo de configuración de credenciales de Google

    $redirect_uri = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
    $client->setRedirectUri($redirect_uri);
    $client->addScope(Google_Service_Calendar::CALENDAR);

    // Verificar si el código de autorización está presente en la URL
    if (!isset($_SESSION['access_token']) && !isset($_GET['code'])) {
        // Redirigir al usuario para iniciar sesión
        $auth_url = $client->createAuthUrl();
        header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
        exit();
    }

    if (isset($_GET['code'])) {
        // Intercambiar el código de autorización por un token de acceso
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
        $_SESSION['access_token'] = $token;


        // Guardar el token de actualización si es proporcionado por Google
        if (isset($token['refresh_token'])) {
            $_SESSION['refresh_token'] = $token['refresh_token'];
        }

        // Redirigir al dashboard o página principal después de la autenticación
        header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
        exit();
    }

    $client->setAccessToken($_SESSION['access_token']);
    if ($client->isAccessTokenExpired() && isset($_SESSION['refresh_token'])) {
        $client->fetchAccessTokenWithRefreshToken($_SESSION['refresh_token']);
        $_SESSION['access_token'] = $client->getAccessToken();
    }

    header("Location: " . $baseUrl . "user_admin/index.php");
    exit();
} catch (Exception $e) {
    // Manejo de errores
    echo 'Error: ' . $e->getMessage();
    // Podrías loguear el error, redirigir a una página de error, etc.
}
