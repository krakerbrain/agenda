<?php
require_once dirname(__DIR__, 2) . '/classes/Appointments.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';
require_once dirname(__DIR__, 2) . '/access-token/seguridad/jwt.php';
$baseUrl = ConfigUrl::get();
$datosUsuario = validarToken();
if (!$datosUsuario) {
    header("Location: " . $baseUrl . "login/index.php");
}

try {

    $company_id = $datosUsuario['company_id'];
    $appointments = new Appointments;

    if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data['id'])) {
            $result = $appointments->delete_appointment($data['id']);
            if ($result > 0) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Cita eliminada correctamente']);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'No se pudo eliminar la cita']);
            }
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'No se pudo eliminar la cita']);
        }
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $appointments->get_appointments($company_id)]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Error al procesar la solicitud: ' . $e->getMessage()]);
}
