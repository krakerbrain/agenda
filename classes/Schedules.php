<?php
require_once dirname(__DIR__) . '/classes/Database.php';

class Schedules
{
    private $db;
    private $company_id;

    public function __construct($company_id)
    {
        $this->db = new Database(); // Usa la clase Database
        $this->company_id = $company_id;
    }

    public function getSchedules()
    {
        $this->db->query("SELECT s.id AS schedule_id, s.day_id, d.day_name as day, s.work_start, s.work_end, s.break_start, s.break_end, s.is_enabled
            FROM company_schedules s
            JOIN days_of_week d ON s.day_id = d.id
            WHERE s.company_id = :company_id
            ORDER BY s.id");
        $this->db->bind(':company_id', $this->company_id);
        return $this->db->resultSet();
    }

    //getenabledSchedulesDays
    public function getEnabledSchedulesDays()
    {
        $this->db->query("SELECT day_id, 
                                work_start, 
                                work_end, 
                                break_start, 
                                break_end 
                        FROM company_schedules 
                        WHERE company_id = :company_id 
                        AND is_enabled = 1");
        $this->db->bind(':company_id', $this->company_id);
        return $this->db->resultSet();
    }

    //getschedulebyday
    public function getScheduleByDay($day_id)
    {
        $this->db->query("SELECT work_start, work_end, break_start, break_end 
                        FROM company_schedules 
                        WHERE company_id = :company_id 
                        AND day_id = :day_id");
        $this->db->bind(':company_id', $this->company_id);
        $this->db->bind(':day_id', $day_id);
        return $this->db->single();
    }

    public function saveSchedules($schedulesData)
    {
        $schedulesData = $schedulesData['schedule'];

        $this->db->beginTransaction();
        try {
            foreach ($schedulesData as $schedule) {
                $scheduleId = $schedule['schedule_id'];
                $isEnabled = $schedule['is_enabled'];

                if ($scheduleId) {
                    $this->db->query("
                    UPDATE company_schedules
                    SET 
                        work_start = " . (isset($schedule['start']) ? ":work_start" : "work_start") . ",
                        work_end = " . (isset($schedule['end']) ? ":work_end" : "work_end") . ",
                        break_start = " . (isset($schedule['break_start']) ? ":break_start" : "break_start") . ",
                        break_end = " . (isset($schedule['break_end']) ? ":break_end" : "break_end") . ",
                        is_enabled = :is_enabled
                    WHERE id = :schedule_id
                ");

                    if (isset($schedule['start'])) {
                        $this->db->bind(':work_start', $schedule['start']);
                    }
                    if (isset($schedule['end'])) {
                        $this->db->bind(':work_end', $schedule['end']);
                    }
                    if (isset($schedule['break_start'])) {
                        $this->db->bind(':break_start', $schedule['break_start']);
                    }
                    if (isset($schedule['break_end'])) {
                        $this->db->bind(':break_end', $schedule['break_end']);
                    }
                    $this->db->bind(':is_enabled', $isEnabled);
                    $this->db->bind(':schedule_id', $scheduleId);

                    $this->db->execute();
                } else {
                    $dayId = $schedule['day_id'];
                    $this->db->query("
                        INSERT INTO company_schedules (company_id, day_id, work_start, work_end, break_start, break_end, is_enabled)
                        VALUES (:company_id, :day_id, :work_start, :work_end, :break_start, :break_end, :is_enabled)
                    ");
                    $this->db->bind(':company_id', $this->company_id);
                    $this->db->bind(':day_id', $dayId);
                    $this->db->bind(':work_start', $schedule['work_start']);
                    $this->db->bind(':work_end', $schedule['work_end']);
                    $this->db->bind(':break_start', $schedule['break_start']);
                    $this->db->bind(':break_end', $schedule['break_end']);
                    $this->db->bind(':is_enabled', $isEnabled);
                    $this->db->execute();
                }
            }
            $this->db->endTransaction();
            return "Schedules saved successfully.";
        } catch (PDOException $e) {
            $this->db->cancelTransaction();
            return "Error saving schedules: " . $e->getMessage();
        }
    }

    public function removeBreakTime($scheduleId)
    {
        $this->db->query("UPDATE company_schedules SET break_start = NULL, break_end = NULL WHERE id = :schedule_id");
        $this->db->bind(':schedule_id', $scheduleId);
        $this->db->execute();
    }

    public function copyMondayToAllDays($scheduleData)
    {

        for ($day = 1; $day <= 7; $day++) {

            $this->db->query("UPDATE company_schedules
            SET work_start = :work_start, work_end = :work_end, break_start = :break_start, break_end = :break_end, is_enabled = :is_enabled
            WHERE company_id = :company_id AND day_id = :day");
            $this->db->bind(':work_start', $scheduleData['start']);
            $this->db->bind(':work_end', $scheduleData['end']);
            $this->db->bind(':break_start', $scheduleData['break_start']);
            $this->db->bind(':break_end', $scheduleData['break_end']);
            $this->db->bind(':is_enabled', $scheduleData['is_enabled']);
            $this->db->bind(':company_id', $this->company_id);
            $this->db->bind(':day', $day);
            $this->db->execute();
        }
    }

    public function validateSelectedDate($day_of_week)
    {
        $db = new Database();

        // Consultar el horario habilitado para el día seleccionado
        $db->query("
        SELECT work_start, work_end 
        FROM company_schedules 
        WHERE company_id = :company_id 
          AND day_id = :day_of_week 
          AND is_enabled = 1
    ");
        $db->bind(':company_id', $this->company_id);
        $db->bind(':day_of_week', $day_of_week);

        $schedule = $db->single();

        if (!$schedule) {
            return [
                'success' => false,
                'message' => 'El día seleccionado no esta habilitado en el horario de la empresa.',
            ];
        }

        return [
            'success' => true,
            'work_start' => $schedule['work_start'],
            'work_end' => $schedule['work_end'],
        ];
    }
}
