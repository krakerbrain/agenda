<?php
require_once dirname(__DIR__, 2) . '/configs/init.php';
require_once dirname(__DIR__, 2) . '/classes/Database.php';
require_once dirname(__DIR__, 2) . '/classes/Users.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (empty($token) || empty($newPassword) || empty($confirmPassword)) {
        $response['message'] = "Todos los campos son requeridos.";
    } elseif ($newPassword !== $confirmPassword) {
        $response['message'] = "Las contraseñas no coinciden.";
    } elseif (strlen($newPassword) < 8) {
        $response['message'] = "La contraseña debe tener al menos 8 caracteres.";
    } else {
        try {
            $db = new Database();
            $user = new Users($db);
            $tokenData = $user->validate_reset_token($token);

            if ($tokenData && strtotime($tokenData['expires_at']) > time()) {
                // Verificar que el token pertenece al usuario
                $userTokenValid = $user->verify_user_reset_token($tokenData['user_id'], $token);

                if ($userTokenValid) {
                    // Actualizar contraseña
                    if ($user->update_user_password($tokenData['user_id'], $newPassword)) {
                        // Invalidar el token después de usarlo
                        $user->invalidate_reset_token($token);
                        $response['success'] = true;
                        $response['message'] = "Contraseña actualizada correctamente. Redirigiendo al login...";
                    } else {
                        $response['message'] = "Error al actualizar la contraseña.";
                    }
                } else {
                    $response['message'] = "Token no válido para este usuario.";
                }
            } else {
                $response['message'] = "Token inválido o expirado.";
            }
        } catch (PDOException $e) {
            $response['message'] = "Error de conexión: " . $e->getMessage();
        }
    }
} else {
    $response['message'] = "Método no permitido.";
}

echo json_encode($response);
