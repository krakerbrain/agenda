<?php
require_once 'Database.php';

class UniqueEvents extends Database
{
    public function add_event($data)
    {
        try {
            $db = new Database();

            // Insertar el evento en la tabla `unique_events`
            $db->query('INSERT INTO unique_events (company_id, name, description, created_at) 
                        VALUES (:company_id, :name, :description, now())');
            $db->bind(':company_id', $data['company_id']);
            $db->bind(':name', $data['name']);
            $db->bind(':description', $data['description']);
            $db->execute();

            // Obtener el ID del evento recién creado
            $eventId = $db->lastInsertId();

            // Insertar las fechas del evento en `unique_event_dates`, incluyendo `start_time` y `end_time`
            $db->query('INSERT INTO unique_event_dates (event_id, event_date, event_start_time, event_end_time) 
                        VALUES (:event_id, :event_date, :start_time, :end_time)');

            // Verificar que los arrays `dates`, `start_times` y `end_times` tienen la misma longitud
            if (count($data['dates']) === count($data['start_times']) && count($data['dates']) === count($data['end_times'])) {
                foreach ($data['dates'] as $index => $date) {
                    $db->bind(':event_id', $eventId);
                    $db->bind(':event_date', $date); // Fecha del evento
                    $db->bind(':start_time', $data['start_times'][$index]); // Hora de inicio
                    $db->bind(':end_time', $data['end_times'][$index]); // Hora de fin
                    $db->execute();
                }
            } else {
                throw new Exception('Los arrays de fechas, horas de inicio y horas de fin no tienen la misma longitud.');
            }

            return ['event_id' => $eventId];
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function get_upcoming_events($company_id)
    {
        $db = new Database();

        // Consulta para obtener los datos de los eventos
        $db->query('SELECT e.id AS event_id, e.name, e.description, 
                           d.event_date, 
                           d.event_start_time, 
                           d.event_end_time
                    FROM unique_events e
                    INNER JOIN unique_event_dates d ON e.id = d.event_id
                    WHERE e.company_id = :company_id AND d.event_date >= CURDATE()
                    ORDER BY e.id, d.event_date ASC, d.event_start_time ASC');
        $db->bind(':company_id', $company_id);
        $results = $db->resultSet();

        // Agrupar los resultados por evento
        $events = [];
        foreach ($results as $row) {
            $eventId = $row['event_id'];
            if (!isset($events[$eventId])) {
                $events[$eventId] = [
                    'id' => $row['event_id'],
                    'name' => $row['name'],
                    'description' => $row['description'],
                    'dates' => []
                ];
            }
            $events[$eventId]['dates'][] = [
                'event_date' => $row['event_date'],
                'event_start_time' => $row['event_start_time'],
                'event_end_time' => $row['event_end_time']
            ];
        }

        // Retornar el array agrupado
        return array_values($events); // Eliminar índices numéricos para facilitar el formato JSON
    }

    // Método para obtener las personas inscritas a un evento
    public function get_event_inscriptions($company_id)
    {
        $db = new Database();
        $db->query('SELECT ei.id AS inscription_id, 
                   ei.name AS participant_name, 
                   ei.email, 
                   ei.phone, 
                   ei.rut, 
                   ei.created_at, 
                   ue.name AS event_name, 
                   ued.event_date, 
                   ued.event_start_time, 
                   ued.event_end_time
            FROM event_inscriptions ei
            JOIN unique_events ue ON ei.event_id = ue.id
            JOIN unique_event_dates ued ON ue.id = ued.event_id
            WHERE ue.company_id = :company_id
            ORDER BY ued.event_date, ued.event_start_time');

        $db->bind(':company_id', $company_id);
        return $db->resultSet();
    }

    public function delete_event($event_id)
    {
        try {
            $db = new Database();

            // Eliminar las fechas asociadas al evento
            $db->query('DELETE FROM unique_event_dates WHERE event_id = :event_id');
            $db->bind(':event_id', $event_id);
            $db->execute();

            // Eliminar el evento
            $db->query('DELETE FROM unique_events WHERE id = :event_id');
            $db->bind(':event_id', $event_id);
            $db->execute();

            return ['success' => true];
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function delete_event_date($event_id, $event_date, $start_time)
    {
        try {
            $db = new Database();

            // Eliminar la fecha específica asociada al evento
            $db->query('DELETE FROM unique_event_dates 
                        WHERE event_id = :event_id 
                          AND event_date = :event_date 
                          AND event_start_time = :start_time');
            $db->bind(':event_id', $event_id);
            $db->bind(':event_date', $event_date);
            $db->bind(':start_time', $start_time);
            $db->execute();

            // Verificar si hay más fechas asociadas al evento
            $db->query('SELECT COUNT(*) as count FROM unique_event_dates WHERE event_id = :event_id');
            $db->bind(':event_id', $event_id);
            $remainingDates = $db->single();

            if ($remainingDates['count'] == 0) {
                // Si no quedan fechas, eliminar el evento principal
                $db->query('DELETE FROM unique_events WHERE id = :event_id');
                $db->bind(':event_id', $event_id);
                $db->execute();
            }

            return ['success' => true];
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    // Método para registrar a un usuario en un evento
    public function register_user_to_event($data)
    {
        try {
            $db = new Database();

            // Insertar la inscripción en la tabla `event_inscriptions`
            $db->query('INSERT INTO event_inscriptions (event_id, name, email, phone, aviso_reserva,created_at)
                        VALUES (:event_id, :name, :email, :phone, 0, now())');

            // Vincular los parámetros
            $db->bind(':event_id', $data['event_id']);
            $db->bind(':name', $data['name']);
            $db->bind(':email', $data['email']);
            $db->bind(':phone', $data['phone']);

            // Ejecutar la consulta
            $db->execute();

            return ['success' => true, 'message' => 'Inscripción exitosa.'];
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
