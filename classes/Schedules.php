<?php
class Schedules
{
    private $conn;
    private $company_id;

    public function __construct($conn, $company_id)
    {
        $this->conn = $conn;
        $this->company_id = $company_id;
    }

    public function getSchedules()
    {
        $schedulesSql = $this->conn->prepare("
            SELECT s.id AS schedule_id, s.day_id, d.day_name as day, s.work_start, s.work_end, s.break_start, s.break_end, s.is_enabled
            FROM company_schedules s
            JOIN days_of_week d ON s.day_id = d.id
            WHERE s.company_id = :company_id
            ORDER BY s.id
        ");
        $schedulesSql->bindParam(':company_id', $this->company_id);
        $schedulesSql->execute();
        $schedulesData = $schedulesSql->fetchAll(PDO::FETCH_ASSOC);

        return $schedulesData;
    }

    public function saveSchedules($schedulesData)
    {
        $schedulesData = $schedulesData['schedule'];

        $this->conn->beginTransaction();

        try {
            foreach ($schedulesData as $schedule) {
                $scheduleId = $schedule['schedule_id'];
                $isEnabled = $schedule['is_enabled'];

                if ($scheduleId) {
                    $updateScheduleSql = $this->conn->prepare("
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
                        $updateScheduleSql->bindParam(':work_start', $schedule['start']);
                    }
                    if (isset($schedule['end'])) {
                        $updateScheduleSql->bindParam(':work_end', $schedule['end']);
                    }
                    if (isset($schedule['break_start'])) {
                        $updateScheduleSql->bindParam(':break_start', $schedule['break_start']);
                    }
                    if (isset($schedule['break_end'])) {
                        $updateScheduleSql->bindParam(':break_end', $schedule['break_end']);
                    }
                    $updateScheduleSql->bindParam(':is_enabled', $isEnabled);
                    $updateScheduleSql->bindParam(':schedule_id', $scheduleId);

                    $updateScheduleSql->execute();
                } else {
                    $dayId = $schedule['day_id'];
                    $insertScheduleSql = $this->conn->prepare("
                        INSERT INTO company_schedules (company_id, day_id, work_start, work_end, break_start, break_end, is_enabled)
                        VALUES (:company_id, :day_id, :work_start, :work_end, :break_start, :break_end, :is_enabled)
                    ");
                    $insertScheduleSql->bindParam(':company_id', $this->company_id);
                    $insertScheduleSql->bindParam(':day_id', $dayId);
                    $insertScheduleSql->bindParam(':work_start', $schedule['work_start']);
                    $insertScheduleSql->bindParam(':work_end', $schedule['work_end']);
                    $insertScheduleSql->bindParam(':break_start', $schedule['break_start']);
                    $insertScheduleSql->bindParam(':break_end', $schedule['break_end']);
                    $insertScheduleSql->bindParam(':is_enabled', $isEnabled);
                    $insertScheduleSql->execute();
                }
            }

            $this->conn->commit();
            return "Schedules saved successfully.";
        } catch (PDOException $e) {
            $this->conn->rollBack();
            return "Error saving schedules: " . $e->getMessage();
        }
    }

    public function removeBreakTime($scheduleId)
    {
        $sql = "UPDATE company_schedules SET break_start = NULL, break_end = NULL WHERE id = :schedule_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':schedule_id', $scheduleId, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function copyMondayToAllDays($scheduleData)
    {

        for ($day = 1; $day <= 7; $day++) {
            $sql = "UPDATE company_schedules
            SET work_start = :work_start, work_end = :work_end, break_start = :break_start, break_end = :break_end, is_enabled = :is_enabled
            WHERE company_id = :company_id AND day_id = :day";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':work_start' => $scheduleData['start'],
                ':work_end' => $scheduleData['end'],
                ':break_start' => $scheduleData['break_start'],
                ':break_end' => $scheduleData['break_end'],
                ':is_enabled' => $scheduleData['is_enabled'],
                ':company_id' => $this->company_id,
                ':day' => $day,
            ]);
        }
    }
}
