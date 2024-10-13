<?php
class Services
{
    private $conn;
    private $company_id;

    public function __construct($conn, $company_id)
    {
        $this->conn = $conn;
        $this->company_id = $company_id;
    }

    public function getServices()
    {
        $servicesSql = $this->conn->prepare("
            SELECT s.id AS service_id, s.name AS service_name, s.duration, s.observations, s.is_enabled, s.available_days,
                   sc.id AS category_id, sc.category_name, sc.category_description
            FROM services s
            LEFT JOIN service_categories sc ON s.id = sc.service_id
            WHERE s.company_id = :company_id
            ORDER BY s.id, sc.id
        ");
        $servicesSql->bindParam(':company_id', $this->company_id);
        $servicesSql->execute();
        $servicesData = $servicesSql->fetchAll(PDO::FETCH_ASSOC);

        $organizedData = [];
        foreach ($servicesData as $row) {
            $serviceId = $row['service_id'];

            if (!isset($organizedData[$serviceId])) {
                $organizedData[$serviceId] = [
                    'service_id' => $row['service_id'],
                    'service_name' => $row['service_name'],
                    'duration' => $row['duration'],
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


    public function saveServices($servicesData)
    {
        try {
            foreach ($servicesData['service_name'] as $serviceId => $serviceName) {
                $isEnabled = isset($servicesData['service_enabled'][$serviceId]) ? 1 : 0;

                // Convertimos el array de días a una cadena "1,2,3"
                $availableDays = isset($servicesData['available_service_day'][$serviceId])
                    ? implode(',', $servicesData['available_service_day'][$serviceId])
                    : '';

                if (strpos($serviceId, 'new-service') !== false) {
                    // Nuevo servicio
                    $newServiceId = $this->addService(
                        $serviceName,
                        $servicesData['service_duration'][$serviceId],
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
                        $servicesData['service_duration'][$serviceId],
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

            $stmt = $this->conn->prepare("INSERT INTO services (company_id, name, duration, observations, is_enabled, available_days) VALUES (:company_id, :name, :duration, :observations, :is_enabled, :available_days)");
            $stmt->bindParam(':company_id', $this->company_id);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':duration', $duration);
            $stmt->bindParam(':observations', $observations);
            $stmt->bindParam(':is_enabled', $isEnabled);
            $stmt->bindParam(':available_days', $availableDays);
            $stmt->execute();
            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            // Aquí puedes manejar el error, por ejemplo, registrarlo en un log
            error_log("Error al agregar el servicio: " . $e->getMessage());

            // Luego, puedes lanzar una excepción o devolver un mensaje de error
            throw new Exception('No se pudo agregar el servicio.');
        }
    }

    private function updateService($serviceId, $name, $duration, $observations, $isEnabled, $availableDays)
    {
        try {

            $stmt = $this->conn->prepare("UPDATE services SET name = :name, duration = :duration, observations = :observations, is_enabled = :is_enabled, available_days = :available_days WHERE id = :service_id AND company_id = :company_id");
            $stmt->bindParam(':service_id', $serviceId);
            $stmt->bindParam(':company_id', $this->company_id);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':duration', $duration);
            $stmt->bindParam(':observations', $observations);
            $stmt->bindParam(':is_enabled', $isEnabled);
            $stmt->bindParam(':available_days', $availableDays);
            $stmt->execute();
        } catch (PDOException $e) {
            // Aquí puedes manejar el error, por ejemplo, registrarlo en un log
            error_log("Error al actualizar el servicio: " . $e->getMessage());

            // Luego, puedes lanzar una excepción o devolver un mensaje de error
            throw new Exception('No se pudo actualizar el servicio.');
        }
    }

    private function addCategory($serviceId, $categoryName, $categoryDescription)
    {
        try {

            $stmt = $this->conn->prepare("INSERT INTO service_categories (service_id, category_name, category_description) VALUES (:service_id, :category_name, :category_description)");
            $stmt->bindParam(':service_id', $serviceId);
            $stmt->bindParam(':category_name', $categoryName);
            $stmt->bindParam(':category_description', $categoryDescription);
            $stmt->execute();
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
            $stmt = $this->conn->prepare("UPDATE service_categories SET category_name = :category_name, category_description = :category_description WHERE id = :category_id");
            $stmt->bindParam(':category_id', $categoryId);
            $stmt->bindParam(':category_name', $categoryName);
            $stmt->bindParam(':category_description', $categoryDescription);
            $stmt->execute();
        } catch (PDOException $e) {
            // Aquí puedes manejar el error, por ejemplo, registrarlo en un log
            error_log("Error al actualizar la categoría: " . $e->getMessage());

            // Luego, puedes lanzar una excepción o devolver un mensaje de error
            throw new Exception('No se pudo actualizar la categoría.');
        }
    }


    public function checkAppointments($serviceId)
    {
        try {

            $appointmentSql = $this->conn->prepare("
            SELECT COUNT(*) AS count
            FROM appointments
            WHERE id_service = :service_id
            ");
            $appointmentSql->bindParam(':service_id', $serviceId);
            $appointmentSql->execute();
            $result = $appointmentSql->fetch(PDO::FETCH_ASSOC);

            return $result['count'] > 0;
        } catch (PDOException $e) {
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
            $deleteServiceSql = $this->conn->prepare("
                DELETE FROM services
                WHERE id = :service_id
            ");
            $deleteServiceSql->bindParam(':service_id', $serviceId);
            $deleteServiceSql->execute();

            // La eliminación de las categorías asociadas debería ser automática debido a ON DELETE CASCADE
        } catch (PDOException $e) {
            // Manejo del error
            error_log("Error al eliminar el servicio: " . $e->getMessage());
            throw new Exception('No se pudo eliminar el servicio.');
        }
    }

    public function deleteCategory($categoryId)
    {
        try {
            $deleteCategorySql = $this->conn->prepare("DELETE FROM service_categories WHERE id = :category_id");
            $deleteCategorySql->bindParam(':category_id', $categoryId);
            $deleteCategorySql->execute();
        } catch (PDOException $e) {
            // Aquí puedes manejar el error, por ejemplo, registrarlo en un log
            error_log("Error al eliminar la categoría: " . $e->getMessage());

            // Luego, puedes lanzar una excepción o devolver un mensaje de error
            throw new Exception('No se pudo eliminar la categoría.');
        }
    }
}
