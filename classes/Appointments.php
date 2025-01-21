<?php
require_once 'Database.php';
require_once dirname(__DIR__) . '/access-token/seguridad/JWTAuth.php';

use Ramsey\Uuid\Uuid;

class Appointments extends Database
{
    /**
     * Método para agregar una cita.
     *
     * Este método inserta una nueva cita en la base de datos y genera un token para la misma.
     *
     * Parámetros esperados en $data:
     * - company_id: ID de la compañía.
     * - name: Nombre del cliente.
     * - phone: Teléfono del cliente.
     * - mail: Correo electrónico del cliente.
     * - date: Fecha de la cita (YYYY-MM-DD).
     * - start_time: Hora de inicio de la cita (HH:MM:SS).
     * - end_time: Hora de fin de la cita (HH:MM:SS).
     * - id_service: ID del servicio asociado a la cita.
     * - aviso_reserva: (Por defecto "0") Indica si el cliente ha sido notificado.
     * - created_at: Fecha y hora de creación de la cita (asignado automáticamente).
     *
     * @param array $data Datos de la cita.
     * @return array Resultado de la operación:
     *               - Si es exitoso: ['appointment_id' => int, 'appointment_token' => string].
     *               - Si hay error: ['error' => string].
     * @throws PDOException Si ocurre un error en la base de datos.
     */
    public function add_appointment($data)
    {
        try {
            $db = new Database();


            // Verificar si ya existe una cita con los mismos datos
            if ($this->checkExistingAppointment($data)) {
                return ['error' => 'Cita ya ha sido enviada.'];
            }

            $db->query('INSERT INTO appointments (company_id, name, phone, mail, date, start_time, end_time, id_service, aviso_reserva, created_at) 
                    VALUES (:company_id, :name, :phone, :mail, :date, :start_time, :end_time, :id_service, 0, now())');
            $db->bind(':company_id', $data['company_id']);
            $db->bind(':name', $data['name']);
            $db->bind(':phone', $data['phone']);
            $db->bind(':mail', $data['mail']);
            $db->bind(':date', $data['date']);
            $db->bind(':start_time', $data['start_time']);
            $db->bind(':end_time', $data['end_time']);
            $db->bind(':id_service', $data['id_service']);
            $db->execute();

            // Obtener el ID de la cita recién creada
            $appointmentId = $db->lastInsertId();

            // Generar el token para la cita usando el ID
            $jwtAuth = new JWTAuth();
            $appointmentToken = $jwtAuth->generarTokenCita($data['company_id'], $appointmentId);

            // Actualizar la cita con el token generado
            $db->query('UPDATE appointments SET appointment_token = :token WHERE id = :id');
            $db->bind(':token', $appointmentToken);
            $db->bind(':id', $appointmentId);
            $db->execute();

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
            $db = new Database();

            // Consulta SQL para insertar un "día bloqueado" como cita especial
            $query = "
            INSERT INTO appointments (
                company_id, name, phone, mail, date, start_time, end_time, 
                id_service, status, aviso_reserva, aviso_confirmada, created_at
            ) VALUES (
                :company_id, :name, :phone, :mail, :date, :start_time, :end_time, 
                :id_service, :status, :aviso_reserva, :aviso_confirmada, NOW()
            )
        ";

            // Preparar la consulta
            $db->query($query);

            // Asignar valores a los parámetros
            $db->bind(':company_id', $data['company_id']);
            $db->bind(':name', 'Día Bloqueado'); // Nombre genérico para identificar el bloqueo
            $db->bind(':phone', null); // Teléfono no aplica
            $db->bind(':mail', null); // Correo no aplica
            $db->bind(':date', $data['date']);
            $db->bind(':start_time', $data['start_time']);
            $db->bind(':end_time', $data['end_time']);
            $db->bind(':id_service', 0); // ID de servicio 0 para identificar que es un bloqueo
            $db->bind(':status', 1); // Estado activo
            $db->bind(':aviso_reserva', 1); // Marcar como notificado
            $db->bind(':aviso_confirmada', 1); // Marcar como confirmado

            // Ejecutar la consulta
            $db->execute();

            // Obtener el ID de la cita recién creada
            $appointmentId = $db->lastInsertId();

            // Generar el token identificador (UUID v4)
            $appointmentToken = Uuid::uuid4()->toString();

            // Actualizar la cita con el token generado
            $db->query('UPDATE appointments SET appointment_token = :token WHERE id = :id');
            $db->bind(':token', $appointmentToken);
            $db->bind(':id', $appointmentId);
            $db->execute();
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
        $db = new Database();
        $db->query('
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
        $db->bind(':company_id', $company_id);
        return $db->resultSet();
    }


    public function checkExistingAppointment($data)
    {
        $db = new Database();
        $db->query('SELECT COUNT(*) as total FROM appointments WHERE company_id = :company_id AND date = :date AND start_time = :start_time AND end_time = :end_time');
        $db->bind(':company_id', $data['company_id']);
        $db->bind(':date', $data['date']);
        $db->bind(':start_time', $data['start_time']);
        $db->bind(':end_time', $data['end_time']);
        // Obtener el resultado y acceder a la propiedad 'total'
        $countResult = $db->single();

        // Asegurarnos de acceder al conteo correctamente
        return (int)$countResult['total'] > 0; // Retorna verdadero si hay al menos una cita existente
    }

    public function searchAppointments($company_id, $status, $service, $name, $phone, $email, $date, $hour, $tab = 'all')
    {
        $db = new Database();
        $query = 'SELECT a.*, s.name AS service,
                     DATE_FORMAT(a.date, "%d-%m-%Y") as date 
                     FROM appointments a 
                     INNER JOIN services s ON a.id_service = s.id
                     WHERE a.company_id = :company 
                     AND status != 2';

        // Filtrar según el tab actual
        if ($tab === 'unconfirmed') {
            $query .= ' AND a.status = 0 AND a.date >= CURDATE()';
        } elseif ($tab === 'confirmed') {
            $query .= ' AND a.status = 1 AND a.date >= CURDATE()';
        } elseif ($tab === 'past') {
            $query .= ' AND a.date < CURDATE()';
        }

        // Agregar condiciones de búsqueda
        if ($status !== 'all') {
            $query .= ' AND a.status = :status';
        }
        if ($service) {
            $query .= ' AND s.name LIKE :service';
        }
        if ($name) {
            $query .= ' AND a.name LIKE :name';
        }
        if ($phone) {
            $query .= ' AND a.phone LIKE :phone';
        }
        if ($email) {
            $query .= ' AND a.mail LIKE :email';
        }
        if ($date) {
            $query .= ' AND DATE(a.date) = :date';
        }
        if ($hour) {
            $query .= ' AND TIME(a.start_time) = :hour';
        }

        $db->query($query);
        $db->bind(':company', $company_id);
        if ($status !== 'all') $db->bind(':status', $status);
        if ($service) $db->bind(':service', "%$service%");
        if ($name) $db->bind(':name', "%$name%");
        if ($phone) $db->bind(':phone', "%$phone%");
        if ($email) $db->bind(':email', "%$email%");
        if ($date) $db->bind(':date', $date);
        if ($hour) $db->bind(':hour', $hour);

        return $db->resultSet();
    }

    public function get_paginated_appointments($company_id, $status, $offset, $limit)
    {
        $db = new Database();

        $query = 'SELECT a.*, s.name AS service,
                     DATE_FORMAT(a.date, "%d-%m-%Y") as date 
                     FROM appointments a 
                     INNER JOIN services s ON a.id_service = s.id
                     WHERE a.company_id = :company 
                     AND status != 2';

        if ($status === 'unconfirmed') {
            $query .= ' AND status = 0 AND a.date >= CURDATE()';
        } elseif ($status === 'confirmed') {
            $query .= ' AND status = 1 AND a.date >= CURDATE()';
        } elseif ($status === 'past') {
            $query .= ' AND date < CURDATE()';
        }

        $query .= ' ORDER BY a.date DESC LIMIT :offset, :limit';

        $db->query($query);
        $db->bind(':company', $company_id);
        $db->bind(':offset', $offset, PDO::PARAM_INT);
        $db->bind(':limit', $limit, PDO::PARAM_INT);

        return $db->resultSet();
    }


    public function get_appointment($id)
    {
        try {
            //code...

            $db = new Database();
            $db->query('SELECT s.name as service, a.* FROM appointments a
                            JOIN services s
                            ON a.id_service = s.id
                            WHERE a.id = :id');
            $db->bind(':id', $id);
            return $db->single();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    public function get_appointment_token($token)
    {
        try {
            //code...

            $db = new Database();
            $db->query('SELECT id FROM appointments WHERE  appointment_token = :token');
            $db->bind(':token', $token);
            return $db->single();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function getUnconfirmedAppointment($type = 'reserva')
    {
        try {
            $condition = $type === 'reserva'
                ? 'WHERE a.aviso_reserva = 0'
                : 'WHERE a.aviso_confirmada = 0 AND a.aviso_reserva = 1 AND a.status = 1';

            $db = new Database();
            $db->query('SELECT a.*, s.name as service_name, c.name as company_name
                FROM appointments a
                JOIN services s
                ON a.id_service = s.id
                JOIN companies c
                ON c.id = a.company_id
                ' . $condition);
            return $db->resultSet();
        } catch (Exception $e) {
            throw new Exception("Error al obtener citas no confirmadas: " . $e->getMessage());
        }
    }

    // Obtener todas las citas en el rango de fechas
    public function getAppointmentsByDateRange($company_id, $start_date, $end_date)
    {
        $db = new Database();
        $db->query('SELECT date, start_time, end_time FROM appointments WHERE company_id = :company_id AND date BETWEEN :start_date AND :end_date');
        $db->bind(':company_id', $company_id);
        $db->bind(':start_date', $start_date);
        $db->bind(':end_date', $end_date);
        return $db->resultSet();
    }

    public function checkAppointments($company_id, $date, $start_hour, $end_hour)
    {
        $db = new Database();

        // Verificar citas que se solapen con el rango horario dado
        $db->query("
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
        $db->bind(':company_id', $company_id);
        $db->bind(':date', $date);
        $db->bind(':start_hour', $start_hour);
        $db->bind(':end_hour', $end_hour);

        return $db->resultSet();
    }



    public function markAsConfirmed($id, $type)
    {
        $db = new Database();

        if ($type  === 'reserva') {
            $db->query("UPDATE appointments SET aviso_reserva = 1, aviso_confirmada  = 0 WHERE id = :id");
        } else {
            $db->query("UPDATE appointments SET aviso_confirmada = 1 WHERE id = :id");
        }
        $db->bind(':id', $id);
        return $db->execute();
    }

    public function updateAppointment($id, $status, $eventId = null)
    {
        $db = new Database();
        $db->query("UPDATE appointments SET status = :status, event_id = :event_id, updated_at = now() WHERE id = :id");
        $db->bind(':status', $status);
        $db->bind(':event_id', $eventId);
        $db->bind(':id', $id);
        $db->execute();
        return $db->rowCount();
    }

    public function delete_appointment($id)
    {
        $db = new Database();
        $db->query('DELETE FROM appointments WHERE id = :id');
        $db->bind(':id', $id);
        $db->execute();
        return $db->rowCount();
    }

    public function deleteBlockedDay($token, $company_id)
    {
        $db = new Database();
        $db->query("DELETE FROM appointments WHERE appointment_token = :token AND company_id = :company_id");
        $db->bind(':token', $token);
        $db->bind(':company_id', $company_id);

        return $db->execute();
    }
}
