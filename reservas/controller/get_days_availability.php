<?php
require_once dirname(__DIR__, 2) . '/classes/CompanyModel.php';
require_once dirname(__DIR__, 2) . '/classes/Services.php';
require_once dirname(__DIR__, 2) . '/classes/Schedules.php';
require_once dirname(__DIR__, 2) . '/classes/Appointments.php';

// Obtener los datos enviados en formato JSON desde el cliente
$data = json_decode(file_get_contents('php://input'), true);

// Extraer los datos necesarios del cliente
$service_id = $data['service_id'];
$calendar_days_available = $data['calendar_days_available']; // Número de días a evaluar
$company_id = $data['company_id'];
$today = new DateTime(); // Fecha actual

// Instanciar modelos necesarios
$companyModel = new CompanyModel();
$services = new Services($company_id);
$schedules = new Schedules($company_id);
$appointmentsData = new Appointments($company_id);

// Obtener configuración del calendario de la empresa
$company = $companyModel->getCompanyCalendarData($company_id);

// 1. Obtener horarios habilitados para la peluquería
$scheduleDays = $schedules->getEnabledSchedulesDays();

// 2. Obtener la duración del servicio solicitado
$service = $services->getAvailableServiceDays($service_id);
$serviceDuration = $service['duration']; // Duración en horas

// 3. Obtener todas las citas dentro del rango de fechas especificado
$start_date = (clone $today)->modify("+1 days"); // Comenzar desde mañana
$end_date = (clone $today)->modify("+$calendar_days_available days"); // Fecha final basada en el rango

$allAppointments = $appointmentsData->getAppointmentsByDateRange(
    $company_id,
    $start_date->format('Y-m-d'),
    $end_date->format('Y-m-d')
);

// 4. Agrupar las citas por día para procesarlas más fácilmente
$appointmentsByDay = [];
foreach ($allAppointments as $appointment) {
    $appointmentsByDay[$appointment['date']][] = $appointment;
}

// 5. Evaluar la disponibilidad de días dentro del rango especificado
$available_days = [];
for ($i = 0; $i < $calendar_days_available; $i++) {
    $currentDate = (clone $today)->modify("+$i days"); // Fecha actual en el rango
    $dayOfWeek = $currentDate->format('N'); // Día de la semana (1=Lunes, 7=Domingo)

    // Saltar el día si coincide con la fecha actual
    if ($currentDate->format('Y-m-d') == $today->format('Y-m-d')) {
        continue;
    }

    // Verificar si el día actual está habilitado en el horario de trabajo
    $schedule = array_filter($scheduleDays, function ($day) use ($dayOfWeek) {
        return $day['day_id'] == $dayOfWeek; // Coincidencia por día de la semana
    });

    if (empty($schedule)) {
        continue; // Día no habilitado, pasar al siguiente
    }

    $schedule = reset($schedule); // Obtener el horario del día actual
    $workStart = new DateTime($schedule['work_start']); // Hora de inicio del trabajo
    $workEnd = new DateTime($schedule['work_end']); // Hora de fin del trabajo
    $breakStart = new DateTime($schedule['break_start']); // Inicio de la pausa
    $breakEnd = new DateTime($schedule['break_end']); // Fin de la pausa

    // Obtener las citas del día actual
    $appointmentsForDay = $appointmentsByDay[$currentDate->format('Y-m-d')] ?? [];

    // Calcular el tiempo ocupado en la mañana y en la tarde
    $morningReserved = 0;
    $afternoonReserved = 0;

    foreach ($appointmentsForDay as $appointment) {
        $start = new DateTime($appointment['start_time']);
        $end = new DateTime($appointment['end_time']);

        if ($end <= $breakStart) {
            // La cita está completamente en el horario de la mañana
            $morningReserved += ($end->getTimestamp() - $start->getTimestamp()) / 3600;
        } elseif ($start >= $breakEnd) {
            // La cita está completamente en el horario de la tarde
            $afternoonReserved += ($end->getTimestamp() - $start->getTimestamp()) / 3600;
        } else {
            // La cita cruza entre la mañana y la tarde, dividir el tiempo
            $morningOverlap = min($breakStart->getTimestamp(), $end->getTimestamp()) - $start->getTimestamp();
            $afternoonOverlap = $end->getTimestamp() - max($breakEnd->getTimestamp(), $start->getTimestamp());
            $morningReserved += max(0, $morningOverlap / 3600); // Sumar horas de la mañana
            $afternoonReserved += max(0, $afternoonOverlap / 3600); // Sumar horas de la tarde
        }
    }

    // Calcular disponibilidad restante en la mañana y la tarde
    $morningAvailable = ($breakStart->getTimestamp() - $workStart->getTimestamp()) / 3600 - $morningReserved;
    $afternoonAvailable = ($workEnd->getTimestamp() - $breakEnd->getTimestamp()) / 3600 - $afternoonReserved;

    // Verificar si hay espacio suficiente para el servicio
    if ($serviceDuration <= $morningAvailable || $serviceDuration <= $afternoonAvailable) {
        // Hay suficiente tiempo en la mañana o en la tarde
        $available_days[] = $currentDate->format('Y-m-d');
    } elseif ($serviceDuration <= ($morningAvailable + $afternoonAvailable)) {
        // Combinando mañana y tarde hay tiempo suficiente
        $available_days[] = $currentDate->format('Y-m-d');
    }
}

// 6. Devolver los días disponibles en formato JSON
echo json_encode(['success' => true, 'available_days' => $available_days, 'calendar_mode' => $company['calendar_mode']]);
