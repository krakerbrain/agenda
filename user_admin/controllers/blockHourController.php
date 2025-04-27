<?php
require_once dirname(__DIR__, 2) . '/configs/init.php';
require_once dirname(__DIR__, 2) . '/access-token/seguridad/JWTAuth.php';
require_once dirname(__DIR__, 2) . '/classes/Appointments.php'; // Clase Appointments
require_once dirname(__DIR__, 2) . '/classes/Schedules.php';   // Clase Schedules
require_once dirname(__DIR__, 2) . '/classes/Customers.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';

$baseUrl = ConfigUrl::get();
$auth = new JWTAuth();

try {
    // Validar token del usuario
    $datosUsuario = $auth->validarTokenUsuario();
    $company_id = $datosUsuario['company_id'];

    // Obtener datos enviados desde el frontend
    $data = json_decode(file_get_contents('php://input'), true);
    $blockDate = $data['date'] ?? null;
    $allDay = $data['all_day'] ?? false;
    $startHour = $data['start_hour'] ?? null;
    $endHour = $data['end_hour'] ?? null;
    $user_id = $data['user_id'] ?? $datosUsuario['user_id']; // Obtener el ID del usuario desde el token o desde la solicitud 

    // Validar que la fecha esté presente
    if (!$blockDate) {
        echo json_encode(['success' => false, 'message' => 'La fecha es obligatoria.']);
        exit;
    }

    $today = date('Y-m-d'); // Fecha actual en formato YYYY-MM-DD
    if ($blockDate < $today) {
        echo json_encode([
            'success' => false,
            'message' => 'No se puede bloquear una fecha pasada.'
        ]);
        exit; // Terminar la ejecución
    }

    // Instanciar clases necesarias
    $schedules = new Schedules($company_id, $user_id);
    $appointments = new Appointments();
    $customers = new Customers();

    // Validar horario habilitado en la fecha seleccionada
    $dayOfWeek = date('N', strtotime($blockDate)); // Obtener día de la semana (1 = lunes, 7 = domingo)
    $validation = $schedules->validateSelectedDate($dayOfWeek);

    if (!$validation['success']) {
        echo json_encode(['success' => false, 'message' => $validation['message']]);
        exit;
    }

    // Determinar rango de horas a validar
    $workStart = $validation['work_start'];
    $workEnd = $validation['work_end'];
    $startHour = $allDay ? $workStart : $startHour;
    $endHour = $allDay ? $workEnd : $endHour;

    // Validar conflictos con citas existentes
    $conflictingAppointments = $appointments->checkAppointments($company_id, $user_id, $blockDate, $startHour, $endHour);

    if (!empty($conflictingAppointments)) {
        echo json_encode([
            'success' => false,
            'message' => $allDay ?
                'No se puede bloquear el día completo, hay citas agendadas en esa fecha.' :
                'No se puede bloquear el rango de horas seleccionado, hay citas en conflicto.',
            'citas' => $conflictingAppointments,
        ]);
        exit;
    }

    // // Si no hay conflictos, permitir el bloqueo del día o rango
    if ($validation['success'] && empty($conflictingAppointments)) {
        // Obtener el cliente especial (se crea solo si no existe)
        $blockedCustomerId = $customers->getOrCreateBlockedDayCustomer($company_id);
        $blockData = [
            'company_id' => $company_id,
            'user_id' => $user_id,
            'customer_id' => $blockedCustomerId['id'],
            'date' => $blockDate,
            'start_time' => $startHour,
            'end_time' => $endHour,
        ];

        $result = $appointments->addBlockedDay($blockData);

        echo json_encode($result);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Error al procesar la solicitud: ' . $e->getMessage()]);
}
