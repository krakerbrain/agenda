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

    public function autoOpenNewPeriod()
    {
        $companyManager = new CompanyManager();
        $companies = $companyManager->getAllActiveCompanies();
        // Registrar el inicio del proceso
        error_log("Inicio del cron job para apertura automática de periodos: " . date('Y-m-d H:i:s') . PHP_EOL, 3, dirname(__DIR__) . '/cron/log/actualizarfixeddate.log');
        foreach ($companies as $company) {
            // Usar try-catch para manejar errores
            try {
                // Verificar si la opción de apertura automática está activada
                if (!$company['auto_open']) {
                    continue; // Saltar si la compañía no tiene auto_open activado
                }

                $fixedStartDay = new DateTime($company['fixed_start_date']);
                $duration = $company['fixed_duration'];

                // Calcular la fecha de término del periodo actual
                $endDate = clone $fixedStartDay;
                $endDate->modify("+$duration days");

                $currentDate = new DateTime();

                // Verificar si el periodo actual termina en menos de un día
                if ($currentDate >= $endDate->modify('-1 day')) {
                    // Calcular la nueva fecha de inicio para el próximo periodo
                    $newStartDay = (new DateTime())->modify('+1 day')->format('Y-m-d');

                    // Actualizar en la base de datos
                    $updated = $companyManager->updateFixedStartDay($company['id'], $newStartDay);

                    if ($updated) {
                        error_log("Periodo actualizado para la compañía ID: " . $company['id'] . " con nueva fecha de inicio: " . $newStartDay . PHP_EOL, 3, dirname(__DIR__) . '/cron/log/actualizarfixeddate.log');
                    } else {
                        error_log("Error al actualizar la fecha de inicio para la compañía ID: " . $company['id'] . PHP_EOL, 3, dirname(__DIR__) . '/cron/log/actualizarfixeddate.log');
                    }
                } else {
                    error_log("No se ha encontrado un periodo actual terminado en menos de un día para la compañía ID: " . $company['id'] . PHP_EOL, 3, dirname(__DIR__) . '/cron/log/actualizarfixeddate.log');
                }
            } catch (Exception $e) {
                // Registrar la excepción en el log
                error_log("Excepción al procesar la compañía ID: " . $company['id'] . " - Error: " . $e->getMessage() . PHP_EOL, 3, dirname(__DIR__) . '/cron/log/actualizarfixeddate.log');
            }
        }
    }
}
