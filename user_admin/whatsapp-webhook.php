<?php
require_once dirname(__DIR__) . '/configs/init.php';
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $verify_token = "www.agendarium.com";
    $challenge = $_GET['hub_challenge'];
    $hub_verify_token = $_GET['hub_verify_token'];

    if ($hub_verify_token === $verify_token) {
        echo $challenge; // Responde con el desafío para verificar
    } else {
        http_response_code(403);
        echo "Token de verificación incorrecto.";
    }
}
