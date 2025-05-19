<?php
require_once dirname(__DIR__, 2) . '/configs/init.php';
require_once dirname(__DIR__, 2) . '/access-token/seguridad/JWTAuth.php';
require_once dirname(__DIR__, 2) . '/access-token/seguridad/ActivationTokenService.php';
require_once dirname(__DIR__, 2) . '/classes/Users.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => ''];

if (isset($_POST['usuario']) && isset($_POST['contrasenia'])) {
    $pass = $_POST['contrasenia'];
    $usuario = $_POST['usuario'];

    if (empty($pass) || empty($usuario)) {
        $response['message'] = "Debe llenar todos los campos.";
    } else {
        if (!preg_match('/^[\w.%+-]+@[A-Za-z0-9.-]+\.[A-Z]{2,}$/i', $usuario)) {
            $response['message'] = "Formato de correo incorrecto.";
        } else {
            try {
                $user = new Users();
                $datos = $user->get_user_for_login($usuario);

                if ($datos) {
                    $tokenVerificacion = hash('sha256', $datos['name'] . $usuario);
                    if (hash_equals($datos['token_sha256'], $tokenVerificacion)) {
                        if (password_verify($pass, $datos['password'])) {
                            $tokenService = new ActivationTokenService();
                            if ($tokenService->hasActiveToken($datos['user_id'])) {
                                $response['message'] = "Credenciales incorrectas."; // o un mensaje más claro si quieres
                            } else {
                                $auth = new JWTAuth();
                                $auth->generarToken($datos['company_id'], $datos['role_id'], $datos['user_id']);
                                $response['success'] = true;
                                $response['redirect'] = $_ENV['URL_LOGIN'];
                            }
                        } else {
                            $response['message'] = "Credenciales incorrectas.";
                        }
                    } else {
                        $response['message'] = "Token de verificación incorrecto.";
                    }
                } else {
                    $response['message'] = "Usuario no existe.";
                }
            } catch (PDOException $e) {
                $response['message'] = "Error de conexión: " . $e->getMessage();
            }
        }
    }
} else {
    $response['message'] = "Solicitud inválida.";
}

echo json_encode($response);
