<?php
require_once dirname(__DIR__, 2) . '/classes/CompanyManager.php';
require_once dirname(__DIR__, 2) . '/classes/Users.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';
require_once dirname(__DIR__, 2) . '/access-token/seguridad/jwt.php';

$baseUrl = ConfigUrl::get();
$datosUsuario = validarTokenSuperUser();
if (!$datosUsuario) {
    header("Location: " . $baseUrl . "login/index.php");
    exit();
}
header('Content-Type: application/json');
try {
    $companyManager = new CompanyManager();
    if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        $data = json_decode(file_get_contents('php://input'), true);
        $result = $companyManager->deleteCompany($data['id']);
        if ($result['success']) {
            $user = new Users();
            $result = $user->delete_user_by_company($data['id']);
            echo json_encode($result);
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
        $data = json_decode(file_get_contents('php://input'), true);
        $result = $companyManager->updateCompanyStatus($data);
        echo json_encode($result);
    } else {
        echo json_encode(['success' => true, 'data' => $companyManager->getAllCompanies()]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Error al procesar la solicitud: ' . $e->getMessage()]);
}
