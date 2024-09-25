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
    public function generarToken($company_id, $role_id)
    {
        $payload = [
            "company_id" => $company_id,
            "role_id" => $role_id, // Incluimos el rol en el payload
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
                    $last_activity = $decoded->last_activity;

                    // Verificar la inactividad
                    if ((time() - $last_activity) > $this->timeout) {
                        $this->invalidarSesion();
                    } else {
                        // Regenerar el token para extender la sesión
                        $this->generarToken($company_id, $role_id);
                        return ['company_id' => $company_id, 'role_id' => $role_id];
                    }
                }
            } catch (Exception $e) {
                echo "Error JWT: " . $e->getMessage();
            }
        }

        header("Location: " . $this->baseUrl . "login/index.php");
        exit();
    }

    // Función para invalidar sesión
    private function invalidarSesion()
    {
        setcookie("jwt", "", time() - 3600, "/", "", false, true);
        header("Location: " . $this->baseUrl . "login/index.php");
        exit();
    }
}
