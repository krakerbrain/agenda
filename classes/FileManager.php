<?php
require_once 'ConfigUrl.php'; // Asegúrate de tener la ruta correcta

class FileManager
{
    private $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif']; // Extensiones permitidas

    public function uploadLogo($name, $company_id)
    {
        if (!empty($_FILES['logo']['name'])) {
            // Ruta de la carpeta específica de la empresa
            $upload_dir = dirname(__DIR__) . '/assets/img/uploads/logo-' . $company_id . '/';

            // Crear la carpeta si no existe
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true); // Crear la carpeta con permisos
            }

            // Eliminar el logo actual si existe (buscando en la carpeta de la empresa)
            $this->deleteCurrentLogo($company_id);

            // Obtener la extensión del archivo
            $extension = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));

            // Validar la extensión del archivo
            if (!in_array($extension, $this->allowed_extensions)) {
                throw new Exception('Formato de archivo no permitido. Solo se permiten: ' . implode(', ', $this->allowed_extensions));
            }

            // Formatear el nombre del archivo
            $formatted_name = 'logo-' . preg_replace('/[^a-zA-Z0-9]/', '_', $name) . '-' . date('dmY') . '-' . uniqid() . '.' . $extension;

            $logo_path = $upload_dir . $formatted_name;

            // Intentar mover el archivo a la carpeta de uploads
            if (!move_uploaded_file($_FILES['logo']['tmp_name'], $logo_path)) {
                throw new Exception('Error al subir el archivo.');
            }

            // Devolver la ruta relativa del logo
            $logo = 'assets/img/uploads/logo-' . $company_id . '/' . $formatted_name;
            return $logo;
        } else {
            throw new Exception('No se seleccionó ningún archivo.');
        }
    }

    // Método para eliminar el logo actual
    private function deleteCurrentLogo($company_id)
    {
        // Ruta de la carpeta del logo de la empresa
        $dir = dirname(__DIR__) . '/assets/img/uploads/logo-' . $company_id . '/';

        // Si la carpeta existe
        if (is_dir($dir)) {
            // Obtener todos los archivos en la carpeta
            $files = glob($dir . '*'); // Obtener todos los archivos en la carpeta

            // Eliminar cada archivo en la carpeta
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file); // Eliminar el archivo
                }
            }
        }
    }
}
