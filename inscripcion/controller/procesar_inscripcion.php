<?php
require_once dirname(__DIR__, 2) . '/classes/CompanyManager.php';
require_once dirname(__DIR__, 2) . '/classes/UserRegistrationService.php';
require_once dirname(__DIR__, 2) . '/access-token/seguridad/ActivationTokenService.php';
require_once dirname(__DIR__, 2) . '/classes/EmailTemplate.php';
require_once dirname(__DIR__, 2) . '/classes/EmailSender.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // 1. Recibir datos del formulario
        $name = $_POST['business_name'];
        $owner_name = $_POST['owner_name'];
        $email = $_POST['email'];
        $userService = new UserRegistrationService();

        // 1.2 Validar correo
        if ($userService->emailExists($email)) {
            echo json_encode([
                'success' => false,
                'message' => 'El correo ya está registrado. Por favor usa otro.'
            ]);
            exit;
        }

        // 2. Crear empresa
        $companyManager = new CompanyManager();
        $companyResult = $companyManager->registerNewCompanyFromWeb($name);

        if (!$companyResult['success']) {
            throw new Exception($companyResult['error']);
        }

        $company_id = $companyResult['company_id'];

        // 3. Crear usuario principal (puedes usar un método como registerInitialUser)
        $userResult = $userService->registerInitialUserFromWeb($owner_name, $email, $company_id);

        if (!$userResult['success']) {
            throw new Exception($userResult['error']);
        }

        // 4. Generar token de activación
        $tokenService = new ActivationTokenService();
        $token = $tokenService->createTokenForUser($userResult['user_id']);

        // 5. Construir correo con plantilla
        $template = new EmailTemplate();
        $emailContent = $template->buildActivationEmail($owner_name, $token);

        // 6. Enviar correo
        $emailSender = new EmailSender();
        $emailSender->sendInscriptionMail($emailContent['subject'], $email, $emailContent['body']);

        echo json_encode([
            'success' => true,
            'message' => 'Cuenta creada correctamente. Te enviamos un correo para activar tu cuenta.'
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => "Error inesperado: " . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => "Método no permitido"
    ]);
}
