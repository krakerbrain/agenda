<?php
require_once dirname(__DIR__) . '/classes/DatabaseSessionManager.php';
$manager = new DatabaseSessionManager();
$manager->startSession();
$conn = $manager->getDB();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $company_id = $_POST['company_id'];
    $work_days = isset($_POST['work_days']) ? implode(',', $_POST['work_days']) : '';
    $schedule_mode = $_POST['schedule_mode'];
    $work_start = $_POST['work_start'];
    $work_end = $_POST['work_end'];
    $break_start = isset($_POST['break_start']) ? $_POST['break_start'] : null;
    $break_end = isset($_POST['break_end']) ? $_POST['break_end'] : null;
    $service_name = isset($_POST['service_name']) ? $_POST['service_name'] : '';
    $service_duration = isset($_POST['service_duration']) ? $_POST['service_duration'] : '';
    $blocked_dates = implode(',', $_POST['blocked_dates']); // Convertir a cadena
    $calendar_days_available = isset($_POST['calendar_days_available']) ? $_POST['calendar_days_available'] : '';

    try {
        $sql = $conn->prepare("
            UPDATE companies 
            SET work_days = :work_days,
                schedule_mode = :schedule_mode,
                work_start = :work_start,
                work_end = :work_end,
                break_start = :break_start,
                break_end = :break_end,
                blocked_dates = :blocked_dates,
                calendar_days_available = :calendar_days_available
            WHERE id = :company_id AND is_active = 1
        ");

        $sql->bindParam(':company_id', $company_id);
        $sql->bindParam(':work_days', $work_days);
        $sql->bindParam(':schedule_mode', $schedule_mode);
        $sql->bindParam(':work_start', $work_start);
        $sql->bindParam(':work_end', $work_end);
        $sql->bindParam(':break_start', $break_start);
        $sql->bindParam(':break_end', $break_end);
        $sql->bindParam(':blocked_dates', $blocked_dates, PDO::PARAM_STR);
        $sql->bindParam(':calendar_days_available', $calendar_days_available);

        // echo "update companies set work_days = $work_days, schedule_mode = $schedule_mode, work_start = $work_start, work_end = $work_end, break_start = $break_start, break_end = $break_end, blocked_dates = $blocked_dates WHERE id = 1 AND is_active = 1";
        $sql->execute();

        if ($service_name && $service_duration) {
            $sql = $conn->prepare("INSERT INTO services (company_id, name, duration) VALUES (:company_id, :name, :duration)");
            $sql->bindParam(':company_id', $company_id);
            $sql->bindParam(':name', $service_name);
            $sql->bindParam(':duration', $service_duration);
            $sql->execute();
        }

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
