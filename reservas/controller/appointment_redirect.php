<?php
require_once dirname(__DIR__, 2) . '/access-token/seguridad/JWTAuth.php';
require_once dirname(__DIR__, 2) . '/classes/CompanyManager.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';

$jwtAuth = new JWTAuth();
// Validar el token
if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $result = $jwtAuth->validarTokenCita($token);

    if ($result['valid']) {
        // Obtener la URL amigable desde companies
        $company = new CompanyManager();
        $custom_url = $company->getCompanyCustomUrl($result['company_id']);

        // Construir la URL completa usando ConfigUrl
        $fullUrl = ConfigUrl::get() . "reservas/$custom_url?view=details&reservation_id={$result['appointment_id']}";

        // Redireccionar a la URL final
        header("Location: $fullUrl");
        exit(); // Asegurarse de detener la ejecución después del redireccionamiento
    } else {
        // Manejar el caso en que el token no es válido
        echo "Token inválido. Por favor, verifique su enlace.";
    }
} else {
    echo "No se ha proporcionado ningún token.";
}
