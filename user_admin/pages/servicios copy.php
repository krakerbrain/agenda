<?php
require_once dirname(__DIR__, 2) . '/classes/DatabaseSessionManager.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';
$baseUrl = ConfigUrl::get();
$manager = new DatabaseSessionManager();
$manager->startSession();
$conn = $manager->getDB();

$sesion = isset($_SESSION['company_id']);

if (!$sesion) {
    header("Location: " . $baseUrl . "login/index.php");
}

// Obtener los servicios existentes de la empresa junto con sus categorías y descripciones
$servicesSql = $conn->prepare("
    SELECT s.id AS service_id, s.name AS service_name, s.duration, s.observations, 
           sc.id AS category_id, sc.category_name, sc.category_description
    FROM services s
    LEFT JOIN service_categories sc ON s.id = sc.service_id
    WHERE s.company_id = :company_id
");
$servicesSql->bindParam(':company_id', $_SESSION['company_id']);
$servicesSql->execute();
$servicesData = $servicesSql->fetchAll(PDO::FETCH_ASSOC);

// Organizar los datos en un formato más adecuado para la renderización
$services = [];
foreach ($servicesData as $data) {
    $serviceId = $data['service_id'];
    if (!isset($services[$serviceId])) {
        $services[$serviceId] = [
            'service_id' => $data['service_id'],
            'service_name' => $data['service_name'],
            'duration' => $data['duration'],
            'observations' => $data['observations'],
            'categories' => []
        ];
    }
    if ($data['category_id']) {
        $services[$serviceId]['categories'][] = [
            'category_id' => $data['category_id'],
            'category_name' => $data['category_name'],
            'category_description' => $data['category_description']
        ];
    }
}
?>


<div class="container my-5">
    <form id="servicesForm" method="POST" action="index.php" class="border p-4 rounded">
        <table class="table table-borderless table-striped table-sm">
            <thead>
                <tr>
                    <th>Nombre del Servicio</th>
                    <th>Duración (horas)</th>
                    <th>Observaciones</th>
                    <th>Categorías</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody id="servicesTableBody">
                <?php foreach ($services as $service) : ?>
                    <tr class="service-row">
                        <td>
                            <input type="text" class="form-control" name="services[<?php echo $service['service_id']; ?>][name]" value="<?php echo htmlspecialchars($service['service_name']); ?>">
                        </td>
                        <td>
                            <input type="number" class="form-control" name="services[<?php echo $service['service_id']; ?>][duration]" value="<?php echo htmlspecialchars($service['duration']); ?>">
                        </td>
                        <td>
                            <textarea class="form-control" name="services[<?php echo $service['service_id']; ?>][observations]" placeholder="Observaciones servicio"><?php echo htmlspecialchars($service['observations']); ?></textarea>
                        </td>
                        <td>
                            <button type="button" class="btn btn-outline-primary btn-sm add-category" onclick="addCategoryService(this, <?php echo $service['service_id']; ?>)">+
                                Categoría</button>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm delete-service" data-id="<?php echo $service['service_id']; ?>">Eliminar</button>
                        </td>
                    </tr>
                    <?php if (!empty($service['categories'])) : ?>
                        <?php foreach ($service['categories'] as $category) : ?>
                            <tr class="category-item">
                                <td colspan="2">
                                    <input type="text" class="form-control mb-1" name="services[<?php echo $service['service_id']; ?>][categories][<?php echo $category['category_id']; ?>][name]" value="<?php echo htmlspecialchars($category['category_name']); ?>" placeholder="Nombre de la Categoría">
                                </td>
                                <td colspan="2">
                                    <textarea class="form-control mb-1" name="services[<?php echo $service['service_id']; ?>][categories][<?php echo $category['category_id']; ?>][description]" placeholder="Descripción de la Categoría"><?php echo htmlspecialchars($category['category_description']); ?></textarea>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-category" data-category-id="<?php echo $category['category_id']; ?>">Eliminar</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php endforeach; ?>

                <!-- Empty row for new service addition -->
                <tr id="newServiceRow" class="service-row">
                    <td><input type="text" class="form-control" name="new_service[name]" placeholder="Nuevo Servicio">
                    </td>
                    <td><input type="number" class="form-control" name="new_service[duration]" placeholder="Duración">
                    </td>
                    <td><textarea class="form-control" name="new_service[observations]" placeholder="Observaciones"></textarea></td>
                    <td>
                        <button type="button" class="btn btn-outline-primary btn-sm add-category" data-service-id="new">+ Categoría</button>
                    </td>
                    <td>
                        <button type="button" class="btn btn-success btn-sm add-service">Añadir</button>
                    </td>
                </tr>
            </tbody>
        </table>
        <button type="submit" class="btn btn-success mt-3">Guardar Configuración</button>
    </form>
</div>