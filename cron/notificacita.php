<?php
require_once dirname(__DIR__) . '/configs/init.php';
require_once dirname(__DIR__) . '/classes/Appointments.php';
require_once dirname(__DIR__) . '/classes/EmailTemplate.php';
require_once dirname(__DIR__) . '/classes/NotificationLog.php';
require_once dirname(__DIR__) . '/user_admin/send_wsp.php';

// Crear instancias de las clases necesarias
$appointments = new Appointments();
$emailTemplateBuilder = new EmailTemplate();
$notificationLog = new NotificationLog();

try {
    error_log("INFO: Inicio cron notificación cita " . date('Y-m-d H:i:s') . PHP_EOL, 3, __DIR__ . '/log/avisoreserva.log');

    // Tipos de notificaciones: 'reserva' y 'confirmacion'
    $notificationTypes = ['reserva', 'confirmacion'];

    foreach ($notificationTypes as $type) {
        $unconfirmedAppointments = $appointments->getUnconfirmedAppointment($type);

        foreach ($unconfirmedAppointments as $appointment) {
            $existingLogs = $notificationLog->getAllLogsForAppointment($appointment['id']);

            // Inicializar estados
            $wspStatus = 'pending';
            $emailStatus = 'pending';
            $notificationId = null;
            $attempts = 0;

            if ($existingLogs) {
                foreach ($existingLogs as $log) {
                    if ($appointment['id'] == $log['appointment_id']) {
                        $notificationId = $log['id'];
                        $attempts = $log['attempts'];

                        // Evaluar el estado de cada método
                        if ($log['method'] === 'whatsapp' && $log['status'] === 'sent') {
                            $wspStatus = 'sent';
                        } elseif ($log['method'] === 'email' && $log['status'] === 'sent') {
                            $emailStatus = 'sent';
                        }
                    }
                }
            }

            // Solo enviar si los estados no son 'sent'
            $shouldSendWsp = ($wspStatus !== 'sent');
            $shouldSendEmail = ($emailStatus !== 'sent');

            if ($shouldSendWsp) {
                $templateName = $type === 'reserva' ? 'registro_reserva' : 'confirmar_reserva';
                $wspStatusCode = sendWspReserva(
                    $templateName,
                    $appointment['phone'],
                    $appointment['name'],
                    $appointment['date'],
                    $appointment['start_time'],
                    $appointment['company_name'],
                    $appointment['appointment_token'],
                    ucwords($appointment['service_name'])
                );
                // $wspStatusCode = 200;
                $wspStatus = ($wspStatusCode == 200 || $wspStatusCode == 201) ? 'sent' : 'failed';

                // Actualizar o crear registro de WhatsApp
                if ($notificationId) {
                    $notificationLog->update($notificationId, [
                        'status' => $wspStatus,
                        'attempts' => $attempts + 1,
                        'last_attempt' => date('Y-m-d H:i:s'),
                    ]);
                } else {
                    $notificationLog->create([
                        'appointment_id' => $appointment['id'],
                        'type' => $type,
                        'method' => 'whatsapp',
                        'status' => $wspStatus,
                        'attempts' => 1,
                        'last_attempt' => date('Y-m-d H:i:s'),
                    ]);
                }
            }

            if ($shouldSendEmail) {
                $emailContent = $emailTemplateBuilder->buildEmail($appointment, $type);
                $emailStatus = $emailContent['success'] ? 'sent' : 'failed';
                // $emailStatus =  'sent';

                // Actualizar o crear registro de email
                if ($notificationId) {
                    $notificationLog->update($notificationId, [
                        'status' => $emailStatus,
                        'attempts' => $attempts + 1,
                        'last_attempt' => date('Y-m-d H:i:s'),
                    ]);
                } else {
                    $notificationLog->create([
                        'appointment_id' => $appointment['id'],
                        'type' => $type,
                        'method' => 'email',
                        'status' => $emailStatus,
                        'attempts' => 1,
                        'last_attempt' => date('Y-m-d H:i:s'),
                    ]);
                }
            }

            // Verificar que ambos métodos hayan sido exitosos antes de confirmar la cita
            if ($wspStatus === 'sent' && $emailStatus === 'sent') {
                $appointments->markAsConfirmed($appointment['id'], $type);
                $notificationLog->delete($appointment['id']);
            }
        }
    }
} catch (Exception $e) {
    error_log("ERROR: Error en el cron de notificaciones: " . $e->getMessage() . PHP_EOL, 3, __DIR__ . '/log/avisoreserva.log');
} finally {
    error_log("INFO: Cron finalizado " . date('Y-m-d H:i:s') . PHP_EOL, 3, __DIR__ . '/log/avisoreserva.log');
    $appointments = null;
    $emailTemplateBuilder = null;
    $notificationLog = null;
}
