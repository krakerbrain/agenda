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
        $tab = $_GET['tab'] ?? '';

        // Detectamos si es autocompletado (viene input+query) o búsqueda completa
        $isAutocomplete = isset($_GET['input']) && isset($_GET['query']);

        switch ($tab) {
            case 'customers':
                $customers = new Customers();
                $data = $customers->searchCustomers(
                    $company_id,
                    $_GET['input'] ?? '',
                    $_GET['query'] ?? '',
                    $_GET['status'] ?? null
                );
                break;

            case 'events':
                $events = new UniqueEvents();
                $data = $events->searchEventInscriptions(
                    $company_id,
                    $_GET['input'] ?? '',
                    $_GET['query'] ?? ''
                );
                break;

            default:
                $appointments = new Appointments();
                if ($isAutocomplete) {
                    // Mantener autocompletado: un solo campo
                    $data = $appointments->searchAppointments(
                        $company_id,
                        $_GET['input'] ?? '',
                        $_GET['query'] ?? '',
                        $tab
                    );
                } else {
                    // Búsqueda avanzada: varios filtros
                    $filters = [
                        'service' => $_GET['service'] ?? null,
                        'name'    => $_GET['name'] ?? null,
                        'phone'   => $_GET['phone'] ?? null,
                        'mail'    => $_GET['mail'] ?? null,
                        'date'    => $_GET['date'] ?? null,
                        'hour'    => $_GET['hour'] ?? null,
                        'status'  => $_GET['status'] ?? null,
                    ];
                    $data = $appointments->searchAppointmentsAdvanced($company_id, $filters, $tab);
                }
                break;
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $data]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Error al procesar la solicitud: ' . $e->getMessage()]);
}
