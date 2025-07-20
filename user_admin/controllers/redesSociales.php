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
        $socialData = $_POST;

        // Verifica que getMaxOrderForCompany() devuelva un valor numérico
        $maxOrder = $socials->getMaxOrderForCompany();
        $newOrder = $maxOrder + 1;

        $result = $socials->agregarRedSocial(
            $socialData['social_network'],
            $socialData['social_url'],
            $newOrder
        );

        echo json_encode(['success' => $result['success'], 'message' => $result['message'] ?? null, 'error' => $result['error'] ?? null]);
        exit;
    } elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        $data = json_decode(file_get_contents('php://input'), true);

        if (isset($data['order'])) {
            // Actualizar el orden de múltiples redes sociales
            foreach ($data['order'] as $item) {
                $socials->updateSocialOrder($item['id'], $item['order']);
            }
            echo json_encode(['success' => true, 'message' => 'Orden actualizado con éxito.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Datos de orden no proporcionados.']);
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        $deleteData = json_decode(file_get_contents("php://input"), true);

        if (isset($deleteData['id'])) {
            $socialId = $deleteData['id'];
            $socials->deleteSocial($socialId);
            echo json_encode(['success' => true, 'message' => 'Red social eliminada exitosamente.']);
        }
    } else {
        // Obtener las redes sociales ordenadas por el campo 'orden'
        $redes = $socials->obtenerRedesSociales();
        echo json_encode(['success' => true, 'data' => $redes]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Error al procesar la solicitud: ' . $e->getMessage()]);
}
