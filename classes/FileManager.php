<?php
require_once 'ConfigUrl.php';
require_once 'ImageHandler.php';

class FileManager
{
    private $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
    private $imageHandler;

    // Constructor donde puedes configurar el tamaño máximo y la calidad
    public function __construct($maxWidth = 1200, $quality = 80)
    {
        $this->imageHandler = new ImageHandler($maxWidth, $quality);
    }

    public function uploadLogo($name, $company_id)
    {
        return $this->uploadImage($_FILES['logo'], $company_id, 'logo', $name);
    }

    public function uploadBanner($company_id)
    {
        return $this->uploadImage($_FILES['banner'], $company_id, 'banner');
    }

    private function uploadImage($file, $company_id, $type, $name = '')
    {
        if (empty($file['name'])) {
            throw new Exception("No se seleccionó ningún archivo.");
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, $this->allowed_extensions)) {
            throw new Exception('Formato de archivo no permitido. Solo se permiten: ' . implode(', ', $this->allowed_extensions));
        }

        // Definir carpeta base
        if ($type === 'logo') {
            $folder = "logo-" . $company_id;
            $prefix = 'logo-' . preg_replace('/[^a-zA-Z0-9]/', '_', $name) . '-' . date('dmY') . '-' . uniqid();
        } elseif ($type === 'banner') {
            $folder = "user_" . $company_id;
            $prefix = 'banner-' . date('dmY') . '-' . uniqid();
        } else {
            throw new Exception('Tipo de imagen no reconocido.');
        }

        $upload_dir = dirname(__DIR__) . "/assets/img/" . ($type === 'logo' ? "uploads/" : "banners/") . $folder . "/";
        $this->deleteExistingFiles($upload_dir);

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $new_filename = $prefix . '.' . $extension;
        $destination = $upload_dir . $new_filename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new Exception("Error al mover el archivo al servidor.");
        }

        // Usar la clase ImageHandler para optimizar la imagen
        // $this->imageHandler->optimize($destination);

        // Ruta relativa que puedes guardar en la BD
        return ($type === 'logo' ? "assets/img/uploads/" : "assets/img/banners/") . $folder . "/" . $new_filename;
    }

    private function deleteExistingFiles($dir)
    {
        if (is_dir($dir)) {
            $files = glob($dir . '*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }
    }
}
