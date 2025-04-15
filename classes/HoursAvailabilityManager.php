<?php

require_once dirname(__DIR__) . '/classes/CompanyManager.php';
require_once dirname(__DIR__) . '/classes/Services.php';
require_once dirname(__DIR__) . '/classes/Schedules.php';
require_once dirname(__DIR__) . '/classes/Appointments.php';

class HoursAvailabilityManager
{
    private $companyManager;
    private $services;
    private $schedules;
    private $appointments;
    // DEBUG MODE PARA USARLO EN CONSULTAS DESDE THUNDERBIRD O POSTMAN
    private $debugMode;
    private $user_id;

    public function __construct($company_id, $user_id, $debugMode = false)
    {
        $this->companyManager = new CompanyManager();
        $this->services = new Services($company_id, $user_id);
        $this->schedules = new Schedules($company_id, $user_id);
        $this->appointments = new Appointments();
        $this->company_id = $company_id;
        $this->user_id = $user_id;
        $this->debugMode = $debugMode;
    }

    public function getAvailableHours($date, $service_id)
    {
        // Validar que la empresa exista
        // if (!$this->companyManager->companyExists($company_id)) {
        //     return ['success' => false, 'message' => 'Empresa no encontrada o inactiva.'];
        // }

        $companyData = $this->companyManager->getCompanyTimeStep($this->company_id);

        if ($companyData) {
            $time_step = $companyData['time_step']; // Puede ser null o un valor definido
        } else {
            echo "La empresa no existe o no está activa.";
        }

        // Obtener duración del servicio
        $service = $this->services->getAvailableServiceDays($service_id);
        if (!$service) {
            return ['success' => false, 'message' => 'Servicio no encontrado.'];
        }
        $service_duration = $service['duration'];

        // Obtener horario del día
        $day_id = (new DateTime($date))->format('N');
        $schedule = $this->schedules->getScheduleByDay($day_id);
        if (!$schedule) {
            return ['success' => false, 'message' => 'No hay horario de trabajo definido para esta fecha.'];
        }

        // Extraer horarios
        $work_start = $schedule['work_start'];
        $work_end = $schedule['work_end'];
        $break_start = $schedule['break_start'];
        $break_end = $schedule['break_end'];

        // Generar horarios disponibles
        $available_slots = $this->generateTimeSlots($work_start, $work_end, $service_duration, $break_start, $break_end, $time_step);

        // Filtrar citas reservadas
        $reserved_appointments = $this->appointments->getAppointmentsByDate($this->company_id, $this->user_id, $date);
        $filtered_slots = $this->filterReservedSlots($available_slots, $reserved_appointments);

        if ($this->debugMode) {
            // Retornar los datos estructurados
            return [
                'success' => true,
                'available_slots' => $available_slots,
                'available_times' => $filtered_slots,
                'service_duration' => $service_duration / 60,
                'work_start' => $work_start,
                'work_end' => $work_end,
                'break_start' => $break_start,
                'break_end' => $break_end,
                'reserved_appointments' => $reserved_appointments,
                'time_step' => $time_step,
            ];
        }

        $horasDisponibles = array_map(function ($rango) {
            list($inicio, $fin) = explode(' - ', $rango); // Separar inicio y fin
            return $inicio; // Retornar solo la hora de inicio
        }, $filtered_slots);

        return [
            'success' => true,
            'available_times' => $horasDisponibles,
        ];
    }

    // private function calculateStep($duration)
    // {
    //     return ($duration % 30 == 0 && $duration % 60 != 0) ? 30 : 60;
    // }

