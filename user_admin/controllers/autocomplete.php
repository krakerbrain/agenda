<?php
require_once dirname(__DIR__, 2) . '/configs/init.php';
require_once dirname(__DIR__, 2) . '/access-token/seguridad/JWTAuth.php';
require_once dirname(__DIR__, 2) . '/classes/Appointments.php';
require_once dirname(__DIR__, 2) . '/classes/Customers.php'; // Incluir la clase Customers
require_once dirname(__DIR__, 2) . '/classes/UniqueEvents.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';

$baseUrl = ConfigUrl::get();
$auth = new JWTAuth();
$datosUsuario = $auth->validarTokenUsuario();
$data = null;

try {
    $company_id = $datosUsuario['company_id'];

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $input = isset($_GET['input']) ? $_GET['input'] : '';
        $query = isset($_GET['query']) ? $_GET['query'] : '';
        $tab = isset($_GET['tab']) ? $_GET['tab'] : '';
        $status = isset($_GET['status']) ? $_GET['status'] : null;

        switch ($tab) {
            case 'customers':
                $customers = new Customers();
                $data = $customers->searchCustomers($company_id, $input, $query, $status);
                break;
            case 'events':
                $events = new UniqueEvents();
                $data = $events->searchEventInscriptions($company_id, $input, $query);
                break;
            default:
                $appointments = new Appointments();
                $data = $appointments->searchAppointments($company_id, $input, $query, $tab);
                break;
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $data]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Error al procesar la solicitud: ' . $e->getMessage()]);
}
