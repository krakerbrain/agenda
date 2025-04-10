<?php
require_once dirname(__DIR__, 2) . '/classes/FileManager.php';

header('Content-Type: application/json');

try {
    if (isset($_FILES['banner']) && $_FILES['banner']['error'] === UPLOAD_ERR_OK) {
        $companyId = $_POST['companyId'];

        if (empty($companyId)) {
            echo json_encode(['success' => false, 'error' => 'El companyId es requerido.']);
            exit;
        }

        $fileManager = new FileManager();
        $bannerPath = $fileManager->uploadBanner($companyId);

        echo json_encode(['success' => true, 'imageUrl' => '/' . $bannerPath]);
    } else {
        echo json_encode(['success' => false, 'error' => 'No se recibió una imagen válida.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
