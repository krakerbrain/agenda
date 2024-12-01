<?php

require_once dirname(__DIR__, 2) . '/configs/init.php';
require_once dirname(__DIR__, 2) . '/access-token/seguridad/JWTAuth.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';
require_once dirname(__DIR__, 2) . '/classes/IntegrationManager.php'; // Asegúrate de que la clase esté en la ubicación correcta

$baseUrl = ConfigUrl::get();
$auth = new JWTAuth();
$datosUsuario = $auth->validarTokenUsuario();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Decodifica los datos recibidos en el cuerpo de la solicitud
        $data = json_decode(file_get_contents('php://input'), true);

        // Instanciar la clase IntegrationManager
        $integrationManager = new IntegrationManager();

        // Ejecutar el método para deshabilitar la integración
        $result = $integrationManager->disableGoogleCalendarIntegration($datosUsuario['company_id']);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Integración deshabilitada correctamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al deshabilitar la integración.']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Excepción capturada: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
}
