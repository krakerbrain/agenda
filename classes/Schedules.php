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
            SELECT s.id AS schedule_id, d.day_name as day, s.work_start, s.work_end, s.break_start, s.break_end, s.is_enabled
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
}
