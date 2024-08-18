<?php

class Appointments
{
    private $conn;

    public function __construct($dbConnection)
    {
        $this->conn = $dbConnection;
    }

    public function getAppointment($id)
    {
        $stmt = $this->conn->prepare("SELECT s.name as service, a.* FROM appointments a
                            JOIN services s
                            ON a.id_service = s.id
                            WHERE a.id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function confirmAppointment($id)
    {
        $stmt = $this->conn->prepare("UPDATE appointments SET status = 1 WHERE id = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Otros métodos relacionados con las citas...
}
