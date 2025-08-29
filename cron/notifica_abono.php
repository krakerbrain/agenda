<?php
require_once dirname(__DIR__) . '/configs/init.php';
require_once dirname(__DIR__) . '/classes/Appointments.php';
require_once dirname(__DIR__) . '/classes/NotificationLog.php';
require_once dirname(__DIR__) . '/user_admin/send_remember_wsp.php';
require_once dirname(__DIR__) . '/classes/IntegrationManager.php';
require_once dirname(__DIR__) . '/classes/FeatureManager.php';
$appointments = new Appointments();
$notificationLog = new NotificationLog();
$integrationManager = new IntegrationManager();
$featureManager = new FeatureManager();

date_default_timezone_set('UTC');

try {
    error_log("INFO: Inicio cron notificación abono " . date('Y-m-d H:i:s') . PHP_EOL, 3, __DIR__ . '/log/notifica_abono.log');

    // Traer todas las citas pendientes de abono
    $pendingAppointments = $appointments->getPendingAbonoAppointments(); // status 1, aviso_reserva = 1, aviso_confirmada = 0

    // Agrupar citas por compañía
    $appointmentsByCompany = [];
    foreach ($pendingAppointments as $appointment) {
        $appointmentsByCompany[$appointment['company_id']][] = $appointment;
    }

    // Recorrer compañías
    foreach ($appointmentsByCompany as $companyId => $companyAppointments) {

        // 1. Verificar si la compañía tiene habilitado el feature flag
        if (!$featureManager->isEnabled($companyId, 'notifica_abono')) {
            // error_log("INFO: Notificación de abono deshabilitada para compañía $companyId" . PHP_EOL, 3, __DIR__ . '/log/notifica_abono.log');
            continue;
        }

        // 2. Verificar si WhatsApp está habilitado para esta compañía
        $companyIntegrations = $integrationManager->getCompanyIntegrations($companyId);
        $whatsappEnabled = false;
        foreach ($companyIntegrations as $integration) {
            if ($integration['integration_id'] == 2 && $integration['company_enabled']) {
                $whatsappEnabled = true;
                break;
            }
        }

        if (!$whatsappEnabled) {
            error_log("INFO: WhatsApp no habilitado para compañía {$companyId}" . PHP_EOL, 3, __DIR__ . '/log/notifica_abono.log');
            continue;
        }

        // Procesar citas de la compañía
        foreach ($companyAppointments as $appointment) {

            // Calcular horas transcurridas desde created_at
            $hoursPassed = (time() - strtotime($appointment['created_at'])) / 3600;

            // Obtener logs existentes de la cita
            $existingLogs = $notificationLog->getAllLogsForAppointment($appointment['id']);

            // Inicializar estados
            $notifications = [
                'abono_24h' => ['status' => 'pending', 'attempts' => 0, 'id' => null],
                'abono_48h' => ['status' => 'pending', 'attempts' => 0, 'id' => null],
            ];

            if ($existingLogs) {
                foreach ($existingLogs as $log) {
                    if ($log['method'] === 'whatsapp' && isset($notifications[$log['type']])) {
                        $notifications[$log['type']]['status'] = $log['status'];
                        $notifications[$log['type']]['attempts'] = $log['attempts'];
                        $notifications[$log['type']]['id'] = $log['id'];
                    }
                }
            }

            // Función interna para enviar WhatsApp y registrar log
            $sendWspAndLog = function ($type) use ($appointment, $notifications, $notificationLog, $whatsappEnabled) {
                if (!$whatsappEnabled) return;
                $n = $notifications[$type];
                $shouldSend = ($n['status'] !== 'sent' && $n['status'] !== 'failed_permanent' && $n['attempts'] < 3);
                if (!$shouldSend) return;

                try {
                    if ($_ENV["APP_ENV"] != 'local') {
                        $wspStatusCode = sendRememberWsp(
                            $type,
                            $appointment['customer_phone'],
                            $appointment['customer_name'],
                            $appointment['appointment_date'],
                            $appointment['appointment_time'],
                            $appointment['company_name'],
                            $appointment['appointment_token']
                        );
                        $status = ($wspStatusCode == 200 || $wspStatusCode == 201) ? 'sent' : 'failed';
                    } else {
                        // para pruebas, simular siempre éxito
                        $status = 'sent';
                    }
                    error_log("INFO: Enviando WhatsApp ({$type}) cita {$appointment['id']} a {$appointment['customer_phone']}" . PHP_EOL, 3, __DIR__ . '/log/notifica_abono.log');
                } catch (Exception $e) {
                    $status = 'failed';
                    error_log("ERROR: Falló envío WhatsApp ({$type}) cita {$appointment['id']}: " . $e->getMessage() . PHP_EOL, 3, __DIR__ . '/log/notifica_abono.log');
                }

                // Ahora centralizamos el manejo de intentos y status
                handleNotification(
                    $notificationLog,
                    $appointment['id'],
                    'whatsapp',
                    $n['id'],
                    $status,
                    $n['attempts'],
                    $type
                );
            };

            // Enviar 24h si corresponde
            if ($hoursPassed >= 24 && $notifications['abono_24h']['status'] !== 'sent') {
                $sendWspAndLog('abono_24h');
            }

            // Enviar 48h si ya se envió 24h y pasaron 48h desde la creación
            if ($hoursPassed >= 48 && $notifications['abono_24h']['status'] === 'sent' && $notifications['abono_48h']['status'] !== 'sent') {
                $sendWspAndLog('abono_48h');
            }
        }
    }

    error_log("INFO: Cron notifica_abono finalizado " . date('Y-m-d H:i:s') . PHP_EOL, 3, __DIR__ . '/log/notifica_abono.log');
} catch (Exception $e) {
    error_log("ERROR: Cron notifica_abono: " . $e->getMessage() . PHP_EOL, 3, __DIR__ . '/log/notifica_abono.log');
}

function handleNotification($notificationLog, $appointment_id, $method, $notificationId, $status, $attempts, $type)
{
    $newAttempts = $attempts + 1;

    if ($status !== 'sent' && $newAttempts >= 3) {
        $status = 'failed_permanent';
    }

    $data = [
        'appointment_id' => $appointment_id,
        'type'           => $type,
        'method'         => $method,
        'status'         => $status,
        'attempts'       => $notificationId ? $newAttempts : 1,
        'last_attempt'   => date('Y-m-d H:i:s'),
    ];

    if ($notificationId) {
        $notificationLog->update($notificationId, $data);
    } else {
        $notificationLog->create($data);
    }
}
