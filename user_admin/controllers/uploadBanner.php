<?php
require_once dirname(__DIR__, 2) . '/configs/init.php';

header('Content-Type: application/json');

try {
    // Verificar que se subió un archivo
    if (isset($_FILES['banner']) && $_FILES['banner']['error'] === UPLOAD_ERR_OK) {
        // Obtener el companyId enviado desde el frontend
        $companyId = $_POST['companyId']; // Recuperar el companyId

        // Validar que el companyId no esté vacío
        if (empty($companyId)) {
            echo json_encode(['success' => false, 'error' => 'El companyId es requerido.']);
            exit;
        }

        // Definir la ruta de la carpeta del usuario
        $uploadDir = dirname(__DIR__, 2) . "/assets/img/banners/";
        $userDir = $uploadDir . "user_" . $companyId . "/";

        // Si la carpeta existe, eliminar su contenido antes de subir la nueva imagen
        if (is_dir($userDir)) {
            $files = glob($userDir . '*'); // Obtener todos los archivos dentro de la carpeta
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file); // Eliminar el archivo
                }
            }
        } else {
            mkdir($userDir, 0755, true); // Crear la carpeta con permisos 0755 si no existe
        }

        // Definir la ruta de destino del archivo
        $fileName = basename($_FILES['banner']['name']); // Nombre del archivo
        $destPath = $userDir . $fileName;

        // Mover el archivo al servidor
        if (move_uploaded_file($_FILES['banner']['tmp_name'], $destPath)) {
            echo json_encode([
                'success' => true,
                'imageUrl' => "/assets/img/banners/user_" . $companyId . "/" . $fileName // URL de la imagen
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al guardar la imagen.']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'No se recibió una imagen válida.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Error en el servidor: ' . $e->getMessage()]);
}
