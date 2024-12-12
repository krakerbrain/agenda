<?php
require_once 'Database.php';

class NotificationLog
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function create($data)
    {
        $sql = "INSERT INTO notifications_log (appointment_id, type, method, status) 
                VALUES (:appointment_id, :type, :method, :status)";
        $this->db->query($sql);
        $this->db->bind(':appointment_id', $data['appointment_id']);
        $this->db->bind(':type', $data['type']);
        $this->db->bind(':method', $data['method']);
        $this->db->bind(':status', $data['status']);
        $this->db->execute();

        return $this->db->lastInsertId();
    }

    public function update($id, $data)
    {
        $sql = "UPDATE notifications_log 
                SET status = :status, attempts = :attempts, last_attempt = :last_attempt 
                WHERE id = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':attempts', $data['attempts']);
        $this->db->bind(':last_attempt', $data['last_attempt']);
        $this->db->execute();
    }

    public function getAllLogsForAppointment($appointmentId)
    {

        try {
            $sql = "SELECT * FROM notifications_log 
            WHERE appointment_id = :appointment_id";
            $this->db->query($sql);
            $this->db->bind(':appointment_id', $appointmentId);
            return $this->db->resultSet();
        } catch (Exception $e) {
            throw new Exception("Error al logs para citas: " . $e->getMessage());
        }
    }


    public function getById($id)
    {
        $sql = "SELECT * FROM notifications_log WHERE id = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function delete($id)
    {
        $sql = "DELETE FROM notifications_log WHERE appointment_id = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        $this->db->execute();
        return $this->db->rowCount();
    }
}
