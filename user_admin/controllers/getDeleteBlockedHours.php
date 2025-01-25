<?php
require_once dirname(__DIR__, 2) . '/configs/init.php';
require_once dirname(__DIR__, 2) . '/access-token/seguridad/JWTAuth.php';
require_once dirname(__DIR__, 2) . '/classes/Appointments.php';
require_once dirname(__DIR__, 2) . '/classes/Schedules.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';

$baseUrl = ConfigUrl::get();
$auth = new JWTAuth();

try {
    // Obtener método HTTP
    $method = $_SERVER['REQUEST_METHOD'];

    // Validar token del usuario
    $datosUsuario = $auth->validarTokenUsuario();
    $company_id = $datosUsuario['company_id'];

    $appointments = new Appointments();

    if ($method === 'GET') {
        // Obtener los días bloqueados desde la base de datos
        $blockedDays = $appointments->getBlockedDays($company_id);

        // Retornar los datos al frontend
        echo json_encode(['success' => true, 'data' => $blockedDays]);
    } elseif ($method === 'DELETE') {
        // Validar que el token sea proporcionado
        $data = json_decode(file_get_contents('php://input'), true); // Obtener datos de la solicitud DELETE
        $token = $data['token'] ?? null;

        if (!$token) {
            echo json_encode(['success' => false, 'message' => 'Token no proporcionado.']);
            http_response_code(400); // Código HTTP 400 Bad Request
            exit;
        }

        // Eliminar el día bloqueado usando el token
        $result = $appointments->deleteBlockedDay($token, $company_id);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Día bloqueado eliminado correctamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Día bloqueado no encontrado o no se pudo eliminar.']);
            http_response_code(404); // Código HTTP 404 Not Found
        }
    } else {
        // Método no permitido
        echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
        http_response_code(405); // Código HTTP 405 Method Not Allowed
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Error al procesar la solicitud: ' . $e->getMessage()]);
    http_response_code(500); // Código HTTP 500 Internal Server Error
}
