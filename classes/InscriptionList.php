<?php
require_once 'Database.php';

class InscriptionList extends Database
{
    public function delete_event_from_list($inscription_id)
    {
        try {

            $db = new Database();
            $db->query('DELETE FROM event_inscriptions WHERE id = :id');
            $db->bind(':id', $inscription_id);
            $db->execute();
            return $db->rowCount();
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
