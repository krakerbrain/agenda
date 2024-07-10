<?php
require_once dirname(__DIR__) . '/classes/DatabaseSessionManager.php';
require_once dirname(__DIR__) . '/classes/ConfigUrl.php';
$baseUrl = ConfigUrl::get();
$manager = new DatabaseSessionManager();
$manager->startSession();
$conn = $manager->getDB();

$sesion = isset($_SESSION['company_id']);
if (!$sesion) {
    header("Location: " . $baseUrl . "login/index.php");
}

$sql = $conn->prepare("SELECT * FROM companies WHERE id = :company_id  AND is_active = 1");
$sql->bindParam(':company_id', $_SESSION['company_id']);
$sql->execute();
$company = $sql->fetch(PDO::FETCH_ASSOC);

if (!$company) {
    echo "Empresa no encontrada o inactiva.";
    exit;
}

// Obtener los servicios existentes de la empresa
$servicesSql = $conn->prepare("SELECT * FROM services WHERE company_id = $company[id]");
$servicesSql->execute();
$services = $servicesSql->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración de Empresa</title>
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>assets/css/form.css">
</head>

<body>
    <div class="form-container">
        <h2>Configuración de Empresa</h2>
        <form id="companyConfigForm">
            <input type="hidden" name="company_id" id="company_id" value="<?= $company['id']; ?>">
            <h3>Días de Trabajo</h3>
            <?php
            $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            foreach ($daysOfWeek as $day) {
                $checked = in_array($day, explode(',', $company['work_days'])) ? 'checked' : '';
                echo "<label><input type='checkbox' name='work_days[]' value='$day' $checked> $day</label>";
            }
            ?>

            <h3>Modo de Horario</h3>
            <label><input type="radio" name="schedule_mode" value="free" <?php echo $company['schedule_mode'] == 'free' ? 'checked' : ''; ?>> Libre Elección</label>
            <label><input type="radio" name="schedule_mode" value="blocks" <?php echo $company['schedule_mode'] == 'blocks' ? 'checked' : ''; ?>> Bloques de Horarios</label>

            <h3>Disponibilidad del calendario</h3>
            <label for="calendar_days_available">Días disponibles para reservar:</label>
            <input type="number" id="calendar_days_available" name="calendar_days_available" value="<?php echo $company['calendar_days_available']; ?>">

            <div id="freeSchedule">
                <h3>Horas de Trabajo</h3>
                <label for="work_start">Hora de Inicio:</label>
                <input type="time" id="work_start" name="work_start" value="<?php echo htmlspecialchars($company['work_start']); ?>" required>
                <label for="work_end">Hora de Fin:</label>
                <input type="time" id="work_end" name="work_end" value="<?php echo htmlspecialchars($company['work_end']); ?>" required>
                <label for="break_start">Hora de Inicio del Descanso:</label>
                <input type="time" id="break_start" name="break_start" value="<?php echo htmlspecialchars($company['break_start']); ?>">
                <label for="break_end">Hora de Fin del Descanso:</label>
                <input type="time" id="break_end" name="break_end" value="<?php echo htmlspecialchars($company['break_end']); ?>">
            </div>

            <h3>Fechas Bloqueadas</h3>
            <div id="blockedDatesContainer">
                <?php
                $blocked_dates = explode(',', $company['blocked_dates']);
                foreach ($blocked_dates as $blocked_date) {
                    echo "<div class='blocked-date' style='display: flex; align-items: end;'>
                                    <input type='date' name='blocked_dates[]' value='$blocked_date' >
                                    <button type='button' class='remove-date' style='margin-left: 10px;'>Eliminar</button>
                                  </div>";
                }
                ?>
            </div>
            <button type="button" id="addBlockedDate">Añadir Fecha Bloqueada</button>

            <h3>Servicios</h3>
            <table id="servicesTable">
                <thead>
                    <tr>
                        <th>Nombre del Servicio</th>
                        <th>Duración (horas)</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($services as $service) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($service['name']); ?></td>
                            <td><?php echo htmlspecialchars($service['duration']); ?></td>
                            <td><button type="button" class="delete-service" data-id="<?php echo $service['id']; ?>">Eliminar</button></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <label for="service_name">Nombre del Servicio:</label>
            <input type="text" id="service_name" name="service_name">
            <label for="service_duration">Duración del Servicio (horas):</label>
            <input type="number" id="service_duration" name="service_duration">

            <button type="submit">Guardar Configuración</button>
        </form>
    </div>

    <script>

    </script>
</body>

</html>