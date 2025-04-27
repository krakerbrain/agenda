<?php
require_once dirname(__DIR__, 2) . '/classes/CompanyManager.php';
require_once dirname(__DIR__, 2) . '/classes/UserRegistrationService.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Recibir datos del formulario
        $name = $_POST['business_name'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];
        $logo = isset($_FILES['logo']) ? $_FILES['logo'] : null;
        $owner_name = $_POST['owner_name'];
        $email = $_POST['email'];
        $password = '1234'; // Contraseña temporal
        $role_id = 2; // Rol predeterminado

        // Crear empresa
        $companyManager = new CompanyManager();
        $companyResult = $companyManager->createCompany($name, $phone, $address, $logo);

        if (!$companyResult['success']) {
            echo json_encode($companyResult);
            exit;
        }

        // Registrar usuario propietario
        $userRegistration = new UserRegistrationService();
        $userData = [
            'username' => $owner_name,
            'email' => $email,
            'password' => $password,
            'password2' => $password,
            'role_id' => $role_id,
            'company_id' => $companyResult['company_id']
        ];

        $userResult = $userRegistration->registerUser($userData, $companyResult['company_id'], true);

        if ($userResult['success']) {
            echo json_encode([
                'success' => true,
                'message' => "Empresa y usuario creados exitosamente. Bienvenido a Agendarium."
            ]);
        } else {
            echo json_encode($userResult);
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => "Error inesperado: " . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => "Método de solicitud no permitido."
    ]);
}
