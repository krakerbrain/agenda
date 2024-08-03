<?php
require_once dirname(__DIR__, 2) . '/classes/DatabaseSessionManager.php';
$manager = new DatabaseSessionManager();
$conn = $manager->getDB();

$data = json_decode(file_get_contents('php://input'), true);
$date = $data['date'];
$service_id = $data['service_id'];
$company_id = $data['company_id'];

// Obtener datos de la empresa
$sql = $conn->prepare("SELECT * FROM companies WHERE id = :company_id AND is_active = 1");
$sql->bindParam(':company_id', $company_id);
$sql->execute();
$company = $sql->fetch(PDO::FETCH_ASSOC);

if (!$company) {
    echo json_encode(['success' => false, 'message' => 'Empresa no encontrada o inactiva.']);
    exit;
}

// Obtener la duración del servicio
$sql_service = $conn->prepare("SELECT duration FROM services WHERE id = :service_id");
$sql_service->bindParam(':service_id', $service_id);
$sql_service->execute();
$service = $sql_service->fetch(PDO::FETCH_ASSOC);

if (!$service) {
    echo json_encode(['success' => false, 'message' => 'Servicio no encontrado.']);
    exit;
}

// Convertir la duración del servicio a minutos
$service_duration = floatval($service['duration']);
$service_duration_minutes = (int)($service_duration * 60);

// Calcular day_id basado en la fecha seleccionada
$dateObj = new DateTime($date);
$day_id = (int)$dateObj->format('N'); // ISO-8601 (1=Lunes, 7=Domingo)

// Obtener horas de trabajo y descanso desde company_schedules
$sql_schedules = $conn->prepare("
    SELECT work_start, work_end, break_start, break_end 
    FROM company_schedules 
    WHERE company_id = :company_id AND day_id = :day_id AND is_enabled = 1
");
$sql_schedules->bindParam(':company_id', $company_id);
$sql_schedules->bindParam(':day_id', $day_id);
$sql_schedules->execute();
$schedule = $sql_schedules->fetch(PDO::FETCH_ASSOC);

if (!$schedule) {
    echo json_encode(['success' => false, 'message' => 'No hay horario de trabajo definido para esta fecha.']);
    exit;
}

$work_start = new DateTime($schedule['work_start']);
$work_end = new DateTime($schedule['work_end']);
$break_start = new DateTime($schedule['break_start']);
$break_end = new DateTime($schedule['break_end']);

// Calcular la duración disponible antes del descanso
$duration_before_break = $work_start->diff($break_start);
$minutes_before_break = $duration_before_break->h * 60 + $duration_before_break->i;

// Crear rangos de tiempo disponibles
$available_times = [];
$current_time = clone $work_start;

// Si la duración del servicio es mayor que el tiempo disponible antes del descanso, saltar el descanso
if ($service_duration_minutes > $minutes_before_break) {
    $end_time = clone $current_time;
    $end_time->add(new DateInterval('PT' . $service_duration_minutes . 'M'));

    if ($end_time <= $work_end) {
        $available_times[] = [
            'start' => $current_time->format('H:i'),
            'end' => $end_time->format('H:i')
        ];
    } else {
        echo json_encode(['success' => false, 'message' => 'La duración del servicio excede el horario laboral.']);
        exit;
    }
} else {
    // Calcular bloques de tiempo normalmente si el servicio cabe antes del descanso
    while ($current_time < $work_end) {
        $end_time = clone $current_time;
        $end_time->add(new DateInterval('PT' . $service_duration_minutes . 'M'));

        // Validar que el bloque de tiempo sea suficientemente largo y no caiga en el período de descanso
        if ($current_time < $break_start && $end_time > $break_start) {
            if (($break_start->getTimestamp() - $current_time->getTimestamp()) >= $service_duration_minutes * 60) {
                $available_times[] = [
                    'start' => $current_time->format('H:i'),
                    'end' => $break_start->format('H:i')
                ];
            }
            // Saltar el periodo de descanso
            $current_time = clone $break_end;
        } elseif ($current_time >= $break_start && $current_time < $break_end) {
            // Saltar el periodo de descanso
            $current_time = clone $break_end;
        } elseif ($end_time <= $work_end) {
            // Añadir el bloque de tiempo si cabe en el horario de trabajo
            $block_duration = $current_time->diff($end_time);
            $block_duration_minutes = $block_duration->h * 60 + $block_duration->i;

            if ($block_duration_minutes >= $service_duration_minutes) {
                $available_times[] = [
                    'start' => $current_time->format('H:i'),
                    'end' => $end_time->format('H:i')
                ];
            }
            $current_time->add(new DateInterval('PT' . $service_duration_minutes . 'M'));
        } else {
            // Si el bloque de tiempo excede las horas de trabajo, romper el bucle
            break;
        }
    }
}

// Obtener citas reservadas para la fecha seleccionada
$sql_appointments = $conn->prepare("SELECT start_time, end_time FROM appointments WHERE company_id = ? AND date = ?");
$sql_appointments->execute([$company['id'], $date]);
$appointments = $sql_appointments->fetchAll(PDO::FETCH_ASSOC);

// Filtrar horas disponibles eliminando las que ya están reservadas
foreach ($appointments as $appointment) {
    // Combina la fecha con las horas de inicio y fin
    $appointment_start_time = $date . ' ' . $appointment['start_time'];
    $appointment_end_time = $date . ' ' . $appointment['end_time'];

    // Crea objetos DateTime
    $appointment_start = new DateTime($appointment_start_time);
    $appointment_end = new DateTime($appointment_end_time);

    foreach ($available_times as $key => $time_range) {
        $range_start = new DateTime($date . ' ' . $time_range['start']);
        $range_end = new DateTime($date . ' ' . $time_range['end']);

        if (($range_start < $appointment_end && $range_end > $appointment_start)) {
            unset($available_times[$key]);
        }
    }
}

// Reindexar el array para eliminar huecos
$available_times = array_values($available_times);

if (empty($available_times)) {
    echo json_encode(['success' => false, 'message' => 'No hay horas disponibles para la fecha seleccionada.']);
} else {
    echo json_encode(['success' => true, 'available_times' => $available_times]);
}
