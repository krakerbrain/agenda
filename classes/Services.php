<?php
require_once dirname(__DIR__) . '/classes/Database.php';

class Services
{
    private $db;
    private $company_id;
    private $user_id;

    public function __construct($company_id, $user_id)
    {
        $this->db = new Database(); // Usa la clase Database
        $this->company_id = $company_id;
        $this->user_id = $user_id;
    }

    public function getServices()
    {
        $this->db->query("
        SELECT 
                s.id AS service_id,
                s.name AS service_name,
                s.duration,
                s.observations,
                s.is_enabled,
                us.available_days,
                sc.id AS category_id,
                sc.category_name,
                sc.category_description
            FROM 
                services s
            INNER JOIN 
                user_services us ON s.id = us.service_id AND us.user_id = :user_id
            LEFT JOIN 
                service_categories sc ON s.id = sc.service_id
            WHERE 
                s.company_id = :company_id
            ORDER BY 
                s.name, sc.category_name
    ");
        $this->db->bind(':company_id', $this->company_id);
        $this->db->bind(':user_id', $this->user_id);
        $servicesData = $this->db->resultSet();

        $organizedData = [];
        foreach ($servicesData as $row) {
            $serviceId = $row['service_id'];

            if (!isset($organizedData[$serviceId])) {
                $durationFormatted = $this->formatDuration($row['duration']); // Formatea la duración
                $organizedData[$serviceId] = [
                    'service_id' => $row['service_id'],
                    'service_name' => $row['service_name'],
                    'duration' => $row['duration'],
                    'duration_formatted' => $durationFormatted, // Agrega la duración formateada
                    'observations' => $row['observations'],
                    'is_enabled' => $row['is_enabled'],
                    'available_days' => $row['available_days'],
                    'categories' => []
                ];
            }

            if ($row['category_id']) {
                $organizedData[$serviceId]['categories'][] = [
                    'category_id' => $row['category_id'],
                    'category_name' => $row['category_name'],
                    'category_description' => $row['category_description']
                ];
            }
        }

        // Reindexar el array para que sea un array numérico
        $result = array_values($organizedData);

        return $result;
    }

    //getavailableservicedays
    public function getAvailableServiceDays($serviceId)
    {
        try {
            $sql = "SELECT 
                        s.duration, 
                        COALESCE(us.available_days, s.available_days) AS available_days,
                        us.is_active AS user_service_active,
                        s.is_enabled AS service_enabled
                    FROM services s
                    LEFT JOIN user_services us ON us.service_id = s.id AND us.user_id = :user_id
                    WHERE s.id = :service_id";

            $this->db->query($sql);
            $this->db->bind(':service_id', $serviceId);
            $this->db->bind(':user_id', $this->user_id);
            $result = $this->db->single();

            if (!$result) {
                throw new Exception("Service not found");
            }

            // Verificar si el servicio está habilitado tanto a nivel general como para el usuario
            if ((isset($result->user_service_active) && $result->user_service_active === 0) ||
                (isset($result->service_enabled) && $result->service_enabled === 0)
            ) {
                throw new Exception("Service is not available");
            }

            return $result;
        } catch (Exception $e) {
            // Puedes loggear el error aquí si es necesario
            error_log("Error in getAvailableServiceDays: " . $e->getMessage());
            return false;
        }
    }

    public function getCompanyServices()
    {
        $sql = "SELECT id, name FROM services 
                WHERE company_id = :company_id 
                AND is_enabled = 1
                ORDER BY name";

        $this->db->query($sql);
        $this->db->bind(':company_id', $this->company_id);
        return $this->db->resultSet();
    }

    public function getUserAssignedServices()
    {
        $sql = "SELECT service_id, available_days, is_active 
                FROM user_services 
                WHERE user_id = :user_id";

        $this->db->query($sql);
        $this->db->bind(':user_id', $this->user_id);
        $result = $this->db->resultSet();


        $formatted = [];
        foreach ($result as $row) {
            // Convertir los días disponibles (string "1,2,3" a array)
            $daysArray = explode(',', $row['available_days']);
            $daysStatus = [];

            foreach ($daysArray as $day) {
                if (is_numeric($day)) {
                    $daysStatus[$day] = true;
                }
            }

            $formatted[$row['service_id']] = [
                'checked' => (bool)$row['is_active'],
                'days' => $daysStatus
            ];
        }

        return $formatted;
    }


    public function saveServices($servicesData)
    {
        try {
            foreach ($servicesData['service_name'] as $serviceId => $serviceName) {
                $isEnabled = isset($servicesData['service_enabled'][$serviceId]) ? 1 : 0;

                // Convertimos el array de días a una cadena "1,2,3"
                $availableDays = isset($servicesData['available_service_day'][$serviceId])
                    ? implode(',', $servicesData['available_service_day'][$serviceId])
                    : '';

                // Convertir horas y minutos a minutos totales
                $hours = isset($servicesData['service_duration_hours'][$serviceId])
                    ? (int)$servicesData['service_duration_hours'][$serviceId]
                    : 0;
                $minutes = isset($servicesData['service_duration_minutes'][$serviceId])
                    ? (int)$servicesData['service_duration_minutes'][$serviceId]
                    : 0;

                $totalDuration = ($hours * 60) + $minutes; // Total de minutos

                if (strpos($serviceId, 'new-service') !== false) {
                    // Nuevo servicio
                    $newServiceId = $this->addService(
                        $serviceName,
                        $totalDuration, // Guardar la duración como minutos
                        $servicesData['service_observations'][$serviceId],
                        $isEnabled,
                        $availableDays
                    );

                    // Agregar categorías si están presentes
                    if (isset($servicesData['category_name'][$serviceId])) {
                        foreach ($servicesData['category_name'][$serviceId] as $index => $categoryName) {
                            $this->addCategory(
                                $newServiceId,
                                $categoryName,
                                $servicesData['category_description'][$serviceId][$index]
                            );
                        }
                    }
                } else {
                    // Actualizar servicio existente
                    $this->updateService(
                        $serviceId,
                        $serviceName,
                        $totalDuration, // Guardar la duración como minutos
                        $servicesData['service_observations'][$serviceId],
                        $isEnabled,
                        $availableDays
                    );

                    // Actualizar categorías si están presentes
                    if (isset($servicesData['category_name'][$serviceId])) {
                        foreach ($servicesData['category_name'][$serviceId] as $index => $categoryName) {
                            if (strpos($index, 'new-category') !== false) {
                                $this->addCategory(
                                    $serviceId,
                                    $categoryName,
                                    $servicesData['category_description'][$serviceId][$index]
                                );
                            } else {
                                $this->updateCategory(
                                    $index,
                                    $categoryName,
                                    $servicesData['category_description'][$serviceId][$index]
                                );
                            }
                        }
                    }
                }
            }

            return json_encode(['success' => true, 'message' => 'Servicios guardados exitosamente.']);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Error al guardar los servicios: ' . $e->getMessage()]);
        }
    }


    private function addService($name, $duration, $observations, $isEnabled, $availableDays)
    {
        try {
            $this->db->query("INSERT INTO services (company_id, name, duration, observations, is_enabled, available_days) VALUES (:company_id, :name, :duration, :observations, :is_enabled, :available_days)");
            $this->db->bind(':company_id', $this->company_id);
            $this->db->bind(':name', $name);
            $this->db->bind(':duration', $duration);
            $this->db->bind(':observations', $observations);
            $this->db->bind(':is_enabled', $isEnabled);
            $this->db->bind(':available_days', $availableDays);
            $this->db->execute();
            return $this->db->lastInsertId();
        } catch (Exception $e) {
            // Aquí puedes manejar el error, por ejemplo, registrarlo en un log
            error_log("Error al agregar el servicio: " . $e->getMessage());

            // Luego, puedes lanzar una excepción o devolver un mensaje de error
            throw new Exception('No se pudo agregar el servicio.');
        }
    }

    private function updateService($serviceId, $name, $duration, $observations, $isEnabled, $availableDays)
    {
        try {

            $stmt = $this->db->query("UPDATE services SET name = :name, duration = :duration, observations = :observations, is_enabled = :is_enabled, available_days = :available_days WHERE id = :service_id AND company_id = :company_id");
            $this->db->bind(':service_id', $serviceId);
            $this->db->bind(':company_id', $this->company_id);
            $this->db->bind(':name', $name);
            $this->db->bind(':duration', $duration);
            $this->db->bind(':observations', $observations);
            $this->db->bind(':is_enabled', $isEnabled);
            $this->db->bind(':available_days', $availableDays);
            $this->db->execute();
        } catch (Exception $e) {
            // Aquí puedes manejar el error, por ejemplo, registrarlo en un log
            error_log("Error al actualizar el servicio: " . $e->getMessage());

            // Luego, puedes lanzar una excepción o devolver un mensaje de error
            throw new Exception('No se pudo actualizar el servicio.');
        }
    }

    private function addCategory($serviceId, $categoryName, $categoryDescription)
    {
        try {

            $stmt = $this->db->query("INSERT INTO service_categories (service_id, category_name, category_description) VALUES (:service_id, :category_name, :category_description)");
            $this->db->bind(':service_id', $serviceId);
            $this->db->bind(':category_name', $categoryName);
            $this->db->bind(':category_description', $categoryDescription);
            $this->db->execute();
        } catch (PDOException $e) {
            // Aquí puedes manejar el error, por ejemplo, registrarlo en un log
            error_log("Error al agregar la categoría: " . $e->getMessage());

            // Luego, puedes lanzar una excepción o devolver un mensaje de error
            throw new Exception('No se pudo agregar la categoría.');
        }
    }

    private function updateCategory($categoryId, $categoryName, $categoryDescription)
    {
        try {
            $stmt = $this->db->query("UPDATE service_categories SET category_name = :category_name, category_description = :category_description WHERE id = :category_id");
            $this->db->bind(':category_id', $categoryId);
            $this->db->bind(':category_name', $categoryName);
            $this->db->bind(':category_description', $categoryDescription);
            $this->db->execute();
        } catch (Exception $e) {
            // Aquí puedes manejar el error, por ejemplo, registrarlo en un log
            error_log("Error al actualizar la categoría: " . $e->getMessage());

            // Luego, puedes lanzar una excepción o devolver un mensaje de error
            throw new Exception('No se pudo actualizar la categoría.');
        }
    }

    public function saveUserAssignments($assignments)
    {
        $this->db->beginTransaction();

        try {
            // 1. Eliminar asignaciones no presentes en los nuevos datos
            $sqlDelete = "DELETE FROM user_services WHERE user_id = :user_id";

            if (!empty($assignments)) {
                $serviceIds = array_keys($assignments);
                $namedParams = [];

                // Crear parámetros nombrados dinámicamente
                foreach ($serviceIds as $i => $id) {
                    $paramName = ":service_id_" . $i;
                    $namedParams[$paramName] = $id;
                }

                $placeholders = implode(',', array_keys($namedParams));
                $sqlDelete .= " AND service_id NOT IN ($placeholders)";
            }

            // Preparar la consulta
            $this->db->query($sqlDelete);

            // Bind del user_id
            $this->db->bind(':user_id', $this->user_id, PDO::PARAM_INT);

            // Bind de los service_ids usando los nombres de parámetros creados
            if (!empty($assignments)) {
                foreach ($namedParams as $param => $value) {
                    $this->db->bind($param, $value, PDO::PARAM_INT);
                }
            }

            // Ejecutar la consulta
            $this->db->execute();

            // 2. Insertar o actualizar asignaciones
            foreach ($assignments as $serviceId => $data) {
                // Verificar si ya existe la asignación
                $sqlCheck = "SELECT COUNT(*) as count FROM user_services 
                             WHERE user_id = :user_id AND service_id = :service_id";
                $this->db->query($sqlCheck);
                $this->db->bind(':user_id', $this->user_id);
                $this->db->bind(':service_id', $serviceId, PDO::PARAM_INT);
                $this->db->execute();
                $exists = $this->db->single()['count'] > 0;
                // $stmtCheck->bindParam(':user_id', $userId, PDO::PARAM_INT);
                // $stmtCheck->bindParam(':service_id', $serviceId, PDO::PARAM_INT);
                // $stmtCheck->execute();
                // $exists = $stmtCheck->fetch(PDO::FETCH_ASSOC)['count'] > 0;

                if ($exists) {
                    // Actualizar asignación existente
                    $sql = "UPDATE user_services SET
                            available_days = :available_days,
                            is_active = :is_active,
                            updated_at = NOW()
                            WHERE user_id = :user_id AND service_id = :service_id";
                } else {
                    // Nueva asignación
                    $sql = "INSERT INTO user_services (
                            user_id, service_id, available_days, is_active, created_at
                            ) VALUES (
                            :user_id, :service_id, :available_days, :is_active, NOW()
                            )";
                }

                $this->db->query($sql);
                $this->db->bind(':user_id', $this->user_id, PDO::PARAM_INT);
                $this->db->bind(':service_id', $serviceId, PDO::PARAM_INT);
                $this->db->bind(':available_days', $data['available_days']);
                $this->db->bind(':is_active', $data['is_active'], PDO::PARAM_INT);
                $this->db->execute();
                // $stmt = $this->db->prepare($sql);
                // $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
                // $stmt->bindParam(':service_id', $serviceId, PDO::PARAM_INT);
                // $stmt->bindParam(':available_days', $data['available_days']);
                // $stmt->bindParam(':is_active', $data['is_active'], PDO::PARAM_INT);
                // $stmt->execute();
            }

            $this->db->endTransaction();
            return true;
        } catch (Exception $e) {
            $this->db->cancelTransaction();
            error_log("Error saving assignments: " . $e->getMessage());
            return false;
        }
    }


