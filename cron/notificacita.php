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
date_default_timezone_set('UTC');
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
            $wspNotificationId = null;
            $emailNotificationId = null;
            $wspAttempts = 0;
            $emailAttempts = 0;

            if ($existingLogs) {
                foreach ($existingLogs as $log) {
                    if ($appointment['id'] == $log['appointment_id']) {
                        if ($log['method'] === 'whatsapp') {
                            $wspNotificationId = $log['id'];
                            $wspAttempts = $log['attempts'];
                            if ($log['status'] === 'sent') {
                                $wspStatus = 'sent';
                            }
                        } elseif ($log['method'] === 'email') {
                            $emailNotificationId = $log['id'];
                            $emailAttempts = $log['attempts'];
                            if ($log['status'] === 'sent') {
                                $emailStatus = 'sent';
                            }
                        }
                    }
                }
            }

            // Solo enviar si los estados no son 'sent'
            $shouldSendWsp = ($wspStatus !== 'sent');
            $shouldSendEmail = ($emailStatus !== 'sent');

            if ($shouldSendWsp) {
                try {
                    $templateName = $type === 'reserva' ? 'registro_reserva' : 'confirmar_reserva';
                    $wspStatusCode = sendWspReserva(
                        $templateName,
                        $appointment['customer_phone'],
                        $appointment['customer_name'],
                        formatearFecha($appointment['date']),
                        $appointment['start_time'],
                        $appointment['company_name'],
                        $appointment['appointment_token'],
                        ucwords($appointment['service_name'])
                    );
                    // $wspStatusCode = 200;
                    $wspStatus = ($wspStatusCode == 200 || $wspStatusCode == 201) ? 'sent' : 'failed';
                    handleNotificationRegister($notificationLog, $appointment['id'], 'whatsapp', $wspNotificationId, $wspStatus, $wspAttempts, $type);
                } catch (Exception $e) {
                    handleNotificationRegister($notificationLog, $appointment['id'], 'whatsapp', $wspNotificationId, $wspStatus, $wspAttempts, $type);
                    $wspStatus = 'failed';
                    throw new Exception("Error al enviar mensaje de whatsapp: " . $e->getMessage());
                }
            }

            if ($shouldSendEmail) {
                try {
                    $emailContent = $emailTemplateBuilder->buildEmail($appointment, $type);
                    $emailStatus = $emailContent['success'] ? 'sent' : 'failed';
                } catch (Exception $e) {
                    // Actualizar o crear registro de email
                    $emailStatus = 'failed';
                    handleNotificationRegister($notificationLog, $appointment['id'], 'email', $emailNotificationId, $emailStatus, $emailAttempts, $type);
                    throw new Exception("Error al enviar correo: " . $e->getMessage());
                }
                // Actualizar o crear registro de email
                handleNotificationRegister($notificationLog, $appointment['id'], 'email', $emailNotificationId, $emailStatus, $emailAttempts, $type);
            }

            // Verificar que ambos métodos hayan sido exitosos antes de confirmar la cita
            if ($wspStatus === 'sent' && $emailStatus === 'sent') {
                $appointments->markAsConfirmed($appointment['id'], $type);
                // $notificationLog->delete($appointment['id']);
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


function handleNotificationRegister($notificationLog, $appointment_id, $method, $notificationId, $status, $attempts, $type)
{
    // Actualizar o crear registro de email
    if ($notificationId) {
        $notificationLog->update($notificationId, [
            'status' => $status,
            'attempts' => $attempts + 1,
            'last_attempt' => date('Y-m-d H:i:s'),
        ]);
    } else {
        $notificationLog->create([
            'appointment_id' => $appointment_id,
            'type' => $type,
            'method' => $method,
            'status' => $status,
            'attempts' => 1,
            'last_attempt' => date('Y-m-d H:i:s'),
        ]);
    }
}

function formatearFecha($fecha)
{
    // Definir los días de la semana y los meses en español
    $dias = [
        'domingo',
        'lunes',
        'martes',
        'miércoles',
        'jueves',
        'viernes',
        'sábado',
    ];

    $meses = [
        'enero',
        'febrero',
        'marzo',
        'abril',
        'mayo',
        'junio',
        'julio',
        'agosto',
        'septiembre',
        'octubre',
        'noviembre',
        'diciembre'
    ];

    // Convertir la fecha en formato 'Y-m-d' a un objeto DateTime
    $fecha_obj = new DateTime($fecha);

    // Obtener el día de la semana (0=domingo, 1=lunes, ..., 6=sábado)
    $dia_semana = $fecha_obj->format('w');

    // Obtener el día, mes y año de la fecha
    $dia = $fecha_obj->format('d');
    $mes = $fecha_obj->format('m'); // Mes en formato numérico (01-12)
    $anio = $fecha_obj->format('Y');

    // Construir la fecha en el formato deseado: "viernes, 18 de abril de 2025"
    $fecha_formateada = ucfirst($dias[$dia_semana]) . ', ' . $dia . ' de ' . $meses[$mes - 1] . ' de ' . $anio;

    return $fecha_formateada;
}
