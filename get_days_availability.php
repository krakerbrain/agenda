<?php
require_once __DIR__ . '/classes/DatabaseSessionManager.php';
$manager = new DatabaseSessionManager();
$conn = $manager->getDB();

$data = json_decode(file_get_contents('php://input'), true);
$service_id = $data['service_id'];
$calendar_days_available = $data['calendar_days_available'];
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

// Obtener días laborales y fechas bloqueadas
$work_days = explode(',', $company['work_days']);
$blocked_dates = explode(',', $company['blocked_dates']);

// Mapeo de días de la semana
$work_days_map = [
    "Sunday" => 0,
    "Monday" => 1,
    "Tuesday" => 2,
    "Wednesday" => 3,
    "Thursday" => 4,
    "Friday" => 5,
    "Saturday" => 6
];

// Convertir work_days a números
$work_days_numeric = array_map(function ($day) use ($work_days_map) {
    return $work_days_map[$day];
}, $work_days);

// Rango de fechas (hoy + 30 días)

$start_date = new DateTime();
$end_date = new DateTime();
$end_date->modify('+' . $calendar_days_available . ' days');

// Obtener todas las citas en el rango de fechas
$sql_appointments = $conn->prepare("
    SELECT date, start_time, end_time 
    FROM appointments 
    WHERE company_id = :company_id 
    AND date BETWEEN :start_date AND :end_date
");
$sql_appointments->bindValue(':company_id', $company_id);
$sql_appointments->bindValue(':start_date', $start_date->format('Y-m-d'));
$sql_appointments->bindValue(':end_date', $end_date->format('Y-m-d'));
$sql_appointments->execute();
$appointments = $sql_appointments->fetchAll(PDO::FETCH_ASSOC);

// Organizar citas por fecha en un array temporal
$appointments_by_date = [];
foreach ($appointments as $appointment) {
    $appointments_by_date[$appointment['date']][] = $appointment;
}

$interval = new DateInterval('P1D');
$daterange = new DatePeriod($start_date, $interval, $end_date);

$available_days = [];

foreach ($daterange as $date) {
    $day_of_week = $date->format('w');
    $date_str = $date->format('Y-m-d');

    if (in_array($day_of_week, $work_days_numeric) && !in_array($date_str, $blocked_dates)) {
        // Obtener horarios disponibles para el día
        $work_start = new DateTime($company['work_start']);
        $work_end = new DateTime($company['work_end']);
        $break_start = new DateTime($company['break_start']);
        $break_end = new DateTime($company['break_end']);

        // Obtener citas reservadas para la fecha seleccionada
        $day_appointments = isset($appointments_by_date[$date_str]) ? $appointments_by_date[$date_str] : [];

        // Calcular la duración disponible antes del descanso
        $duration_before_break = $work_start->diff($break_start);
        $minutes_before_break = $duration_before_break->h * 60 + $duration_before_break->i;

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

        // Filtrar horas disponibles eliminando las que ya están reservadas
        foreach ($day_appointments as $appointment) {
            $appointment_start_time = $date_str . ' ' . $appointment['start_time'];
            $appointment_end_time = $date_str . ' ' . $appointment['end_time'];

            $appointment_start = new DateTime($appointment_start_time);
            $appointment_end = new DateTime($appointment_end_time);

            foreach ($available_times as $key => $time_range) {
                $range_start = new DateTime($date_str . ' ' . $time_range['start']);
                $range_end = new DateTime($date_str . ' ' . $time_range['end']);

                if (($range_start < $appointment_end && $range_end > $appointment_start)) {
                    unset($available_times[$key]);
                }
            }
        }

        $available_times = array_values($available_times);

        if (!empty($available_times)) {
            $available_days[] = $date_str;
        }
    }
}

echo json_encode(['success' => true, 'available_days' => $available_days]);
