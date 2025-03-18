<?php
require_once 'Database.php';

class UniqueEvents extends Database
{
    public function add_event($data)
    {
        try {
            $db = new Database();

            // Insertar el evento en la tabla `unique_events`
            $db->query('INSERT INTO unique_events (company_id, name, description, cupo_maximo, created_at) 
                        VALUES (:company_id, :name, :description, :cupo_maximo, now())');
            $db->bind(':company_id', $data['company_id']);
            $db->bind(':name', $data['name']);
            $db->bind(':cupo_maximo', $data['cupo_maximo']);
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
        $db->query('SELECT e.id AS event_id, e.name, e.description, e.cupo_maximo,
                           d.event_date, 
                           d.event_start_time, 
                           d.event_end_time
                    FROM unique_events e
                    INNER JOIN unique_event_dates d ON e.id = d.event_id
                    WHERE e.company_id = :company_id 
                    AND d.event_date >= CURDATE()
                    AND cupo_maximo > 0
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
                    'cupo_maximo' => $row['cupo_maximo'],
                    'dates' => []
                ];
            }
            // Formatear la fecha y la hora 
            $eventDate = DateTime::createFromFormat('Y-m-d', $row['event_date'])->format('d-m-Y');
            $eventStartTime = DateTime::createFromFormat('H:i:s', $row['event_start_time'])->format('H:i');
            $eventEndTime = DateTime::createFromFormat('H:i:s', $row['event_end_time'])->format('H:i');

            $events[$eventId]['dates'][] = [
                'event_date' => $eventDate,
                'event_start_time' => $eventStartTime,
                'event_end_time' => $eventEndTime
            ];
        }


        // Retornar el array agrupado
        return array_values($events); // Eliminar índices numéricos para facilitar el formato JSON
    }


    public function getUnconfirmedEvent($type = 'reserva')
    {
        $condition = $type === 'reserva'
            ? 'WHERE ei.aviso_reserva = 0'
            : 'WHERE ei.aviso_confirmada = 0 AND ei.aviso_reserva = 1 AND ei.status = 1';

        $db = new Database();
        $db->query('SELECT ei.id AS inscription_id, 
                        ei.name AS participant_name, 
                        ei.email, 
                        ei.phone, 
                        ue.company_id, 
                        ue.name AS event_name, 
                        ue.notas_correo_reserva_evento AS notas_reserva,
                        ue.notas_correo_confirmacion_evento AS notas_confirmacion,
                        ued.event_date, 
                        ued.event_start_time
                 FROM event_inscriptions ei
                 JOIN unique_events ue ON ei.event_id = ue.id
                 JOIN unique_event_dates ued ON ue.id = ued.event_id
                 ' . $condition);
        return $db->resultSet();
    }

    public function markAsNotified($id, $type = 'reserva')
    {
        $db = new Database();

        if ($type === 'reserva') {
            // Marcar solo como aviso_reserva
            $db->query("UPDATE event_inscriptions SET aviso_reserva = 1, aviso_confirmada = 0 WHERE id = :id");
        } else {
            // Marcar como aviso_confirmada
            $db->query("UPDATE event_inscriptions SET aviso_confirmada = 1 WHERE id = :id");
        }

        $db->bind(':id', $id);
        return $db->execute();
    }

    public function updateEvent($id, $status)
    {
        $db = new Database();

        try {
            // Actualizar el estado de la inscripción
            $db->query("UPDATE event_inscriptions SET status = :status, updated_at = now() WHERE id = :id");
            $db->bind(':status', $status);
            $db->bind(':id', $id);
            $db->execute();
            return $db->rowCount();
        } catch (Exception $e) {
            $db->cancelTransaction();
            throw new Exception("Error al actualizar el evento: " . $e->getMessage());
        }
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
                   ei.status,
                   ei.created_at, 
                   ue.name AS event_name, 
                   DATE_FORMAT(ued.event_date, "%d-%m-%Y") as event_date, 
                   ued.event_start_time, 
                   ued.event_end_time
            FROM event_inscriptions ei
            JOIN unique_events ue ON ei.event_id = ue.id
            JOIN unique_event_dates ued ON ue.id = ued.event_id
            WHERE ue.company_id = :company_id
            ORDER BY event_date, ued.event_start_time');

        $db->bind(':company_id', $company_id);
        return $db->resultSet();
    }

    public function searchEventInscriptions($company_id, $input, $query, $status = null)
    {
        $db = new Database();

        // Consulta base
        $querySql = 'SELECT ei.id AS inscription_id, 
                            ei.name AS participant_name, 
                            ei.email, 
                            ei.phone, 
                            ei.rut, 
                            ei.status,
                            ei.created_at, 
                            ue.name AS event_name, 
                            ued.event_date, 
                            ued.event_start_time, 
                            ued.event_end_time
                     FROM event_inscriptions ei
                     JOIN unique_events ue ON ei.event_id = ue.id
                     JOIN unique_event_dates ued ON ue.id = ued.event_id
                     WHERE ue.company_id = :company_id';

        // Filtrar por estado si se proporciona
        if ($status !== null && $status !== 'all') {
            $querySql .= ' AND ei.status = :status';
        }

        // Agregar condición de búsqueda dinámica
        if ($input && $query) {
            switch ($input) {
                case 'event':
                    $querySql .= ' AND ue.name LIKE :query';
                    break;
                case 'name':
                    $querySql .= ' AND ei.name LIKE :query';
                    break;
                case 'phone':
                    $querySql .= ' AND ei.phone LIKE :query';
                    break;
                case 'email':
                    $querySql .= ' AND ei.email LIKE :query';
                    break;
                case 'date':
                    $querySql .= ' AND DATE(ued.event_date) = :query';
                    break;
                case 'start_time':
                    $querySql .= ' AND TIME(ued.event_start_time) = :query';
                    break;
                default:
                    throw new Exception("Campo de búsqueda no válido.");
            }
        }

        // Ordenar por fecha y hora del evento
        $querySql .= ' ORDER BY ued.event_date, ued.event_start_time';

        // Preparar y ejecutar la consulta
        $db->query($querySql);
        $db->bind(':company_id', $company_id);

        // Vincular parámetros adicionales
        if ($status !== null && $status !== 'all') {
            $db->bind(':status', $status);
        }
        if ($input && $query) {
            $db->bind(':query', $input === 'date' || $input === 'start_time' ? $query : "%$query%");
        }

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

    public function register_inscription($data)
    {
        try {
            $db = new Database();

            // Verificar si el evento tiene cupo disponible
            $db->query('SELECT cupo_maximo FROM unique_events WHERE id = :event_id');
            $db->bind(':event_id', $data['event_id']);
            $event = $db->single();

            if (!$event || $event['cupo_maximo'] <= 0) {
                return ['error' => 'El evento ya no tiene cupos disponibles.'];
            }

            // Insertar la inscripción en la tabla `event_inscriptions`
            $db->query('INSERT INTO event_inscriptions (event_id, name, email, phone, aviso_reserva, created_at)
                    VALUES (:event_id, :name, :email, :phone, 0, now())');
            $db->bind(':event_id', $data['event_id']);
            $db->bind(':name', $data['name']);
            $db->bind(':email', $data['email']);
            $db->bind(':phone', $data['phone']);
            $db->execute();

            // Reducir el cupo máximo del evento
            $db->query('UPDATE unique_events SET cupo_maximo = cupo_maximo - 1 WHERE id = :event_id AND cupo_maximo > 0');
            $db->bind(':event_id', $data['event_id']);
            $db->execute();

            return ['success' => true, 'message' => 'Inscripción exitosa.'];
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
