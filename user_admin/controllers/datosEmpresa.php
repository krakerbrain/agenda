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
        // Obtener todos los datos directamente de la base de datos
        $companyData = $companyManager->getCompanyDataForDatosEmpresa($company_id);

        echo json_encode([
            'success' => true,
            'data' => $companyData
        ]);
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validar y formatear teléfono
        $fomattedPhone = formatPhoneNumber($_POST['phone'] ?? '');

        // Actualizar los datos de la empresa
        $data = [
            'phone' => $fomattedPhone,
            'address' => $_POST['address'] ?? null,
            'description' => $_POST['description'] ?? null,
            'logo' => $_POST['logo_url'] ?? null,
            'selected_banner' => $_POST['banner_url'] ?? null
        ];

        $companyManager->updateCompanyData($company_id, $data);

        echo json_encode([
            'success' => true,
            'message' => 'Datos de la empresa actualizados correctamente'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Error al procesar la solicitud: ' . $e->getMessage()
    ]);
}

function formatPhoneNumber($telefono)
{
    // (Mantener la misma función de formateo)
    $telefono = preg_replace('/[\s\-\(\)]/', '', $telefono);

    if (preg_match('/^\+56\d{9}$/', $telefono)) {
        return $telefono;
    }

    if (preg_match('/^56\d{9}$/', $telefono)) {
        return '+' . $telefono;
    }

    if (preg_match('/^9\d{8}$/', $telefono)) {
        return '+56' . $telefono;
    }

    if (preg_match('/^\d{8}$/', $telefono)) {
        return '+569' . $telefono;
    }

    throw new Exception('Número de teléfono inválido.');
}