    private function generateTimeSlots($horaInicio, $horaFin, $duracion, $break_start = null, $break_end = null, $time_step = null)
    {
        $rangos = [];

        // Convertimos las horas a timestamp
        $horaInicioTimestamp = strtotime($horaInicio);
        $horaFinTimestamp = strtotime($horaFin);
        $breakStartTimestamp = $break_start ? strtotime($break_start) : null;
        $breakEndTimestamp = $break_end ? strtotime($break_end) : null;

        // Definir el paso de tiempo (30 o 60 minutos según la duración)
        $paso = $time_step ?? $duracion;

        // Si el servicio dura 4 horas o más, se restringe su disponibilidad
        if ($duracion >= 240) {
            // Solo permitir reservas a la primera hora de la mañana o de la tarde
            if ($duracion >= 480) {
                // Servicios de 8 horas solo pueden comenzar a las 09:30
                $posiblesHorarios = [$horaInicioTimestamp];
            } else {
                // Servicios de 4 a 7 horas pueden empezar a las 09:30 o 15:00
                $posiblesHorarios = [$horaInicioTimestamp, $breakEndTimestamp];
            }

            foreach ($posiblesHorarios as $inicio) {
                $fin = $inicio + ($duracion * 60);
                if ($fin <= $horaFinTimestamp) {
                    $rangos[] = date('H:i', $inicio) . ' - ' . date('H:i', $fin);
                }
            }
        } else {
            if (!$breakStartTimestamp || !$breakEndTimestamp) {
                for ($inicio = $horaInicioTimestamp; $inicio < $horaFinTimestamp; $inicio += ($paso * 60)) {
                    $fin = $inicio + ($paso * 60);

                    // $finFormateado = date('H:i', $fin);
                    // $horaFinTimestampFormateado = date('H:i', $horaFinTimestamp);
                    // Evitar que el rango supere la hora de cierre
                    if ($fin > $horaFinTimestamp) {
                        break;
                    }

                    // Verificar si el servicio supera la hora de cierre
                    if ($duracion > 60 && $inicio > ($horaFinTimestamp - $duracion * 60)) {
                        // Si el servicio supera el cierre, no lo agregamos
                        break;
                    }

                    $rangos[] = date('H:i', $inicio) . ' - ' . date('H:i', $fin);
                }
            } else {
                // Iteramos para generar los rangos antes del descanso
                for ($inicio = $horaInicioTimestamp; $inicio < $breakStartTimestamp; $inicio += ($paso * 60)) {
                    $fin = $inicio + ($paso * 60);

                    // $inicioFormateado = date('H:i', $inicio);
                    // $finFormateado = date('H:i', $fin);
                    // $breakStartTimestampFormateado = date('H:i', $breakStartTimestamp);
                    // $horaFinTimestampFormateado = date('H:i', $horaFinTimestamp);

                    if ($fin > $breakStartTimestamp || $fin > $horaFinTimestamp) {
                        break;
                    }

                    $rangos[] = date('H:i', $inicio) . ' - ' . date('H:i', $fin);
                }

                // Iteramos para generar los rangos después del descanso
                for ($inicio = $breakEndTimestamp; $inicio < $horaFinTimestamp; $inicio += ($paso * 60)) {
                    $fin = $inicio + ($paso * 60);

                    // $finFormateado = date('H:i', $fin);
                    // $horaFinTimestampFormateado = date('H:i', $horaFinTimestamp);
                    if ($fin > $horaFinTimestamp) {
                        break;
                    }

                    // Verificar si el servicio supera la hora de cierre
                    if ($duracion > 60 && $inicio > ($horaFinTimestamp - $duracion * 60)) {
                        // Si el servicio supera el cierre, no lo agregamos
                        break;
                    }

                    $rangos[] = date('H:i', $inicio) . ' - ' . date('H:i', $fin);
                }
            }
        }

        return $rangos;
    }

    private function filterReservedSlots($available_slots, $reserved_appointments)
    {
        return array_values(array_filter($available_slots, function ($slot) use ($reserved_appointments) {
            list($start, $end) = explode(' - ', $slot);
            $start_time = strtotime($start);
            $end_time = strtotime($end);

            foreach ($reserved_appointments as $appointment) {
                $appointment_start = strtotime($appointment['start_time']);
                $appointment_end = strtotime($appointment['end_time']);

                if (!($end_time <= $appointment_start || $start_time >= $appointment_end)) {
                    return false;
                }
            }
            return true;
        }));
    }
}
