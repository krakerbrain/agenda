<?php
require_once dirname(__DIR__, 2) . '/classes/Users.php';
require_once dirname(__DIR__, 2) . '/access-token/seguridad/ActivationTokenService.php';

$userId = $_POST['user_id'] ?? null;
$password = $_POST['password'] ?? '';
$password2 = $_POST['password2'] ?? '';
$token = $_POST['token'] ?? '';

if (!$userId || !$token || !$password || !$password2) {
    die("Faltan datos requeridos.");
}

if ($password !== $password2) {
    die("Las contraseñas no coinciden.");
}

try {
    $user = new Users();
    // Actualizar la contraseña
    $result = $user->update_user_password($userId, $password);

    // Eliminar el token
    $tokenService = new ActivationTokenService();
    $tokenService->deleteToken($token);

    if ($result) {
        echo "✅ Cuenta activada correctamente. Ya puedes iniciar sesión.";
        // Puedes redirigir aquí si deseas: header("Location: login.php");
    } else {
        echo "❌ Error al activar la cuenta. Intenta nuevamente.";
    }
} catch (Exception $e) {
    echo "❌ Ocurrió un error: " . $e->getMessage();
}
