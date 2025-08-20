<?php
require_once dirname(__DIR__, 2) . '/classes/Users.php';
require_once dirname(__DIR__, 2) . '/access-token/seguridad/ActivationTokenService.php';

$userId = $_POST['user_id'] ?? null;
$password = $_POST['password'] ?? '';
$password2 = $_POST['password2'] ?? '';
$token = $_POST['token'] ?? '';
$email = $_POST['email'] ?? '';

header('Content-Type: application/json'); // Devolver JSON

if (!$userId || !$token || !$password || !$password2) {
    echo json_encode([
        'success' => false,
        'message' => 'Faltan datos requeridos.'
    ]);
    exit;
}

if ($password !== $password2) {
    echo json_encode([
        'success' => false,
        'message' => 'Las contraseñas no coinciden.'
    ]);
    exit;
}

try {
    $user = new Users();
    $result = $user->update_user_password($userId, $password);

    $tokenService = new ActivationTokenService();
    $tokenService->deleteToken($token);

    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Cuenta activada correctamente. Ya puedes iniciar sesión.',
            'email' => $email
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error al activar la cuenta. Intenta nuevamente.'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Ocurrió un error: ' . $e->getMessage()
    ]);
}
