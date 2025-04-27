<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTAuth
{
    private $key;
    private $timeout;
    private $baseUrl;

    public function __construct()
    {
        $this->key = $_ENV['JWTKEY']; // Clave secreta para JWT
        $this->timeout = 1800; // Tiempo de inactividad permitido en segundos (30 minutos)
        $this->baseUrl = ConfigUrl::get(); // Base URL para redirigir en caso de fallo
    }

    // Función para generar token JWT
    public function generarToken($company_id, $role_id, $user_id)
    {
        $payload = [
            "company_id" => $company_id,
            "role_id" => $role_id, // Incluimos el rol en el payload
            "user_id" => $user_id, // Incluimos el ID de usuario en el payload
            "last_activity" => time()
        ];

        $jwt = JWT::encode($payload, $this->key, 'HS256');

        // Configurar la cookie con el token
        setcookie("jwt", $jwt, [
            'expires' => 0,
            'path' => '/',
            'secure' => false,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    }

    // Función para validar el token
    public function validarTokenUsuario()
    {
        if (isset($_COOKIE['jwt'])) {
            try {
                $decoded = JWT::decode($_COOKIE['jwt'], new Key($this->key, 'HS256'));

                if (is_object($decoded)) {
                    $company_id = $decoded->company_id;
                    $role_id = $decoded->role_id;
                    $user_id = $decoded->user_id; // Obtener el ID de usuario
                    $last_activity = $decoded->last_activity;

                    // Verificar la inactividad
                    if ((time() - $last_activity) > $this->timeout) {
                        $this->invalidarSesion();
                    } else {
                        // Regenerar el token para extender la sesión
                        $this->generarToken($company_id, $role_id, $user_id);
                        return ['company_id' => $company_id, 'role_id' => $role_id, 'user_id' => $user_id];
                    }
                }
            } catch (Exception $e) {
                echo "Error JWT: " . $e->getMessage();
            }
        }

        header("Location: " . $this->baseUrl . "login/index.php");
        exit();
    }

    // Función para generar un token de cita
    public function generarTokenCita($company_id, $appointment_id)
    {
        $payload = [
            "company_id" => $company_id,
            "appointment_id" => $appointment_id,
            "created_at" => time()
        ];

        return JWT::encode($payload, $this->key, 'HS256');
    }

    // Validar token de cita
    public function validarTokenCita($token)
    {
        try {
            $decoded = JWT::decode($token, new Key($this->key, 'HS256'));

            if (is_object($decoded) && isset($decoded->appointment_id)) {
                return [
                    'valid' => true,
                    'company_id' => $decoded->company_id,
                    'appointment_id' => $decoded->appointment_id,
                    'created_at' => $decoded->created_at
                ];
            }
        } catch (Exception $e) {
            return [
                'valid' => false,
                'error' => $e->getMessage()
            ];
        }

        return ['valid' => false, 'error' => 'Token inválido'];
    }

    // Función para invalidar sesión
    private function invalidarSesion()
    {
        setcookie("jwt", "", time() - 3600, "/", "", false, true);
        header("Location: " . $this->baseUrl . "login/index.php");
        exit();
    }
}
