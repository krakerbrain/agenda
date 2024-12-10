<?php
require_once 'Database.php';

class InscriptionList extends Database
{
    public function delete_event_from_list($inscription_id)
    {
        try {
            $db = new Database();
            $db->beginTransaction();  // Iniciar una transacción

            // Obtener el event_id relacionado con la inscripción
            $db->query('SELECT event_id FROM event_inscriptions WHERE id = :id');
            $db->bind(':id', $inscription_id);
            $event = $db->single();

            if ($event) {
                // Sumar 1 al cupo_maximo del evento
                $db->query('UPDATE unique_events SET cupo_maximo = cupo_maximo + 1 WHERE id = :event_id');
                $db->bind(':event_id', $event['event_id']);
                $db->execute();

                // Eliminar la inscripción
                $db->query('DELETE FROM event_inscriptions WHERE id = :id');
                $db->bind(':id', $inscription_id);
                $db->execute();

                $db->endTransaction();  // Confirmar la transacción

                return $db->rowCount();  // Retorna el número de filas afectadas
            }

            // Si no se encuentra el evento, se hace rollback
            $db->cancelTransaction();
            return 0;
        } catch (PDOException $e) {
            // En caso de error, hacer rollback y retornar el error
            $db->rollBack();
            return ['error' => $e->getMessage()];
        }
    }
}
