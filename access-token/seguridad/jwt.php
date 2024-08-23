<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';
$baseUrl = ConfigUrl::get();

function generarTokenYConfigurarCookie($company_id)
{
    $key = $_ENV['JWTKEY'];

    $payload = array(
        "company_id" => $company_id,
        "last_activity" => time() // Tiempo de la última actividad
    );

    $jwt = JWT::encode($payload, $key, 'HS256');

    // Configurar la cookie sin fecha de expiración específica para que dure solo mientras dure la sesión
    setcookie("jwt", $jwt, [
        'expires' => 0,
        'path' => '/',
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
}

function validarToken()
{
    $baseUrl = ConfigUrl::get();
    if (isset($_COOKIE['jwt'])) {
        $key = $_ENV['JWTKEY'];
        $timeout = 1800; // Tiempo de inactividad permitido en segundos (30 minutos)

        try {
            $decoded = JWT::decode($_COOKIE['jwt'], new Key($key, 'HS256'));

            if (is_object($decoded)) {
                $company_id = $decoded->company_id;
                $last_activity = $decoded->last_activity;

                if ((time() - $last_activity) > $timeout) {
                    // Si ha pasado demasiado tiempo, invalida la sesión
                    setcookie("jwt", "", time() - 3600, "/", "", false, true); // Borrar la cookie
                    header("Location: " . $baseUrl . "user_admin/index.php"); // Redirigir al login
                    exit;
                } else {
                    // Actualizar la última actividad y regenerar el token
                    generarTokenYConfigurarCookie($company_id);
                    return ['company_id' => $company_id];
                }
            } else {
                header("Location: " . $baseUrl . "user_admin/index.php");
                return null;
            }
        } catch (Exception $e) {
            echo "Error JWT: " . $e->getMessage();
            return null;
        }
    }

    return null;
}

function generarTokenSuperUser()
{
    $key = $_ENV['JWTKEY'];

    $payload = array(
        "role" => "superadmin",
        "last_activity" => time() // Tiempo de la última actividad
    );

    $jwt = JWT::encode($payload, $key, 'HS256');

    // Configurar la cookie sin fecha de expiración específica para que dure solo mientras dure la sesión
    setcookie("superadmin_jwt", $jwt, [
        'expires' => 0,
        'path' => '/',
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
}

function validarTokenSuperUser()
{
    $baseUrl = ConfigUrl::get();
    if (isset($_COOKIE['superadmin_jwt'])) {
        $key = $_ENV['JWTKEY'];
        $timeout = 1800; // Tiempo de inactividad permitido en segundos (30 minutos)

        try {
            $decoded = JWT::decode($_COOKIE['superadmin_jwt'], new Key($key, 'HS256'));

            if (is_object($decoded) && $decoded->role === "superadmin") {
                $last_activity = $decoded->last_activity;

                if ((time() - $last_activity) > $timeout) {
                    // Si ha pasado demasiado tiempo, invalida la sesión
                    setcookie("superadmin_jwt", "", time() - 3600, "/", "", false, true); // Borrar la cookie
                    header("Location: " . $baseUrl . "user_admin/index.php"); // Redirigir al login
                    exit;
                } else {
                    // Actualizar la última actividad y regenerar el token
                    generarTokenSuperUser();
                    return true;
                }
            } else {
                header("Location: " . $baseUrl . "user_admin/index.php");
                return false;
            }
        } catch (Exception $e) {
            echo "Error JWT: " . $e->getMessage();
            return false;
        }
    }

    return false;
}
