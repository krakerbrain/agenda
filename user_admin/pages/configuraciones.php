<?php
require_once dirname(__DIR__, 2) . '/classes/DatabaseSessionManager.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';
$baseUrl = ConfigUrl::get();
$manager = new DatabaseSessionManager();
// $manager->startSession();
session_start();
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

$bgColor = $company['bg_color'];
$fontColor = $company['font_color'];
$btnPrimaryColor = $company['btn1'];
$btnSecondaryColor = $company['btn2'];




?>
<style>
    #example-card {
        background-color: <?= $bgColor ?>;
    }

    #card-title {
        color: <?= $fontColor ?>;
    }

    #card-text {
        color: <?= $fontColor ?>;
    }

    #btn-primary-example {
        background-color: <?= $btnPrimaryColor ?>;
        border-color: <?= $btnPrimaryColor ?>;
        color: <?= $fontColor ?>;
    }

    #btn-secondary-example {
        background-color: <?= $btnSecondaryColor ?>;
        border-color: <?= $btnSecondaryColor ?>;
        color: <?= $fontColor ?>;
    }
</style>
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
            <button type="button" id="addBlockedDate" class="btn btn-primary mb-4">Añadir Fecha Bloqueada</button>
        </div>

        <div class="container mb-4">
            <div class="row">
                <h3 class="mb-3">Color del Formulario</h3>
                <div class="col-md-6 color-inputs d-flex mb-2">
                    <div class="form-color-group w-25">
                        <label for="background-color" class="form-label">Fondo:</label>
                        <input type="color" class="form-control" style="height: 50px" id="background-color" name="background-color" value="<?php echo $company['bg_color'] ?>">
                    </div>
                    <div class="form-color-group w-25">
                        <label for="font-color" class="form-label">Texto:</label>
                        <input type="color" class="form-control" style="height: 50px" id="font-color" name="font-color" value="<?php echo $company['font_color'] ?>">
                    </div>
                    <div class="form-color-group w-25">
                        <label for="btn-primary-color" class="form-label">Btn Anterior:</label>
                        <input type="color" class="form-control" style="height: 50px" id="btn-primary-color" name="btn-primary-color" value="<?php echo $company['btn1'] ?>">
                    </div>
                    <div class="form-color-group w-25">
                        <label for="btn-secondary-color" class="form-label">Btn Siguiente:</label>
                        <input type="color" class="form-control" style="height: 50px" id="btn-secondary-color" name="btn-secondary-color" value="<?php echo $company['btn2'] ?>">
                    </div>
                </div>
                <div class="col-md-6 example-card">
                    <div class="card" id="example-card">
                        <div class="card-body">
                            <h2 class="card-title" id="card-title">Paso 1: Escoge el Servicio</h2>
                            <div class="mb-3">
                                <label for="service" class="form-label" id="card-text">Servicio:</label>
                                <select id="service" name="service" class="form-select mb-4" disabled>
                                    <option value="" selected>Selecciona un servicio</option>

                                </select>
                                <button type=" button" class="btn btn-secondary" id="btn-primary-example">Anterior</button>
                                <button type="button" class="btn btn-primary" id="btn-secondary-example">Siguiente</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-success">Guardar Configuración</button>
    </form>
</div>