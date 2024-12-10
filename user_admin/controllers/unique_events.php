<?php
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';
require_once dirname(__DIR__, 2) . '/classes/UniqueEvents.php';
require_once dirname(__DIR__, 2) . '/classes/CompanyManager.php';
require_once dirname(__DIR__, 2) . '/access-token/seguridad/JWTAuth.php';

$auth = new JWTAuth();
$datosUsuario = $auth->validarTokenUsuario();

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['status']) && $_GET['status'] === 'inscriptions') {
    // Obtener inscripciones
    try {
        $company_id = $datosUsuario['company_id'];
        $uniqueEvents = new UniqueEvents();
        $inscriptions = $uniqueEvents->get_event_inscriptions($company_id);

        echo json_encode([
            'success' => true,
            'data' => $inscriptions
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} else {
    // Aquí irán las otras condiciones, como crear, listar o eliminar eventos


    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Crear evento
        try {
            $company_id = $datosUsuario['company_id'];
            $event_name = trim($_POST['event_name'] ?? '');
            $event_quota = trim($_POST['event_quota'] ?? '');
            $event_description = trim($_POST['event_description'] ?? '');
            $event_dates = $_POST['event_dates'] ?? [];
            $start_times = $_POST['start_time'] ?? [];
            $end_times = $_POST['end_time'] ?? [];

            if (!$company_id || empty($event_name) || empty($event_quota) || empty($event_dates) || empty($start_times) || empty($end_times)) {
                throw new Exception("Todos los campos son obligatorios.");
            }

            if (count($event_dates) !== count($start_times) || count($event_dates) !== count($end_times)) {
                throw new Exception("El número de fechas no coincide con el número de horas de inicio o fin.");
            }

            // Validar fechas y horas
            $validDates = [];
            $validStartTimes = [];
            $validEndTimes = [];

            foreach ($event_dates as $key => $date) {
                $formattedDate = DateTime::createFromFormat('Y-m-d', $date);
                if ($formattedDate && $formattedDate->format('Y-m-d') === $date) {
                    $validDates[] = $formattedDate->format('Y-m-d');
                } else {
                    throw new Exception("Formato de fecha inválido: $date.");
                }

                // Validar hora de inicio
                $startTime = DateTime::createFromFormat('H:i', $start_times[$key]);
                if (!$startTime) {
                    throw new Exception("Formato de hora de inicio inválido: {$start_times[$key]}.");
                } else {
                    $validStartTimes[] = $startTime->format('H:i');
                }

                // Validar hora de fin
                $endTime = DateTime::createFromFormat('H:i', $end_times[$key]);
                if (!$endTime) {
                    throw new Exception("Formato de hora de fin inválido: {$end_times[$key]}.");
                } else {
                    $validEndTimes[] = $endTime->format('H:i');
                }
            }

            // Agregar evento
            $uniqueEvents = new UniqueEvents();
            $data = [
                'company_id' => $company_id,
                'name' => $event_name,
                'cupo_maximo' => $event_quota,
                'description' => $event_description,
                'dates' => $validDates,
                'start_times' => $validStartTimes,
                'end_times' => $validEndTimes
            ];

            // Suponiendo que el método add_event() puede manejar las fechas y horas en estos arrays
            $result = $uniqueEvents->add_event($data);

            if (isset($result['error'])) {
                throw new Exception($result['error']);
            }

            echo json_encode([
                'success' => true,
                'message' => 'Evento creado exitosamente.',
                'event_id' => $result['event_id']
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Obtener eventos
        try {
            $company_id = $datosUsuario['company_id'];
            $uniqueEvents = new UniqueEvents();
            $events = $uniqueEvents->get_upcoming_events($company_id);


            $companyManager = new CompanyManager();
            $url = $companyManager->getCompanyCustomUrl($company_id);

            echo json_encode([
                'success' => true,
                'events' => $events,
                'url' => $url
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $event_id = $data['event_id'];

            if (isset($data['event_date']) && isset($data['start_time'])) {
                // Eliminar una fecha específica
                $event_date = $data['event_date'];
                $start_time = $data['start_time'];
                $uniqueEvents = new UniqueEvents();
                $uniqueEvents->delete_event_date($event_id, $event_date, $start_time);
                echo json_encode(['success' => true, 'message' => 'Fecha eliminada correctamente.']);
            } else {
                // Eliminar todo el evento
                $uniqueEvents = new UniqueEvents();
                $uniqueEvents->delete_event($event_id);
                echo json_encode(['success' => true, 'message' => 'Evento eliminado correctamente.']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Método no permitido.'
        ]);
    }
}
