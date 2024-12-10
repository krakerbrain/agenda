<?php
date_default_timezone_set('UTC');
require_once dirname(__DIR__, 2) . '/configs/init.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';
require_once dirname(__DIR__, 2) . '/classes/UniqueEvents.php';
require_once dirname(__DIR__, 2) . '/access-token/seguridad/JWTAuth.php';

$auth = new JWTAuth();
$datosUsuario = $auth->validarTokenUsuario();
$company_id = $datosUsuario['company_id'];

// Crear instancia de la clase Appointments
$events = new UniqueEvents();
$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'];

try {
    // Actualizar el ID del evento en la base de datos
    $events->updateEvent($id, 1);

    // Confirmar la transacción
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Cita confirmada exitosamente', 'success' => true]);
    http_response_code(200);
} catch (Exception $e) {
    // Intentar obtener el mensaje de error
    $errorResponse = $e->getMessage();
    preg_match('/\{(?:[^{}]|(?R))*\}/', $errorResponse, $matches);

    if ($matches) {
        $errorData = json_decode($matches[0], true);

        if (json_last_error() === JSON_ERROR_NONE) {
            if (isset($errorData['error']['code']) && $errorData['error']['code'] == 401) {
                echo json_encode([
                    'error' => true,
                    'code' => 401,
                    'message' => 'Error de autenticación: ' . $errorData['error']['message'],
                ]);
                http_response_code(401);
            } else {
                echo json_encode([
                    'error' => true,
                    'code' => $errorData['error']['code'] ?? 500,
                    'message' => $errorData['error']['message'] ?? 'Error desconocido.',
                ]);
                http_response_code($errorData['error']['code'] ?? 500);
            }
        } else {
            echo json_encode([
                'error' => true,
                'code' => 500,
                'message' => 'Error interno al procesar la respuesta de la API: ' . $e->getMessage(),
            ]);
            http_response_code(500);
        }
    } else {
        echo json_encode([
            'error' => true,
            'code' => 500,
            'message' => 'Error interno: ' . $e->getMessage(),
        ]);
        http_response_code(500);
    }
} finally {
    $events = null;
}
