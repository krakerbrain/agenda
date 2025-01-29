<?php
require_once dirname(__DIR__, 2) . '/classes/CompanyModel.php';
require_once dirname(__DIR__, 2) . '/classes/Services.php';
require_once dirname(__DIR__, 2) . '/classes/Schedules.php';
require_once dirname(__DIR__, 2) . '/classes/Appointments.php';

// Obtener los datos enviados en formato JSON desde el cliente
// Obtener datos enviados por el cliente
$data = json_decode(file_get_contents('php://input'), true);
$service_id = $data['service_id'];
$company_id = $data['company_id'];
$today = new DateTime();

$companyModel = new CompanyModel();
$services = new Services($company_id);
$schedules = new Schedules($company_id);
$appointmentsData = new Appointments($company_id);

$company = $companyModel->getCompanyCalendarData($company_id);
$scheduleDays = $schedules->getEnabledSchedulesDays();
$service = $services->getAvailableServiceDays($service_id);
$serviceDuration = $service['duration']; // En minutos

// Suponiendo que fixed_start_date está en formato 'Y-m-d' en la base de datos
$fixed_start_day = new DateTime($company['fixed_start_date']);
$calendar_days_available = $company['calendar_mode'] === 'corrido' ? $company['calendar_days_available'] : $company['fixed_duration'];

// Establecer las fechas de inicio y fin
$today = new DateTime("2025-01-19");
$start_date = $today; // Establece la fecha de inicio como fixed_start_day
$end_date = $company['calendar_mode'] === 'corrido' ? clone $start_date : $fixed_start_day;
$end_date->modify('+' . $calendar_days_available . ' days');

$allAppointments = $appointmentsData->getAppointmentsByDateRange(
    $company_id,
    $start_date->format('Y-m-d'),
    $end_date->format('Y-m-d')
);

$appointmentsByDay = [];
foreach ($allAppointments as $appointment) {
    $appointmentsByDay[$appointment['date']][] = $appointment;
}

$available_days = [];
$availableServiceDays = explode(',', $service['available_days']);
$currentDate = clone $start_date; // Comienza desde start_date

while ($currentDate <= $end_date) {
    $dayOfWeek = $currentDate->format('N');

    // Si el día de la semana no está en los días disponibles del servicio, pasamos al siguiente día
    if (!in_array($dayOfWeek, $availableServiceDays)) {
        $currentDate->modify('+1 day');
        continue;
    }

    // Si la fecha actual es igual a la fecha de hoy, pasamos al siguiente día
    if ($currentDate->format('Y-m-d') == $today->format('Y-m-d')) {
        $currentDate->modify('+1 day');
        continue;
    }

    // Si el día de la semana no está en los días de trabajo, pasamos al siguiente día
    $schedule = array_filter($scheduleDays, function ($day) use ($dayOfWeek) {
        return $day['day_id'] == $dayOfWeek;
    });


    if (empty($schedule)) {
        $currentDate->modify('+1 day');
        continue;
    }

    $schedule = reset($schedule);
    $workStart = new DateTime($schedule['work_start']);
    $workEnd = new DateTime($schedule['work_end']);
    $breakStart = new DateTime($schedule['break_start']);
    $breakEnd = new DateTime($schedule['break_end']);

    $appointmentsForDay = $appointmentsByDay[$currentDate->format('Y-m-d')] ?? [];
    $morningReserved = 0;
    $afternoonReserved = 0;

    foreach ($appointmentsForDay as $appointment) {
        $start = new DateTime($appointment['start_time']);
        $end = new DateTime($appointment['end_time']);

        if ($end <= $breakStart) {
            $morningReserved += ($end->getTimestamp() - $start->getTimestamp()) / 60;
        } elseif ($start >= $breakEnd) {
            $afternoonReserved += ($end->getTimestamp() - $start->getTimestamp()) / 60;
        } else {
            $morningOverlap = min($breakStart->getTimestamp(), $end->getTimestamp()) - $start->getTimestamp();
            $afternoonOverlap = $end->getTimestamp() - max($breakEnd->getTimestamp(), $start->getTimestamp());
            $morningReserved += max(0, $morningOverlap / 60);
            $afternoonReserved += max(0, $afternoonOverlap / 60);
        }
    }

    $morningAvailable = (($breakStart->getTimestamp() - $workStart->getTimestamp()) / 60) - $morningReserved;
    $afternoonAvailable = (($workEnd->getTimestamp() - $breakEnd->getTimestamp()) / 60) - $afternoonReserved;

    if ($serviceDuration <= $morningAvailable || $serviceDuration <= $afternoonAvailable) {
        $available_days[] = $currentDate->format('Y-m-d');
    } elseif ($serviceDuration <= ($morningAvailable + $afternoonAvailable)) {
        $available_days[] = $currentDate->format('Y-m-d');
    }

    $currentDate->modify('+1 day'); // Avanza al siguiente día
}


echo json_encode(['success' => true, 'available_days' => $available_days, 'calendar_mode' => $company['calendar_mode']]);
