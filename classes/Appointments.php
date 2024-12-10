<?php
require_once 'Database.php';
require_once dirname(__DIR__) . '/access-token/seguridad/JWTAuth.php';

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



    public function get_appointments($company_id)
    {
        $db = new Database();
        $db->query('SELECT a.*, s.name AS service FROM appointments a 
                         inner join services s 
                         on a.id_service = s.id
                         WHERE a.company_id = :company
                         AND status != 2
                         ORDER BY date DESC');
        $db->bind(':company', $company_id);
        return $db->resultSet();
    }

    public function get_all_appointments($company_id)
    {
        $db = new Database();
        $db->query('SELECT a.*, s.name AS service, 
                 DATE_FORMAT(a.date, "%d-%m-%Y") as date 
                 FROM appointments a 
                 INNER JOIN services s ON a.id_service = s.id
                 WHERE a.company_id = :company
                 AND status != 2
                 ORDER BY a.date DESC');
        $db->bind(':company', $company_id);
        return $db->resultSet();
    }

    public function get_unconfirmed_appointments($company_id)
    {
        $db = new Database();
        $db->query('SELECT a.*, s.name AS service,
                 DATE_FORMAT(a.date, "%d-%m-%Y") as date 
                 FROM appointments a 
                 INNER JOIN services s ON a.id_service = s.id
                 WHERE a.company_id = :company
                 AND status = 0
                 AND a.date >= CURDATE()
                 ORDER BY a.date DESC');
        $db->bind(':company', $company_id);
        return $db->resultSet();
    }

    public function get_confirmed_appointments($company_id)
    {
        $db = new Database();
        $db->query('SELECT a.*, s.name AS service,
                 DATE_FORMAT(a.date, "%d-%m-%Y") as date 
                 FROM appointments a 
                 INNER JOIN services s ON a.id_service = s.id
                 WHERE a.company_id = :company
                 AND status = 1
                 AND a.date >= CURDATE()
                 ORDER BY a.date DESC');
        $db->bind(':company', $company_id);
        return $db->resultSet();
    }

    public function get_past_appointments($company_id)
    {
        $db = new Database();
        $db->query('SELECT a.*, s.name AS service,
                 DATE_FORMAT(a.date, "%d-%m-%Y") as date 
                 FROM appointments a 
                 INNER JOIN services s ON a.id_service = s.id
                 WHERE a.company_id = :company
                 AND date < CURDATE()
                 AND status != 2
                 ORDER BY a.date DESC');
        $db->bind(':company', $company_id);
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

    public function getUnconfirmedReserva()
    {
        $db = new Database();
        $db->query("SELECT a.*, s.name as service_name FROM appointments a
                JOIN services s
                ON a.id_service = s.id
                WHERE a.aviso_reserva = 0");
        return $db->resultSet();
    }

    public function getUnconfirmedAppointment()
    {
        $db = new Database();
        $db->query("SELECT a.*, s.name as service_name FROM appointments a
                JOIN services s
                ON a.id_service = s.id
                WHERE aviso_confirmada = 0 
                AND aviso_reserva = 1 
                AND status = 1");
        return $db->resultSet();
    }


    public function markAsConfirmed($id, $type = 'reserva')
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
}
