<?php

require_once 'CompanyManager.php';

class CronTasks
{
    public function clearPastBlockedDates()
    {
        $companyManager = new CompanyManager();
        $companies = $companyManager->getAllActiveCompanies();

        // Registrar el inicio del proceso
        error_log("Inicio de la tarea cron para eliminar fechas bloqueadas: " . PHP_EOL . date('Y-m-d H:i:s'), 3, '../cron/cron-error/error.log');
        if (empty($companies)) {
            error_log("No se encontraron compañías activas." . PHP_EOL, 3, '../cron/cron-error/error.log');
            return; // Salir si no hay compañías
        }

        foreach ($companies as $company) {
            $success = $companyManager->removePastBlockedDates($company['id']);
            if (!$success) {
                // Registrar error si la eliminación falla
                error_log("Error al eliminar fechas pasadas para la compañía ID: " . PHP_EOL . $company['id'], 3, '../cron/cron-error/error.log');
            }
        }

        // Registrar el final del proceso
        error_log("Finalizada la tarea cron para eliminar fechas bloqueadas: " . PHP_EOL . date('Y-m-d H:i:s'), 3, '../cron/cron-error/error.log');
    }
}
