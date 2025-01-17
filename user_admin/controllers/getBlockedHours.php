<?php
require_once dirname(__DIR__, 2) . '/configs/init.php';
require_once dirname(__DIR__, 2) . '/access-token/seguridad/JWTAuth.php';
require_once dirname(__DIR__, 2) . '/classes/Appointments.php';
require_once dirname(__DIR__, 2) . '/classes/Schedules.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';

$baseUrl = ConfigUrl::get();
$auth = new JWTAuth();

try {
    // Validar método HTTP
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
        http_response_code(405); // Código HTTP 405 Method Not Allowed
        exit;
    }

    // Validar token del usuario
    $datosUsuario = $auth->validarTokenUsuario();
    $company_id = $datosUsuario['company_id'];

    // Obtener los días bloqueados desde la base de datos
    $appointments = new Appointments();
    $blockedDays = $appointments->getBlockedDays($company_id);

    // Retornar los datos al frontend
    echo json_encode(['success' => true, 'data' => $blockedDays]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Error al procesar la solicitud: ' . $e->getMessage()]);
}