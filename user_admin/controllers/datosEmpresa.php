<?php
require_once dirname(__DIR__, 2) . '/configs/init.php';
require_once dirname(__DIR__, 2) . '/access-token/seguridad/JWTAuth.php';
require_once dirname(__DIR__, 2) . '/classes/CompanyManager.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';

$baseUrl = ConfigUrl::get();
$auth = new JWTAuth();
$datosUsuario = $auth->validarTokenUsuario();
header('Content-Type: application/json');
try {
    $companyManager = new CompanyManager();
    $company_id = $datosUsuario['company_id'];
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $bannerDir = dirname(__DIR__, 2) . "/assets/img/banners/user_" . $company_id;
        $savedBanner = null;

        if (is_dir($bannerDir)) {
            $files = scandir($bannerDir);
            foreach ($files as $file) {
                if ($file !== "." && $file !== ".." && !is_dir($bannerDir . "/" . $file)) {
                    $savedBanner = $file; // Guarda el primer archivo encontrado
                    break;
                }
            }
        }
        echo json_encode(['success' => true, 'data' => $companyManager->getCompanyDataForDatosEmpresa($company_id), 'savedBanner' => $savedBanner]);
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Capturar los datos de la empresa desde $_POST
        $phone = $_POST['phone'] ?? null;
        $address = $_POST['address'] ?? null;
        $description = $_POST['description'] ?? null;
        $logoUrl = $_POST['logo_url'] ?? null;  // Logo existente si no se sube uno nuevo
        $selected_banner = $_POST['selected-banner'] ?? null;

        // Manejo del archivo de imagen (logo) en $_FILES
        $logoName = $logoUrl;  // Mantener el logo anterior si no hay nuevo
        $fomattedPhone = formatPhoneNumber($phone);
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $fileManager = new FileManager();
            $logoName = $fileManager->uploadLogo($_POST['company_name'], $company_id);
        }

        // Actualizar los datos de la empresa
        $data = [
            'phone' => $fomattedPhone,
            'address' => $address,
            'description' => $description,
            'logo' => $logoName,
            'selected_banner' => $selected_banner
        ];

        $companyManager->updateCompanyData($company_id, $data);

        echo json_encode(['success' => true, 'message' => 'Datos de la empresa actualizados correctamente']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Error al procesar la solicitud: ' . $e->getMessage()]);
}


function formatPhoneNumber($telefono)
{
    // Eliminar espacios en blanco, guiones y paréntesis, pero mantener el símbolo "+"
    $telefono = preg_replace('/[\s\-\(\)]/', '', $telefono);

    // Si el número empieza con "+56" y tiene 11 dígitos, es correcto
    if (preg_match('/^\+56\d{9}$/', $telefono)) {
        return $telefono;
    }

    // Si el número ya empieza con "56" y tiene 11 dígitos, añadir "+"
    if (preg_match('/^56\d{9}$/', $telefono)) {
        return '+' . $telefono;
    }

    // Si el número tiene 8 dígitos y empieza con "9" (móvil chileno), agregar "+56"
    if (preg_match('/^9\d{8}$/', $telefono)) {
        return '+56' . $telefono;
    }

    // Si el número tiene 8 dígitos (número fijo chileno), agregar "+569"
    if (preg_match('/^\d{8}$/', $telefono)) {
        return '+569' . $telefono;
    }

    // Si el número no es válido, lanzar una excepción
    throw new Exception('Número de teléfono inválido.');
}
