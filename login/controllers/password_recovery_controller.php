<?php
require_once dirname(__DIR__, 2) . '/configs/init.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';
require_once dirname(__DIR__, 2) . '/classes/Database.php';
require_once dirname(__DIR__, 2) . '/classes/Users.php';
require_once dirname(__DIR__, 2) . '/classes/EmailTemplate.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => ''];
$baseUrl = ConfigUrl::get();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = $_POST['email'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = "Formato de correo incorrecto.";
        echo json_encode($response);
        exit;
    }

    try {
        $db = new Database();
        $user = new Users($db);
        $userData = $user->get_user_by_email($email);

        if ($userData) {
            // Generar token de recuperación
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

            if ($user->save_password_reset_token($userData['id'], $token, $expires)) {
                $emailTemplate = new EmailTemplate($db);

                $mailData = [
                    'reset_link' => $baseUrl . 'login/reset-password.php?token=' . $token,
                    'user_name' => $userData['name'],
                    'user_email' => $email,
                    'expiration_time' => '1 hora'
                ];

                // Flujo diferenciado para superadmin
                if ($userData['role_id'] == 1) {
                    $result = $emailTemplate->sendSuperAdminPasswordRecovery($mailData);
                } else {
                    $mailData['company_id'] = $userData['company_id'];
                    $result = $emailTemplate->sendCompanyPasswordRecovery($mailData);
                }

                if ($result['success']) {
                    $response['success'] = true;
                    $response['message'] = "Se ha enviado un enlace de recuperación a tu correo.";
                } else {
                    $response['message'] = $result['message'] ?? "Error al enviar el correo.";
                }
            } else {
                $response['message'] = "Error al generar el token de recuperación.";
            }
        } else {
            $response['message'] = "No existe una cuenta con este correo.";
        }
    } catch (PDOException $e) {
        $response['message'] = "Error de conexión: " . $e->getMessage();
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }
} else {
    $response['message'] = "Solicitud inválida.";
}

echo json_encode($response);
