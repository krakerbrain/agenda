<?php

require_once dirname(__DIR__, 2) . '/configs/init.php';
require_once dirname(__DIR__, 2) . '/access-token/seguridad/JWTAuth.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';
require_once dirname(__DIR__, 2) . '/classes/RedesSociales.php';
$baseUrl = ConfigUrl::get();

$auth = new JWTAuth();
$datosUsuario = $auth->validarTokenUsuario();


try {
    $company_id = $datosUsuario['company_id'];
    $socials = new RedesSociales($company_id);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);

        if (isset($data['action']) && $data['action'] === 'set_preferred') {
            // Procesar el cambio de la red preferida
            if (isset($data['id']) && isset($data['preferida'])) {
                $socialId = $data['id'];
                $socials->setPreferredSocial($socialId); // Implementa esta función en tu clase

                echo json_encode(['success' => true, 'message' => 'Red preferida actualizada con éxito.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al actualizar la red preferida.']);
            }
        } else {
            // Procesar la adición de una nueva red social
            $socialData = $_POST;
            $socials->agregarRedSocial($socialData['social_network'], $socialData['social_url']);
            echo json_encode(['success' => true, 'message' => 'Red social guardada exitosamente.']);
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        $deleteData = json_decode(file_get_contents("php://input"), true);

        if (isset($deleteData['id'])) {
            $socialId = $deleteData['id'];
            $socials->deleteSocial($socialId);
            echo json_encode(['success' => true, 'message' => 'Red social eliminada exitosamente.']);
        }
    } else {
        // Obtener las redes sociales y devolver el JSON
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $socials->obtenerRedesSociales()]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Error al procesar la solicitud: ' . $e->getMessage()]);
}
