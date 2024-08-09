<?php
require_once dirname(__DIR__, 2) . '/classes/DatabaseSessionManager.php';
$manager = new DatabaseSessionManager();
// $manager->startSession();
session_start();
$conn = $manager->getDB();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $company_id = $_POST['company_id'];
    $schedule_mode = $_POST['schedule_mode'];
    $calendar_days_available = $_POST['calendar_days_available'];
    $blocked_dates = implode(',', $_POST['blocked_dates']); // Convertir a cadena
    $bg_color = $_POST['background-color'];
    $font_color = $_POST['font-color'];
    $btn1_color = $_POST['btn-primary-color'];
    $btn2_color = $_POST['btn-secondary-color'];

    try {
        $sql = $conn->prepare("
            UPDATE companies 
            SET schedule_mode = :schedule_mode,
                blocked_dates = :blocked_dates,
                calendar_days_available = :calendar_days_available,
                bg_color = :bg_color,
                font_color = :font_color,
                btn1 = :btn1_color,
                btn2 = :btn2_color
            WHERE id = :company_id AND is_active = 1
        ");

        $sql->bindParam(':company_id', $company_id);
        $sql->bindParam(':schedule_mode', $schedule_mode);
        $sql->bindParam(':blocked_dates', $blocked_dates, PDO::PARAM_STR);
        $sql->bindParam(':calendar_days_available', $calendar_days_available);
        $sql->bindParam(':bg_color', $bg_color);
        $sql->bindParam(':font_color', $font_color);
        $sql->bindParam(':btn1_color', $btn1_color);
        $sql->bindParam(':btn2_color', $btn2_color);

        $sql->execute();

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
