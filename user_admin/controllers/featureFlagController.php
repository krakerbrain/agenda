<?php
require_once dirname(__DIR__, 2) . '/configs/init.php';
require_once dirname(__DIR__, 2) . '/classes/FeatureManager.php';

$featureManager = new FeatureManager();

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Caso: verificar un flag específico
        if (isset($_GET['companyId'], $_GET['featureName'])) {
            $companyId = (int) $_GET['companyId'];
            $featureName = trim($_GET['featureName']);
            $enabled = $featureManager->isEnabled($companyId, $featureName);
            echo json_encode(['success' => true, 'enabled' => $enabled]);
            exit;
        } else if (isset($_GET['companyId'])) {
            $companyId = (int) $_GET['companyId'];
            $flags = $featureManager->getFlagsByCompany($companyId);
            echo json_encode(['success' => true, 'flags' => $flags]);
            exit;
        } else {
            // Caso: traer todos los flags
            $data = $featureManager->getAllFlags();
            echo json_encode(['success' => true, 'data' => $data]);
            exit;
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);

        // Actualizar flag existente
        if (isset($input['id'], $input['enabled'])) {
            $id = (int)$input['id'];
            $enabled = (bool)$input['enabled'];
            $featureManager->setFeatureById($id, $enabled);
            echo json_encode(['success' => true]);
            exit;
        }

        // Crear un nuevo flag
        if (isset($input['companyId'], $input['featureName'])) {
            $companyId = (int)$input['companyId'];
            $featureName = trim($input['featureName']);
            $enabled = isset($input['enabled']) ? (bool)$input['enabled'] : false;
            $newId = $featureManager->createFeatureFlag($companyId, $featureName, $enabled);
            echo json_encode(['success' => true, 'id' => $newId]);
            exit;
        }

        echo json_encode(['success' => false, 'msg' => 'Datos incompletos']);
        exit;
    }

    // Otros métodos HTTP no soportados
    echo json_encode(['success' => false, 'msg' => 'Método no permitido']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'msg' => $e->getMessage()]);
}
