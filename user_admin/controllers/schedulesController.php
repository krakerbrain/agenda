<?php
require_once dirname(__DIR__, 2) . '/configs/init.php';
require_once dirname(__DIR__, 2) . '/access-token/seguridad/JWTAuth.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';
require_once dirname(__DIR__, 2) . '/classes/Schedules.php';

$baseUrl = ConfigUrl::get();
$auth = new JWTAuth();
$datosUsuario = $auth->validarTokenUsuario();


try {
    $company_id = $datosUsuario['company_id'];
    $user_id = $datosUsuario['user_id'];
    $schedules = new Schedules($company_id, $user_id);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $postData = json_decode(file_get_contents("php://input"), true);

        if (isset($postData['action']) && $postData['action'] === 'remove_break') {
            $scheduleId = $postData['scheduleId'];
            $schedules->removeBreakTime($scheduleId);
            echo json_encode(['success' => true, 'message' => 'Hora de descanso eliminada exitosamente.']);
        } else if (isset($_POST['copy_from_monday'])) {
            $schedulesData = $_POST['schedule'];
            $schedules->copyMondayToAllDays($schedulesData['Lunes']);
            echo json_encode(['success' => true, 'message' => 'Horarios copiados exitosamente.']);
        } else {
            // Procesar la data del formulario
            $schedulesData = $_POST; // Asume que estás enviando los datos como application/x-www-form-urlencoded
            $schedules->saveSchedules($schedulesData);
            echo json_encode(['success' => true, 'message' => 'Horarios guardados exitosamente.']);
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Obtener los horarios y devolver el JSON
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $schedules->getSchedules()]);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Connection failed: ' . $e->getMessage()]);
}
