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
 



// // Verifica si el token ha sido recibido
// if (isset($_GET['token'])) {
//     $token = $_GET['token'];

//     // Consulta para obtener el ID de la empresa utilizando el token
//     $stmt = $conn->prepare("SELECT custom_url FROM companies WHERE social_token = :token");
//     $stmt->bindParam(':token', $token);
//     $stmt->execute();
//     $result = $stmt->fetch(PDO::FETCH_ASSOC);

//     if ($result['custom_url']) {
//         // Si se encuentra la empresa, buscar la red social preferida
//         $stmt = $conn->prepare("SELECT url FROM company_social_networks WHERE company_id = :company_id AND red_preferida = 1");
//         $stmt->bindParam(':company_id', $result['id']);
//         $stmt->execute();
//         $social = $stmt->fetch(PDO::FETCH_ASSOC);

//         if ($social['url']) {
//             // Si se encuentra la red social preferida, redirigir a la página de la red social
//             header("Location:" . $social['url']);
//         } else {
//             echo "No se ha encontrado una red social preferida.";
//         }
//     } else {
//         echo "Token inválido.";
//     }
// } else {
//     echo "No se ha proporcionado ningún token.";
// }