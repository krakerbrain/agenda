<?php

require_once 'CompanyManager.php'; // Ajustar la ruta de inclusión

class CronTasks
{
    public function clearPastBlockedDates()
    {
        $companyManager = new CompanyManager();
        $companies = $companyManager->getAllActiveCompanies();

        // Registrar el inicio del proceso
        error_log("Inicio de la tarea cron para eliminar fechas bloqueadas: " . date('Y-m-d H:i:s') . PHP_EOL, 3, dirname(__DIR__) . '/cron/cronerror/error.log');

        echo dirname(__DIR__) . '/cron/cronerror/error.log';

        if (empty($companies)) {
            error_log("No se encontraron compañías activas." . PHP_EOL, 3, dirname(__DIR__) . '/cron/cronerror/error.log');
            return; // Salir si no hay compañías
        }

        foreach ($companies as $company) {
            $success = $companyManager->removePastBlockedDates($company['id']);
            if (!$success) {
                // Registrar error si la eliminación falla
                error_log("Error al eliminar fechas pasadas para la compañía ID: " . $company['id'] . PHP_EOL, 3, dirname(__DIR__) . '/cron/cronerror/error.log');
            }
        }

        // Registrar el final del proceso
        error_log("Finalizada la tarea cron para eliminar fechas bloqueadas: " . date('Y-m-d H:i:s') . PHP_EOL, 3, dirname(__DIR__) . '/cron/cronerror/error.log');
    }
}
