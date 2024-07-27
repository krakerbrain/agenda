<?php
require_once dirname(__DIR__, 2) . '/classes/DatabaseSessionManager.php';
$manager = new DatabaseSessionManager();
$manager->startSession();
$conn = $manager->getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $services = $_POST['services'] ?? [];
    $newService = $_POST['new_service'] ?? [];
    $newCategories = $_POST['new_category'] ?? [];

    // Update existing services
    foreach ($services as $serviceId => $serviceData) {
        $updateService = $conn->prepare("
            UPDATE services
            SET name = :name, duration = :duration, observations = :observations
            WHERE id = :service_id AND company_id = :company_id
        ");
        $updateService->execute([
            ':name' => $serviceData['name'],
            ':duration' => $serviceData['duration'],
            ':observations' => $serviceData['observations'],
            ':service_id' => $serviceId,
            ':company_id' => $_SESSION['company_id'],
        ]);

        // Update or insert categories for each service
        foreach ($serviceData['categories'] as $categoryId => $categoryData) {
            if ($categoryId === "new") {
                // Insert new category
                $insertCategory = $conn->prepare("
                    INSERT INTO service_categories (service_id, category_name, category_description)
                    VALUES (:service_id, :category_name, :category_description)
                ");
                $insertCategory->execute([
                    ':service_id' => $serviceId,
                    ':category_name' => $categoryData['name'],
                    ':category_description' => $categoryData['description'],
                ]);
            } else {
                // Update existing category
                $updateCategory = $conn->prepare("
                    UPDATE service_categories
                    SET category_name = :category_name, category_description = :category_description
                    WHERE id = :category_id
                ");
                $updateCategory->execute([
                    ':category_name' => $categoryData['name'],
                    ':category_description' => $categoryData['description'],
                    ':category_id' => $categoryId,
                ]);
            }
        }
    }

    // Insert new service
    if (!empty($newService['name'])) {
        $insertService = $conn->prepare("
            INSERT INTO services (name, duration, observations, company_id)
            VALUES (:name, :duration, :observations, :company_id)
        ");
        $insertService->execute([
            ':name' => $newService['name'],
            ':duration' => $newService['duration'],
            ':observations' => $newService['observations'],
            ':company_id' => $_SESSION['company_id'],
        ]);

        $newServiceId = $conn->lastInsertId();

        // Insert new categories for the new service
        foreach ($newCategories['name'] as $index => $categoryName) {
            $categoryDescription = $newCategories['description'][$index];
            $insertCategory = $conn->prepare("
                INSERT INTO service_categories (service_id, category_name, category_description)
                VALUES (:service_id, :category_name, :category_description)
            ");
            $insertCategory->execute([
                ':service_id' => $newServiceId,
                ':category_name' => $categoryName,
                ':category_description' => $categoryDescription,
            ]);
        }
    }

    echo json_encode(['success' => true]);
    exit;
}
