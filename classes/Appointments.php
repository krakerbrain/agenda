<?php
require_once 'Database.php';
require_once dirname(__DIR__) . '/access-token/seguridad/JWTAuth.php';

use Ramsey\Uuid\Uuid;

class Appointments
{
    private $db;
    public function __construct(?Database $db = null)
    {
        $this->db = $db ?? new Database();
    }
    public function add_appointment($data)
    {
        try {
            // Verificar si ya existe una cita con los mismos datos
            if ($this->checkExistingAppointment($data)) {
                return ['error' => 'Cita ya ha sido enviada.'];
            }

            $this->db->query('INSERT INTO appointments (company_id, customer_id, date, start_time, end_time, id_service, service_category_id, aviso_reserva, created_at) 
                    VALUES (:company_id, :customer_id, :date, :start_time, :end_time, :id_service, :service_category_id, 0, now())');
            $this->db->bind(':company_id', $data['company_id']);
            $this->db->bind(':customer_id', $data['customer_id']);
            $this->db->bind(':date', $data['date']);
            $this->db->bind(':start_time', $data['start_time']);
            $this->db->bind(':end_time', $data['end_time']);
            $this->db->bind(':id_service', $data['id_service']);
            $this->db->bind(':service_category_id', $data['service_category_id']);
            $this->db->execute();

            // Obtener el ID de la cita recién creada
            $appointmentId = $this->db->lastInsertId();

            // Generar el token para la cita usando el ID
            $jwtAuth = new JWTAuth();
            $appointmentToken = $jwtAuth->generarTokenCita($data['company_id'], $appointmentId);

            // Actualizar la cita con el token generado
            $this->db->query('UPDATE appointments SET appointment_token = :token WHERE id = :id');
            $this->db->bind(':token', $appointmentToken);
            $this->db->bind(':id', $appointmentId);
            $this->db->execute();

            // Retornar el ID de la cita y el token generado
            return [
                'appointment_id' => $appointmentId,
                'appointment_token' => $appointmentToken
            ];
        } catch (PDOException $e) {
            // Manejo de errores, puedes loguear el error si es necesario
            return ['error' => $e->getMessage()]; // Retornar el mensaje de error
        }
    }

    public function addBlockedDay($data)
    {
        try {


            // Consulta SQL para insertar un "día bloqueado" como cita especial
            $query = "
            INSERT INTO appointments (
                company_id,customer_id, date, start_time, end_time, 
                id_service, status, aviso_reserva, aviso_confirmada, created_at
            ) VALUES (
                :company_id, :customer_id, :date, :start_time, :end_time, 
                :id_service, :status, :aviso_reserva, :aviso_confirmada, NOW()
            )
        ";

            // Preparar la consulta
            $this->db->query($query);

            // Asignar valores a los parámetros
            $this->db->bind(':company_id', $data['company_id']);
            $this->db->bind(':customer_id', $data['customer_id']);
            $this->db->bind(':date', $data['date']);
            $this->db->bind(':start_time', $data['start_time']);
            $this->db->bind(':end_time', $data['end_time']);
            $this->db->bind(':id_service', 0); // ID de servicio 0 para identificar que es un bloqueo
            $this->db->bind(':status', 1); // Estado activo
            $this->db->bind(':aviso_reserva', 1); // Marcar como notificado
            $this->db->bind(':aviso_confirmada', 1); // Marcar como confirmado

            // Ejecutar la consulta
            $this->db->execute();

            // Obtener el ID de la cita recién creada
            $appointmentId = $this->db->lastInsertId();

            // Generar el token identificador (UUID v4)
            $appointmentToken = Uuid::uuid4()->toString();

            // Actualizar la cita con el token generado
            $this->db->query('UPDATE appointments SET appointment_token = :token WHERE id = :id');
            $this->db->bind(':token', $appointmentToken);
            $this->db->bind(':id', $appointmentId);
            $this->db->execute();
            // Retornar el ID de la cita bloqueada
            return [
                'success' => true,
                'message' => 'Día bloqueado creado exitosamente.',
            ];
        } catch (Exception $e) {
            // Manejo de errores en caso de fallos
            return [
                'success' => false,
                'error' => 'Error al crear el día bloqueado: ' . $e->getMessage(),
            ];
        }
    }

    // getBlockedDays
    public function getBlockedDays($company_id)
    {

        $this->db->query('
        SELECT 
            DATE_FORMAT(date, "%d-%m-%Y") AS date, 
            start_time, 
            end_time, 
            appointment_token AS token 
        FROM 
            appointments 
        WHERE 
            company_id = :company_id 
            AND id_service = 0
            AND (date > CURDATE() OR (date = CURDATE() AND end_time >= NOW()))
    ');
        $this->db->bind(':company_id', $company_id);
        return $this->db->resultSet();
    }


    public function checkExistingAppointment($data)
    {

        $this->db->query('SELECT COUNT(*) as total FROM appointments WHERE company_id = :company_id AND date = :date AND start_time = :start_time AND end_time = :end_time');
        $this->db->bind(':company_id', $data['company_id']);
        $this->db->bind(':date', $data['date']);
        $this->db->bind(':start_time', $data['start_time']);
        $this->db->bind(':end_time', $data['end_time']);
        // Obtener el resultado y acceder a la propiedad 'total'
        $countResult = $this->db->single();

        // Asegurarnos de acceder al conteo correctamente
        return (int)$countResult['total'] > 0; // Retorna verdadero si hay al menos una cita existente
    }

    public function searchAppointments($company_id, $input, $query, $tab = 'all')
    {


        // Consulta base
        $querySql = 'SELECT a.id as id_appointment,a.*, s.name AS service, c.*,
                        DATE_FORMAT(a.date, "%d-%m-%Y") as date 
                 FROM appointments a 
                 INNER JOIN services s ON a.id_service = s.id
                 INNER JOIN customers c ON a.customer_id = c.id
                 INNER JOIN company_customers cc ON c.id = cc.customer_id
                 WHERE a.company_id = :company
                 AND cc.company_id = :company  
                 AND status != 2';

        // Filtrar según el tab actual
        if ($tab === 'unconfirmed') {
            $querySql .= ' AND a.status = 0 AND a.date >= CURDATE()';
        } elseif ($tab === 'confirmed') {
            $querySql .= ' AND a.status = 1 AND a.date >= CURDATE()';
        } elseif ($tab === 'past') {
            $querySql .= ' AND a.date < CURDATE()';
        }

        // Agregar condición de búsqueda dinámica
        if ($input && $query) {
            switch ($input) {
                case 'service':
                    $querySql .= ' AND s.name LIKE :query';
                    break;
                case 'name':
                    $querySql .= ' AND c.name LIKE :query';
                    break;
                case 'phone':
                    $querySql .= ' AND c.phone LIKE :query';
                    break;
                case 'mail':
                    $querySql .= ' AND c.mail LIKE :query';
                    break;
                case 'date':
                    $querySql .= ' AND DATE(a.date) = :query';
                    break;
                case 'hour':
                    $querySql .= ' AND TIME(a.start_time) = :query';
                    break;
                default:
                    throw new Exception("Campo de búsqueda no válido.");
            }
        }

        // Preparar y ejecutar la consulta
        $this->db->query($querySql);
        $this->db->bind(':company', $company_id);
        if ($input && $query) {
            $this->db->bind(':query', $input === 'date' || $input === 'hour' ? $query : "%$query%");
        }

        return $this->db->resultSet();
    }

    public function get_paginated_appointments($company_id, $status, $offset, $limit)
    {
        $query = 'SELECT a.id as id_appointment, a.*, s.name AS service, c.id as id_customer, c.*, COALESCE(cat.category_name, "-") AS category,
                     DATE_FORMAT(a.date, "%d-%m-%Y") as date 
                     FROM appointments a 
                     INNER JOIN services s ON a.id_service = s.id
                     INNER JOIN customers c ON a.customer_id = c.id
                     INNER JOIN company_customers cc ON c.id = cc.customer_id
                    LEFT JOIN service_categories cat ON a.service_category_id = cat.id
                     WHERE a.company_id = :company
                     AND cc.company_id = :company  
                     AND status != 2';

        if ($status === 'unconfirmed') {
            $query .= ' AND status = 0 AND a.date >= CURDATE()';
        } elseif ($status === 'confirmed') {
            $query .= ' AND status = 1 AND a.date >= CURDATE()';
        } elseif ($status === 'past') {
            $query .= ' AND date < CURDATE()';
        }

        $query .= ' ORDER BY a.date DESC LIMIT :offset, :limit';

        $this->db->query($query);
        $this->db->bind(':company', $company_id);
        $this->db->bind(':offset', $offset, PDO::PARAM_INT);
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);

        return $this->db->resultSet();
    }

    // Metodo usado para mostrar al cliente los datos de su cita desde el link de whatsapp
    public function get_appointment($id)
    {
        try {
            //code...


            $this->db->query('SELECT s.name as service, a.*, c.name as customer_name FROM appointments a
                            JOIN customers c
                            ON a.customer_id = c.id
                            JOIN services s
                            ON a.id_service = s.id
                            WHERE a.id = :id');
            $this->db->bind(':id', $id);
            return $this->db->single();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }


    public function get_appointment_token($token)
    {
        try {
            //code...


            $this->db->query('SELECT id FROM appointments WHERE  appointment_token = :token');
            $this->db->bind(':token', $token);
            return $this->db->single();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function getUnconfirmedAppointment($type = 'reserva')
    {
        try {
            $condition = $type === 'reserva'
                ? ' AND a.aviso_reserva = 0'
                : ' AND a.aviso_confirmada = 0 AND a.aviso_reserva = 1 AND a.status = 1';


            $this->db->query('SELECT a.*, cu.name as customer_name, cu.phone as customer_phone, cu.mail as customer_mail, s.name as service_name, c.name as company_name
                FROM appointments a
                JOIN customers cu
                ON a.customer_id = cu.id
                JOIN company_customers cc
                ON cu.id = cc.customer_id
                JOIN services s
                ON a.id_service = s.id
                JOIN companies c
                ON c.id = a.company_id
                WHERE cc.company_id = c.id
                ' . $condition);
            return $this->db->resultSet();
        } catch (Exception $e) {
            throw new Exception("Error al obtener citas no confirmadas: " . $e->getMessage());
        }
    }

    // Obtener todas las citas en el rango de fechas
    public function getAppointmentsByDateRange($company_id, $start_date, $end_date)
    {

        $this->db->query('SELECT date, start_time, end_time FROM appointments WHERE company_id = :company_id AND date BETWEEN :start_date AND :end_date');
        $this->db->bind(':company_id', $company_id);
        $this->db->bind(':start_date', $start_date);
        $this->db->bind(':end_date', $end_date);
        return $this->db->resultSet();
    }

    //obtener citas por fecha

    public function getAppointmentsByDate($company_id, $date)
    {

        $this->db->query('SELECT * FROM appointments WHERE company_id = :company_id AND date = :date');
        $this->db->bind(':company_id', $company_id);
        $this->db->bind(':date', $date);

        return $this->db->resultSet();
    }

    public function checkAppointments($company_id, $date, $start_hour, $end_hour)
    {


        // Verificar citas que se solapen con el rango horario dado
        $this->db->query("
                    SELECT * 
                    FROM appointments 
                    WHERE company_id = :company_id 
                      AND DATE(date) = :date
                      AND (
                          (:start_hour BETWEEN start_time AND end_time) OR 
                          (:end_hour BETWEEN start_time AND end_time) OR 
                          (start_time BETWEEN :start_hour AND :end_hour)
                      )
        ");
        $this->db->bind(':company_id', $company_id);
        $this->db->bind(':date', $date);
        $this->db->bind(':start_hour', $start_hour);
        $this->db->bind(':end_hour', $end_hour);

        return $this->db->resultSet();
    }



    public function markAsConfirmed($id, $type)
    {


        if ($type  === 'reserva') {
            $this->db->query("UPDATE appointments SET aviso_reserva = 1, aviso_confirmada  = 0 WHERE id = :id");
        } else {
            $this->db->query("UPDATE appointments SET aviso_confirmada = 1 WHERE id = :id");
        }
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
    public function updateAppointment($id, $status, $eventId = null)
    {
        try {
            $this->db->query("UPDATE appointments SET status = :status, event_id = :event_id, updated_at = now() WHERE id = :id");
            $this->db->bind(':status', $status);
            $this->db->bind(':event_id', $eventId);
            $this->db->bind(':id', $id);
            $this->db->execute();

            $rowCount = $this->db->rowCount();

            if ($rowCount === 0) {
                return [
                    'success' => false,
                    'message' => 'No se actualizó ninguna cita. Verifica que el ID exista.',
                    'rows_affected' => 0
                ];
            }

            return [
                'success' => true,
                'message' => 'Cita actualizada exitosamente',
                'rows_affected' => $rowCount
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Error al actualizar la cita'
            ];
        }
    }

    public function delete_appointment($id)
    {

        $this->db->query('DELETE FROM appointments WHERE id = :id');
        $this->db->bind(':id', $id);
        $this->db->execute();
        return $this->db->rowCount();
    }

    public function deleteBlockedDay($token, $company_id)
    {

        $this->db->query("DELETE FROM appointments WHERE appointment_token = :token AND company_id = :company_id");
        $this->db->bind(':token', $token);
        $this->db->bind(':company_id', $company_id);

        return $this->db->execute();
    }
}
