<?php

require_once __DIR__ . '/configs/init.php';
require_once __DIR__ . '/classes/Database.php';



function addNewSchedule()
{
    $db = new Database();
    try {
        // Insertar los horarios de trabajo de la nueva compañía
        $days = [1, 2, 3, 4, 5, 6, 7]; // Lunes a Domingo
        foreach ($days as $day) {
            $sql = "INSERT INTO company_schedules 
                               (company_id, user_id, day_id, work_start, work_end, break_start, break_end, is_enabled) 
                               VALUES (:company_id, :user_id,:day_id, NULL, NULL, NULL, NULL, 1)";
            $db->query($sql);
            $db->bind(':company_id', 73);
            $db->bind(':user_id', 45);
            $db->bind(':day_id', $day);
            $db->execute();
        }

        return true;
    } catch (PDOException $e) {
        return "Error: " . $e->getMessage();
    }
}

addNewSchedule();
