<?php
require_once dirname(__DIR__, 2) . '/access-token/seguridad/JWTAuth.php';
require_once dirname(__DIR__, 2) . '/classes/DatabaseSessionManager.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';

$baseUrl = ConfigUrl::get();
$manager = new DatabaseSessionManager();
$auth = new JWTAuth();
$datosUsuario = $auth->validarTokenUsuario();

$company_id = $datosUsuario['company_id'];
$conn = $manager->getDB();

$sql = $conn->prepare("SELECT * FROM companies WHERE id = :company_id  AND is_active = 1");
$sql->bindParam(':company_id', $company_id);
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

    .help i {
        font-size: 1.4rem;
    }
</style>
<div class="container my-4">
    <form id="companyConfigForm">
        <input type="hidden" name="company_id" id="company_id" value="<?= $company_id; ?>">
        <div class="d-flex align-items-baseline">
            <h3 class="mb-3">Modo de Horario</h3>
            <a tabindex="0" role="button" data-bs-trigger="focus" class="btn help" data-bs-toggle="popover"
                data-bs-title="Modo de horario"
                data-bs-content="Puedes permitir que el cliente elija libremente la hora de la reserva o puedes elegir bloques de horarios acorde a la duración de los servicios"><i
                    class="fa fa-circle-question text-primary"></i></a>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="schedule_mode" id="freeChoice" value="free"
                <?php echo $company['schedule_mode'] == 'free' ? 'checked' : ''; ?> disabled>
            <label class="form-check-label" for="freeChoice">Libre Elección</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="schedule_mode" id="blocks" value="blocks"
                <?php echo $company['schedule_mode'] == 'blocks' ? 'checked' : ''; ?>>
            <label class="form-check-label" for="blocks">Bloques de Horarios</label>
        </div>
        <div class="d-flex align-items-baseline">
            <h3 class="mt-4 mb-3">Disponibilidad del calendario</h3>
            <a tabindex="0" role="button" data-bs-trigger="focus" class=" btn help" data-bs-toggle="popover"
                data-bs-title="Disponibilidad"
                data-bs-content="La cantidad de días que elijas es la que el cliente tendrá permitido reservar. Si por ejemplo seleccionas 20 días el cliente no podrá hacer una cita con más de 20 días de antelación"><i
                    class="fa fa-circle-question text-primary"></i></a>
        </div>
        <div class="mb-3">
            <label for="calendar_days_available" class="form-label">Días disponibles para reservar:</label>
            <input type="number" class="form-control" id="calendar_days_available" name="calendar_days_available"
                value="<?php echo $company['calendar_days_available']; ?>">
        </div>

        <div class="d-flex align-items-baseline">
            <h3 class="mb-3">Fechas Bloqueadas</h3>
            <a tabindex="0" role="button" data-bs-trigger="focus" class="btn help" data-bs-toggle="popover"
                data-bs-title="Fechas Bloqueadas"
                data-bs-content="Puedes bloquear fechas específicas para que no se puedan hacer reservas en esos días"><i
                    class="fa fa-circle-question text-primary"></i></a>
        </div>
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
                <div class="d-flex align-items-baseline">
                    <h3 class="mb-3">Color del Formulario</h3>
                    <a tabindex="0" role="button" data-bs-trigger="focus" class="btn help" data-bs-toggle="popover"
                        data-bs-title="Color del Formulario"
                        data-bs-content="Puedes personalizar los colores del formulario de reserva para que se ajusten a la identidad de tu empresa. Puedes ver en el cuadro un ejemplo interactivo de la personalización"><i
                            class="fa fa-circle-question text-primary"></i></a>
                </div>
                <div class="col-md-6 mb-2 row">
                    <div class="color-inputs d-flex">
                        <div class="form-floating w-25">
                            <input type="color" class="form-control" id="background-color" name="background-color"
                                value="<?php echo $company['bg_color'] ?>" style="height: 70px">
                            <label for="background-color">Fondo</label>
                        </div>
                        <div class="form-floating w-25">
                            <input type="color" class="form-control" style="height: 70px" id="font-color"
                                name="font-color" value="<?php echo $company['font_color'] ?>">
                            <label for="font-color">Texto:</label>
                        </div>
                        <div class="form-floating w-25">
                            <input type="color" class="form-control" style="height: 70px" id="btn-primary-color"
                                name="btn-primary-color" value="<?php echo $company['btn1'] ?>">
                            <label for="btn-primary-color">Btn Anterior:</label>
                        </div>
                        <div class="form-floating w-25">
                            <input type="color" class="form-control" style="height: 70px" id="btn-secondary-color"
                                name="btn-secondary-color" value="<?php echo $company['btn2'] ?>">
                            <label for="btn-secondary-color">Btn Siguiente:</label>
                        </div>
                    </div>
                    <div class="align-content-end text-md-end">
                        <button type="button" class="btn btn-primary mt-3" id="resetColors">Restablecer Colores</button>
                    </div>
                </div>
                <!-- Ejemplo de vista de card con estilos seleccionados -->
                <div class="col-md-6 example-card">
                    <div class="card" id="example-card">
                        <div class="card-body">
                            <h2 class="card-title" id="card-title">Paso 1: Escoge el Servicio</h2>
                            <div class="mb-3">
                                <label for="service" class="form-label" id="card-text">Servicio:</label>
                                <select id="service" name="service" class="form-select mb-4" disabled>
                                    <option value="" selected>Selecciona un servicio</option>

                                </select>
                                <button type=" button" class="btn btn-secondary"
                                    id="btn-primary-example">Anterior</button>
                                <button type="button" class="btn btn-primary"
                                    id="btn-secondary-example">Siguiente</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex align-items-baseline">
            <h3 class="mb-3">URL Formulario de Reserva</h3>
            <a tabindex="0" role="button" data-bs-trigger="focus" class="btn help" data-bs-toggle="popover"
                data-bs-title="URL Formulario de Reserva"
                data-bs-content="Esta es la URL que debes compartir con tus clientes para que puedan hacer reservas en tu empresa. También podrás usarla para crear el botón de reservas de tu pagina web o red social"><i
                    class="fa fa-circle-question text-primary"></i></a>
        </div>
        <div class="input-group mb-3">
            <input type="text" class="form-control" id="urlToCopy" value="<?php echo $baseUrl . $company['token']; ?>"
                readonly>
            <button class="btn btn-outline-secondary copyToClipboard" type="button">Copiar URL</button>
        </div>
        <button type="submit" class="btn btn-success">Guardar Configuración</button>
    </form>
</div>