    public function checkAppointments($serviceId)
    {
        try {

            $this->db->query("
            SELECT COUNT(*) AS count
            FROM appointments
            WHERE id_service = :service_id
            ");
            $this->db->bind(':service_id', $serviceId);
            $this->db->execute();
            $result = $this->db->single();

            return $result['count'] > 0;
        } catch (Exception $e) {
            // Aquí puedes manejar el error, por ejemplo, registrarlo en un log
            error_log("Error al verificar las citas: " . $e->getMessage());

            // Luego, puedes lanzar una excepción o devolver un mensaje de error
            throw new Exception('No se pudo verificar las citas.');
        }
    }

    public function deleteService($serviceId)
    {
        try {
            // Eliminar el servicio
            $this->db->query("
                DELETE FROM services
                WHERE id = :service_id
            ");
            $this->db->bind(':service_id', $serviceId);
            $this->db->execute();

            // La eliminación de las categorías asociadas debería ser automática debido a ON DELETE CASCADE
        } catch (Exception $e) {
            // Manejo del error
            error_log("Error al eliminar el servicio: " . $e->getMessage());
            throw new Exception('No se pudo eliminar el servicio.');
        }
    }

    public function deleteCategory($categoryId)
    {
        try {
            $this->db->query("DELETE FROM service_categories WHERE id = :category_id");
            $this->db->bind(':category_id', $categoryId);
            $this->db->execute();
        } catch (Exception $e) {
            // Aquí puedes manejar el error, por ejemplo, registrarlo en un log
            error_log("Error al eliminar la categoría: " . $e->getMessage());

            // Luego, puedes lanzar una excepción o devolver un mensaje de error
            throw new Exception('No se pudo eliminar la categoría.');
        }
    }

    private function formatDuration($minutes)
    {
        $hours = intdiv($minutes, 60); // Calcula las horas
        $remainingMinutes = $minutes % 60; // Calcula los minutos restantes

        return [
            'hours' => $hours,
            'minutes' => $remainingMinutes
        ];
    }
}
