<?php
require_once dirname(__DIR__, 2) . '/classes/DatabaseSessionManager.php';
$manager = new DatabaseSessionManager();
$manager->startSession();
$conn = $manager->getDB();
/*
considerando el formulario de configuracion.php insertar los datos en la tabla companies


id	
name	
logo	
blocked_dates	
is_active	
schedule_mode	
token	
calendar_days_available
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $company_id = $_POST['company_id'];
    $schedule_mode = $_POST['schedule_mode'];
    $calendar_days_available = $_POST['calendar_days_available'];
    $blocked_dates = implode(',', $_POST['blocked_dates']); // Convertir a cadena

    try {
        $sql = $conn->prepare("
            UPDATE companies 
            SET schedule_mode = :schedule_mode,
                blocked_dates = :blocked_dates,
                calendar_days_available = :calendar_days_available
            WHERE id = :company_id AND is_active = 1
        ");

        $sql->bindParam(':company_id', $company_id);
        $sql->bindParam(':schedule_mode', $schedule_mode);
        $sql->bindParam(':blocked_dates', $blocked_dates, PDO::PARAM_STR);
        $sql->bindParam(':calendar_days_available', $calendar_days_available);

        $sql->execute();

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
