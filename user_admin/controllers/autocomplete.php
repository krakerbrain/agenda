<?php
require_once dirname(__DIR__, 2) . '/configs/init.php';
require_once dirname(__DIR__, 2) . '/access-token/seguridad/JWTAuth.php';
require_once dirname(__DIR__, 2) . '/classes/Appointments.php';
require_once dirname(__DIR__, 2) . '/classes/UniqueEvents.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';

$baseUrl = ConfigUrl::get();
$auth = new JWTAuth();
$datosUsuario = $auth->validarTokenUsuario();
$data = null;
try {
    $company_id = $datosUsuario['company_id'];

    // Modificar el controlador para manejar las búsquedas
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $status = isset($_GET['status']) ? $_GET['status'] : 'all';
        $service = isset($_GET['service']) ? $_GET['service'] : '';
        $name = isset($_GET['name']) ? $_GET['name'] : '';
        $phone = isset($_GET['phone']) ? $_GET['phone'] : '';
        $email = isset($_GET['mail']) ? $_GET['mail'] : '';
        $date = isset($_GET['date']) ? $_GET['date'] : '';
        $hour = isset($_GET['hour']) ? $_GET['hour'] : '';
        $tab = isset($_GET['tab']) ? $_GET['tab'] : '';


        // Filtrar los datos basados en los parámetros
        if ($tab != 'events') {
            $appointments = new Appointments;
            $data = $appointments->searchAppointments($company_id, $status, $service, $name, $phone, $email, $date, $hour, $tab);
        } else {
            $events = new UniqueEvents;
            $data = $events->searchEventInscriptions($company_id, $status, $service, $name, $phone, $email, $date, $hour);
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $data]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Error al procesar la solicitud: ' . $e->getMessage()]);
}
