<?php
require_once dirname(__DIR__, 2) . '/configs/init.php';
require_once dirname(__DIR__, 2) . '/access-token/seguridad/JWTAuth.php';
require_once dirname(__DIR__, 2) . '/classes/CompanyManager.php';
require_once dirname(__DIR__, 2) . '/classes/Users.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';
require_once dirname(__DIR__, 2) . '/classes/EmailTemplate.php';

$baseUrl = ConfigUrl::get();
$auth = new JWTAuth();
$datosUsuario = $auth->validarTokenUsuario();
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
        } else {
            echo json_encode($result);
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
        $data = json_decode(file_get_contents('php://input'), true);
        $result = $companyManager->updateCompanyStatus($data);
        if ($result && $data['is_active']) {
            $emailTemplateBuilder = new EmailTemplate();
            $emailSent = $emailTemplateBuilder->buildInscriptionMail($data['id']);
            echo json_encode($emailSent);
        }
        echo json_encode($result);
    } else {
        echo json_encode(['success' => true, 'data' => $companyManager->getCompanyDataForCompanyList()]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Error al procesar la solicitud: ' . $e->getMessage()]);
}
