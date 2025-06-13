<?php

require_once dirname(__DIR__, 2) . '/configs/init.php';
require_once dirname(__DIR__, 2) . '/access-token/seguridad/JWTAuth.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';
require_once dirname(__DIR__, 2) . '/classes/IntegrationManager.php';

$baseUrl = ConfigUrl::get();
$auth = new JWTAuth();
$datosUsuario = $auth->validarTokenUsuario();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        $enable = filter_var($data['enable'] ?? false, FILTER_VALIDATE_BOOLEAN);

        $integrationManager = new IntegrationManager();
        $result = $integrationManager->handleWhatsAppIntegration($datosUsuario['company_id'], $enable);

        echo json_encode([
            'success' => $result,
            'message' => $result
                ? ($enable ? 'WhatsApp habilitado correctamente' : 'WhatsApp deshabilitado correctamente')
                : 'Error al modificar el estado de WhatsApp'
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
