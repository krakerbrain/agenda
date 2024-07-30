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
            SELECT s.id AS service_id, s.name AS service_name, s.duration, s.observations, s.is_enabled, 
                   sc.id AS category_id, sc.category_name, sc.category_description
            FROM services s
            LEFT JOIN service_categories sc ON s.id = sc.service_id
            WHERE s.company_id = :company_id
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
        foreach ($servicesData['service_name'] as $serviceId => $serviceName) {
            $isEnabled = isset($servicesData['service_enabled'][$serviceId]) ? 1 : 0;
            if (strpos($serviceId, 'new-service') !== false) {
                // Nuevo servicio
                $newServiceId = $this->addService($serviceName, $servicesData['service_duration'][$serviceId], $servicesData['service_observations'][$serviceId], $isEnabled);

                if (isset($servicesData['category_name'][$serviceId])) {
                    foreach ($servicesData['category_name'][$serviceId] as $categoryIndex => $categoryName) {
                        $this->addCategory($newServiceId, $categoryName, $servicesData['category_description'][$serviceId][$categoryIndex]);
                    }
                }
            } else {
                // Servicio existente
                $this->updateService($serviceId, $serviceName, $servicesData['service_duration'][$serviceId], $servicesData['service_observations'][$serviceId], $isEnabled);

                if (isset($servicesData['category_name'][$serviceId])) {
                    foreach ($servicesData['category_name'][$serviceId] as $categoryIndex => $categoryName) {
                        // Verificar si la categoría es nueva o existente
                        if (strpos($categoryIndex, 'new-category') !== false) {
                            $this->addCategory($serviceId, $categoryName, $servicesData['category_description'][$serviceId][$categoryIndex]);
                        } else {
                            // Update existing category if needed
                        }
                    }
                }
            }
        }
    }

    private function addService($name, $duration, $observations, $isEnabled)
    {
        $stmt = $this->conn->prepare("INSERT INTO services (company_id, name, duration, observations, is_enabled) VALUES (:company_id, :name, :duration, :observations, :is_enabled)");
        $stmt->bindParam(':company_id', $this->company_id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':duration', $duration);
        $stmt->bindParam(':observations', $observations);
        $stmt->bindParam(':is_enabled', $isEnabled);
        $stmt->execute();
        return $this->conn->lastInsertId();
    }

    private function updateService($serviceId, $name, $duration, $observations, $isEnabled)
    {
        $stmt = $this->conn->prepare("UPDATE services SET name = :name, duration = :duration, observations = :observations, is_enabled = :is_enabled WHERE id = :service_id AND company_id = :company_id");
        $stmt->bindParam(':service_id', $serviceId);
        $stmt->bindParam(':company_id', $this->company_id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':duration', $duration);
        $stmt->bindParam(':observations', $observations);
        $stmt->bindParam(':is_enabled', $isEnabled);
        $stmt->execute();
    }

    private function addCategory($serviceId, $categoryName, $categoryDescription)
    {
        $stmt = $this->conn->prepare("INSERT INTO service_categories (service_id, category_name, category_description) VALUES (:service_id, :category_name, :category_description)");
        $stmt->bindParam(':service_id', $serviceId);
        $stmt->bindParam(':category_name', $categoryName);
        $stmt->bindParam(':category_description', $categoryDescription);
        $stmt->execute();
    }

    public function checkAppointments($serviceId)
    {
        $appointmentSql = $this->conn->prepare("
            SELECT COUNT(*) AS count
            FROM appointments
            WHERE id_service = :service_id
        ");
        $appointmentSql->bindParam(':service_id', $serviceId);
        $appointmentSql->execute();
        $result = $appointmentSql->fetch(PDO::FETCH_ASSOC);

        return $result['count'] > 0;
    }

    public function deleteService($serviceId)
    {
        $deleteServiceSql = $this->conn->prepare("
            DELETE FROM services
            WHERE id = :service_id
        ");
        $deleteServiceSql->bindParam(':service_id', $serviceId);
        $deleteServiceSql->execute();

        $deleteCategoriesSql = $this->conn->prepare("
            DELETE FROM service_categories
            WHERE service_id = :service_id
        ");
        $deleteCategoriesSql->bindParam(':service_id', $serviceId);
        $deleteCategoriesSql->execute();
    }

    public function deleteCategory($categoryId)
    {
        $deleteCategorySql = $this->conn->prepare("DELETE FROM service_categories WHERE id = :category_id");
        $deleteCategorySql->bindParam(':category_id', $categoryId);
        $deleteCategorySql->execute();
    }
}
