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
$companyId = $_SESSION['company_id'];
$sql = $conn->prepare("SELECT * FROM companies WHERE id = :company_id  AND is_active = 1");
$sql->bindParam(':company_id', $_SESSION['company_id']);
$sql->execute();
$company = $sql->fetch(PDO::FETCH_ASSOC);

if (!$company) {
    echo "Empresa no encontrada o inactiva.";
    exit;
}

?>
<div class="container my-4">
    <form id="companyConfigForm">
        <input type="hidden" name="company_id" id="company_id" value="<?= $companyId; ?>">

        <h3 class="mb-3">Modo de Horario</h3>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="schedule_mode" id="freeChoice" value="free" <?php echo $company['schedule_mode'] == 'free' ? 'checked' : ''; ?>>
            <label class="form-check-label" for="freeChoice">Libre Elección</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="schedule_mode" id="blocks" value="blocks" <?php echo $company['schedule_mode'] == 'blocks' ? 'checked' : ''; ?>>
            <label class="form-check-label" for="blocks">Bloques de Horarios</label>
        </div>

        <h3 class="mt-4 mb-3">Disponibilidad del calendario</h3>
        <div class="mb-3">
            <label for="calendar_days_available" class="form-label">Días disponibles para reservar:</label>
            <input type="number" class="form-control" id="calendar_days_available" name="calendar_days_available" value="<?php echo $company['calendar_days_available']; ?>">
        </div>


        <h3 class="mb-3">Fechas Bloqueadas</h3>
        <div id="blockedDatesContainer" class="mb-4">
            <?php
            $blocked_dates = explode(',', $company['blocked_dates']);
            foreach ($blocked_dates as $blocked_date) {
                echo "<div class='d-flex align-items-end mb-2'>
                            <input type='date' class='form-control' name='blocked_dates[]' value='$blocked_date'>
                            <button type='button' class='btn btn-danger btn-sm ms-2 remove-date'>Eliminar</button>
                          </div>";
            }
            ?>
        </div>
        <div class="d-flex flex-column row-cols-md-4">
            <button type="button" id="addBlockedDate" class="btn btn-primary mb-4">Añadir Fecha Bloqueada</button>
            <button type="submit" class="btn btn-success">Guardar Configuración</button>
        </div>
    </form>
</div>