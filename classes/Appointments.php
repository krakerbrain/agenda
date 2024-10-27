<?php
require_once 'Database.php';
require_once dirname(__DIR__) . '/access-token/seguridad/JWTAuth.php';

class Appointments extends Database
{
    public function add_appointment($data)
    {
        try {
            $db = new Database();

            // Insertar la cita sin el token
            $db->query('INSERT INTO appointments (company_id, name, phone, mail, date, start_time, end_time, id_service, created_at) 
                    VALUES (:company_id, :name, :phone, :mail, :date, :start_time, :end_time, :id_service, now())');
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

    public function update_appointment($id)
    {
        $db = new Database();
        $db->query('UPDATE appointments SET status = 1, updated_at = now() WHERE id = :id');
        $db->bind(':id', $id);
        $db->execute();
        return $db->rowCount();
    }
    public function update_event($eventId, $appointmentId)
    {
        $db = new Database();
        $db->query("UPDATE appointments SET event_id = :event_id, updated_at = now() WHERE id = :appointment_id");
        $db->bind(':event_id', $eventId);
        $db->bind(':appointment_id', $appointmentId);
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
