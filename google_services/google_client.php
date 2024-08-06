<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();


function getClient()
{
    $client = new Google_Client();
    $client->setAuthConfig($_ENV['GOOGLE_APPLICATION_CREDENTIALS_PATH']);

    if (isset($_SESSION['access_token'])) {
        $client->setAccessToken($_SESSION['access_token']);
    }

    if ($client->isAccessTokenExpired() && isset($_SESSION['refresh_token'])) {
        $client->fetchAccessTokenWithRefreshToken($_SESSION['refresh_token']);
        $_SESSION['access_token'] = $client->getAccessToken();
    }

    return $client;
}
