<?php
require_once 'Database.php';

class Appointments extends Database
{
    public function add_appointment($data)
    {
        $db = new Database();
        $db->query('INSERT INTO appointments (company_id, name, phone, mail, date, start_time, end_time, id_service) VALUES (:company_id, :name, :phone, :mail, :date, :start_time, :end_time, :id_service)');
        $db->bind(':company_id', $data['company_id']);
        $db->bind(':name', $data['name']);
        $db->bind(':phone', $data['phone']);
        $db->bind(':mail', $data['mail']);
        $db->bind(':date', $data['date']);
        $db->bind(':start_time', $data['start_time']);
        $db->bind(':end_time', $data['end_time']);
        $db->bind(':id_service', $data['id_service']);
        $db->execute();
        return $db->rowCount();


        // No necesitas instanciar $db porque la clase hereda de Database
        // $this->query('INSERT INTO appointments (company_id, user_id, date, time, description) VALUES (:company_id, :user_id, :date, :time, :description)');
        // $this->bind(':company_id', $data['company_id']);
        // $this->bind(':user_id', $data['user_id']);
        // $this->bind(':date', $data['date']);
        // $this->bind(':time', $data['time']);
        // $this->bind(':description', $data['description']);
        // $this->execute();
        // return $this->rowCount();
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


    public function get_appointment($id)
    {
        $db = new Database();
        $db->query('SELECT s.name as service, a.* FROM appointments a
                            JOIN services s
                            ON a.id_service = s.id
                            WHERE a.id = :id');
        $db->bind(':id', $id);
        return $db->single();
    }

    public function update_appointment($id)
    {
        $db = new Database();
        $db->query('UPDATE appointments SET status = 1 WHERE id = :id');
        $db->bind(':id', $id);
        $db->execute();
        return $db->rowCount();
    }
    public function update_event($eventId, $appointmentId)
    {
        $db = new Database();
        $db->query("UPDATE appointments SET event_id = :event_id WHERE id = :appointment_id");
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
