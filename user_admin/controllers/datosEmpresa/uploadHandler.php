<?php
require_once dirname(__DIR__, 3) . '/configs/init.php';
require_once dirname(__DIR__, 3) . '/classes/FileManager.php';

header('Content-Type: application/json');

$response = [
    'success' => false,
    'error' => '',
];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Método no permitido.");
    }

    if (!isset($_POST['companyId']) || !is_numeric($_POST['companyId'])) {
        throw new Exception("ID de empresa no válido.");
    }

    $companyId = (int)$_POST['companyId'];
    $tipo = $_POST['tipo'] ?? 'banner';

    if (!isset($_FILES['file'])) {
        throw new Exception("Archivo no encontrado.");
    }

    $fileManager = new FileManager();

    switch ($tipo) {
        case 'logo':
            $nombreEmpresa = $_POST['nombreEmpresa'] ?? 'empresa';
            $ruta = $fileManager->uploadLogo($nombreEmpresa, $companyId);
            break;

        case 'banner':
            $ruta = $fileManager->uploadBanner($companyId);
            break;

        default:
            throw new Exception("Tipo de archivo no válido.");
    }

    $response['success'] = true;
    $response['imageUrl'] = $ruta;
    $response['fileName'] = basename($ruta);
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response);
