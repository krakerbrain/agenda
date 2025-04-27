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
                s.available_days,
                sc.id AS category_id,
                sc.category_name,
                sc.category_description
            FROM 
                services s
            LEFT JOIN 
                service_categories sc ON s.id = sc.service_id
            WHERE 
                s.company_id = :company_id
            ORDER BY 
                s.name, sc.category_name
    ");
        $this->db->bind(':company_id', $this->company_id);
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

    public function getServicesWithUserAvailability($companyAvailableDays, $userWorkingDays)
    {
        // 1. Obtener todos los servicios activos de la compañía
        $sql = "SELECT s.id, s.name, s.is_enabled, s.available_days 
                FROM services s
                WHERE s.company_id = :company_id 
                AND s.is_enabled = 1
                ORDER BY s.name";

        $this->db->query($sql);
        $this->db->bind(':company_id', $this->company_id);
        $services = $this->db->resultSet();

        // 2. Obtener asignaciones del usuario
        $sqlAssignments = "SELECT service_id, available_days, is_active 
                          FROM user_services 
                          WHERE user_id = :user_id";

        $this->db->query($sqlAssignments);
        $this->db->bind(':user_id', $this->user_id);
        $assignments = $this->db->resultSet();

        // Procesar asignaciones
        $userAssignments = [];
        foreach ($assignments as $assignment) {
            $days = array_fill_keys(explode(',', $assignment['available_days']), true);
            $userAssignments[$assignment['service_id']] = [
                'is_active' => (bool)$assignment['is_active'],
                'days' => $days
            ];
        }

        // 3. Combinar toda la información
        $result = [];
        foreach ($services as $service) {
            $serviceId = $service['id'];

            // Días que el servicio ofrece
            $serviceDays = array_fill_keys(explode(',', $service['available_days']), true);

            // Días disponibles combinados
            $availableDays = [];
            for ($dayId = 1; $dayId <= 7; $dayId++) {
                // Jerarquía de disponibilidad:
                $isAvailable = $companyAvailableDays[$dayId]['enabled'] &&      // 1. Compañía
                    isset($serviceDays[$dayId]) &&                   // 2. Servicio
                    (!empty($userWorkingDays[$dayId]['enabled']));     // 3. Usuario

                $availableDays[$dayId] = [
                    'company_available' => $companyAvailableDays[$dayId]['enabled'],
                    'service_available' => isset($serviceDays[$dayId]),
                    'user_working' => !empty($userWorkingDays[$dayId]['enabled']),
                    'user_assigned' => $isAvailable &&
                        isset($userAssignments[$serviceId]['days'][$dayId]) &&
                        $userAssignments[$serviceId]['days'][$dayId]
                ];
            }

            $result[] = [
                'id' => $serviceId,
                'name' => $service['name'],
                'is_enabled' => (bool)$service['is_enabled'],
                'user_assignment' => $userAssignments[$serviceId] ?? null,
                'available_days' => $availableDays
            ];
        }

        return $result;
    }
    // public function getCompanyServices()
    // {
    //     $sql = "SELECT id, name, available_days FROM services 
    //             WHERE company_id = :company_id 
    //             AND is_enabled = 1
    //             ORDER BY name";

    //     $this->db->query($sql);
    //     $this->db->bind(':company_id', $this->company_id);
    //     return $this->db->resultSet();
    // }

    // public function getUserAssignedServices()
    // {
    //     $sql = "SELECT service_id, available_days, is_active 
    //             FROM user_services 
    //             WHERE user_id = :user_id";

    //     $this->db->query($sql);
    //     $this->db->bind(':user_id', $this->user_id);
    //     $result = $this->db->resultSet();


    //     $formatted = [];
    //     foreach ($result as $row) {
    //         // Convertir los días disponibles (string "1,2,3" a array)
    //         $daysArray = explode(',', $row['available_days']);
    //         $daysStatus = [];

    //         foreach ($daysArray as $day) {
    //             if (is_numeric($day)) {
    //                 $daysStatus[$day] = true;
    //             }
    //         }

    //         $formatted[$row['service_id']] = [
    //             'checked' => (bool)$row['is_active'],
    //             'days' => $daysStatus
    //         ];
    //     }

    //     return $formatted;
    // }


    public function saveServices($servicesData)
    {
        try {
            $newServiceIds = []; // Solo para almacenar nuevos IDs (temporal => real)

            foreach ($servicesData['service_name'] as $serviceId => $serviceName) {
                // Configuración común para todos los servicios
                $isEnabled = isset($servicesData['service_enabled'][$serviceId]) ? 1 : 0;
                $availableDays = isset($servicesData['available_service_day'][$serviceId])
                    ? implode(',', $servicesData['available_service_day'][$serviceId])
                    : '';

                // Calcular duración
                $hours = (int)($servicesData['service_duration_hours'][$serviceId] ?? 0);
                $minutes = (int)($servicesData['service_duration_minutes'][$serviceId] ?? 0);
                $totalDuration = ($hours * 60) + $minutes;

                if (strpos($serviceId, 'new-service') !== false) {
                    // Nuevo servicio
                    $newServiceId = $this->addService(
                        $serviceName,
                        $totalDuration,
                        $servicesData['service_observations'][$serviceId] ?? '',
                        $isEnabled,
                        $availableDays
                    );
                    $newServiceIds[$serviceId] = $newServiceId; // Mapeo ID temporal => real

                    // Procesar categorías si existen
                    if (isset($servicesData['category_name'][$serviceId])) {
                        foreach ($servicesData['category_name'][$serviceId] as $index => $categoryName) {
                            $this->addCategory(
                                $newServiceId,
                                $categoryName,
                                $servicesData['category_description'][$serviceId][$index] ?? ''
                            );
                        }
                    }
                } else {
                    // Servicio existente
                    $this->updateService(
                        $serviceId,
                        $serviceName,
                        $totalDuration,
                        $servicesData['service_observations'][$serviceId] ?? '',
                        $isEnabled,
                        $availableDays
                    );

                    // Procesar categorías
                    if (isset($servicesData['category_name'][$serviceId])) {
                        foreach ($servicesData['category_name'][$serviceId] as $index => $categoryName) {
                            if (strpos($index, 'new-category') !== false) {
                                $this->addCategory(
                                    $serviceId,
                                    $categoryName,
                                    $servicesData['category_description'][$serviceId][$index] ?? ''
                                );
                            } else {
                                $this->updateCategory(
                                    $index,
                                    $categoryName,
                                    $servicesData['category_description'][$serviceId][$index] ?? ''
                                );
                            }
                        }
                    }
                }
            }

            // Respuesta limpia
            $response = [
                'success' => true,
                'message' => 'Servicios procesados correctamente'
            ];

            // Solo agregamos new_service_ids si hay servicios nuevos
            if (!empty($newServiceIds)) {
                $response['new_service_ids'] = $newServiceIds;
            }

            return json_encode($response);
        } catch (Exception $e) {
            return json_encode([
                'success' => false,
                'message' => 'Error al procesar servicios: ' . $e->getMessage()
            ]);
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

                $assignmentData = [
                    'service_id' => $serviceId,
                    'available_days' => $data['available_days'],
                    'is_active' => $data['is_active']
                ];
                if ($exists) {
                    // Actualizar asignación existente
                    $this->updateUserAssignment($assignmentData);
                } else {
                    // Insertar nueva asignación
                    $this->assignUserToService($assignmentData);
                }
            }

            $this->db->endTransaction();
            return true;
        } catch (Exception $e) {
            $this->db->cancelTransaction();
            error_log("Error saving assignments: " . $e->getMessage());
            return false;
        }
    }

    public function assignUserToService($data)
    {

        try {
            $this->db->query("
                INSERT INTO user_services (user_id, service_id, available_days, is_active, created_at) 
                VALUES (:user_id, :service_id, :available_days, :is_active, NOW())
            ");
            $this->db->bind(':user_id', $this->user_id);
            $this->db->bind(':service_id', $data['service_id']);
            $this->db->bind(':available_days', $data['available_days']);
            $this->db->bind(':is_active', $data['is_active']);
            $this->db->execute();
        } catch (Exception $e) {
            // Aquí puedes manejar el error, por ejemplo, registrarlo en un log
            error_log("Error al asignar el usuario al servicio: " . $e->getMessage());

            // Luego, puedes lanzar una excepción o devolver un mensaje de error
            throw new Exception('No se pudo asignar el usuario al servicio.');
        }
    }

    public function updateUserAssignment($data)
    {
        try {
            $this->db->query("
                UPDATE user_services 
                SET available_days = :available_days, is_active = :is_active, updated_at = NOW() 
                WHERE user_id = :user_id AND service_id = :service_id
            ");
            $this->db->bind(':user_id', $this->user_id);
            $this->db->bind(':service_id', $data['service_id']);
            $this->db->bind(':available_days', $data['available_days']);
            $this->db->bind(':is_active', $data['is_active']);
            $this->db->execute();
        } catch (Exception $e) {
            // Aquí puedes manejar el error, por ejemplo, registrarlo en un log
            error_log("Error al actualizar la asignación del usuario: " . $e->getMessage());

            // Luego, puedes lanzar una excepción o devolver un mensaje de error
            throw new Exception('No se pudo actualizar la asignación del usuario.');
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

    // para get_service_providers.php
    public function getServiceWithDays($serviceId)
    {
        $sql = "SELECT id, name, available_days 
            FROM services 
            WHERE id = :service_id 
            AND company_id = :company_id";

        $this->db->query($sql);
        $this->db->bind(':service_id', $serviceId);
        $this->db->bind(':company_id', $this->company_id);

        return $this->db->single();
    }

    public function getServicesByProvider($userId)
    {
        $sql = "SELECT s.id, s.name 
                FROM services s
                JOIN user_services us ON s.id = us.service_id
                WHERE us.user_id = :user_id
                LIMIT 5";
        $this->db->query($sql);
        $this->db->bind(':user_id', $userId);
        return $this->db->resultSet();
    }
}
