<?php
require_once dirname(__DIR__, 2) . '/access-token/seguridad/JWTAuth.php';
require_once dirname(__DIR__, 2) . '/classes/CompanyManager.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';

$baseUrl = ConfigUrl::get();
$company = new CompanyManager();
$auth = new JWTAuth();
$datosUsuario = $auth->validarTokenUsuario();

$company_id = $datosUsuario['company_id'];
$company = $company->getAllCompanyData($company_id);

if (!$company) {
    echo "Empresa no encontrada o inactiva.";
    exit;
}

$bgColor = $company['bg_color'];
$fontColor = $company['font_color'];
$btnPrimaryColor = $company['btn1'];
$btnSecondaryColor = $company['btn2'];

// Se calcula la fecha de inicio y termino del periodo para mostrarla al usuario
$fixed_start_day = new DateTime($company['fixed_start_date']);
$fixed_duration = $company['fixed_duration'];

// Calculamos el periodo actual (fecha de inicio y fecha de término)
$period_end = clone $fixed_start_day;
$period_end->modify('+' . ($fixed_duration - 1) . ' days');

// Obtenemos la fecha actual en medianoche para comparación
$current_date = new DateTime();
$current_date->setTime(0, 0, 0);
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

        <!-- confguración de disponibilidad del calenadario -->

        <div class="d-flex align-items-baseline">
            <h3 class="mt-4 mb-3">Disponibilidad del calendario</h3>
            <a tabindex="0" role="button" data-bs-trigger="focus" class="btn help" data-bs-toggle="popover"
                data-bs-title="Disponibilidad"
                data-bs-content="Define un rango de fechas en el que tu empresa estará disponible para reservas. El cliente podrá elegir entre una disponibilidad fija o continua."><i
                    class="fa fa-circle-question text-primary"></i></a>
        </div>
        <div class="d-flex align-items-center mb-3">
            <div class="form-check me-3">
                <input class="form-check-input" type="radio" name="calendar_mode" id="corrido" value="corrido"
                    <?php echo $company['calendar_mode'] == 'corrido' ? 'checked' : ''; ?>>
                <label class="form-check-label" for="corrido">Contínuo</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="calendar_mode" id="fijo" value="fijo"
                    <?php echo $company['calendar_mode'] == 'fijo' ? 'checked' : ''; ?>>
                <label class="form-check-label" for="fijo">Fijo</label>
            </div>
        </div>

        <!-- Input para Días Disponibles (Corrido) -->
        <div id="corridoInput" class="mb-3"
            style="display: <?php echo ($company['calendar_mode'] == 'corrido') ? 'block' : 'none'; ?>;">
            <div class="d-flex align-items-baseline">
                <h5 class="mt-4 mb-3">Configuración de Disponibilidad Continua</h5>
                <a tabindex="0" role="button" data-bs-trigger="focus" class="btn help" data-bs-toggle="popover"
                    data-bs-title="Disponibilidad Continua"
                    data-bs-content="La cantidad de días que elijas determinará el máximo de días con los que los clientes pueden reservar. Por ejemplo, si seleccionas 20 días, el cliente no podrá hacer una cita con más de 20 días de antelación. Recuerda que cada día se abrirá una nueva fecha disponible."><i
                        class="fa fa-circle-question text-primary"></i></a>
            </div>
            <div class="mb-3">
                <label for="calendar_days_available" class="form-label">Días disponibles para reservar:</label>
                <input type="number" class="form-control" id="calendar_days_available" name="calendar_days_available"
                    value="<?php echo $company['calendar_days_available']; ?>">
            </div>
        </div>

        <!-- Configuración de Disponibilidad Fija (Fijo) -->
        <div id="fijoInput" class="container mb-4"
            style="display: <?php echo ($company['calendar_mode'] == 'fijo') ? 'block' : 'none'; ?>;">
            <div class="d-flex align-items-baseline">
                <h5 class="mt-4 mb-3">Configuración de Disponibilidad Fija</h5>
                <a tabindex="0" role="button" data-bs-trigger="focus" class="btn help" data-bs-toggle="popover"
                    data-bs-title="Disponibilidad Fija"
                    data-bs-content="La cantidad de días que elijas limitará el periodo durante el cual los clientes pueden hacer reservas. Al seleccionar una fecha de inicio y la cantidad de días, no se podrán realizar reservas fuera del periodo establecido hasta que se abra uno nuevo, ya sea automáticamente o manualmente. El calendario no avanzará hasta que se inicie un nuevo periodo."><i
                        class="fa fa-circle-question text-primary"></i></a>
            </div>
            <!-- Mostrar el periodo actual -->
            <?php if ($company['calendar_mode'] == 'fijo'): ?>
                <p><strong>Periodo Actual:</strong> del <?php echo $fixed_start_day->format('d-m-Y'); ?> al
                    <?php echo $period_end->format('d-m-Y'); ?></p>
            <?php endif; ?>
            <div class="mb-3">
                <label for="fixed_start_date" class="form-label">Fecha de Inicio:</label>
                <input type="date" class="form-control" id="fixed_start_date" name="fixed_start_date"
                    value="<?php echo $company['fixed_start_date']; ?>">
            </div>
            <div class="mb-3">
                <label for="fixed_duration" class="form-label">Duración (días):</label>
                <input type="number" class="form-control" id="fixed_duration" name="fixed_duration" min="1"
                    value="<?php echo $company['fixed_duration']; ?>">
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="auto_open" name="auto_open"
                    <?php echo $company['auto_open']  ? 'checked' : ''; ?>
                    <?php echo ($company['calendar_mode'] == 'corrido') ? 'disabled' : ''; ?>>
                <label class="form-check-label" for="auto_open">Abrir automáticamente nuevo periodo al
                    finalizar</label>
                <a tabindex="0" role="button" data-bs-trigger="focus" class="btn help ms-2" data-bs-toggle="popover"
                    data-bs-title="Nuevo periodo automático"
                    data-bs-content="Al terminar el periodo actual se iniciará automáticamente un nuevo periodo considerando la cantidad de días preestablecidos. ">
                    <i class="fa fa-circle-question text-primary"></i>
                </a>
            </div>

            <!-- Botón "Abrir Nuevo Periodo" con ícono de información -->
            <div class="d-flex align-items-center mt-3">
                <button type="button" id="openNewPeriod" class="btn btn-warning" data-bs-toggle="modal"
                    data-bs-target="#newPeriodModal"
                    <?php echo ($company['calendar_mode'] == 'corrido') ? 'disabled' : ''; ?>> Abrir Nuevo Periodo
                </button>
                <a tabindex="0" role="button" data-bs-trigger="focus" class="btn help ms-2" data-bs-toggle="popover"
                    data-bs-title="Abrir Nuevo Periodo"
                    data-bs-content="Si deseas comenzar un nuevo periodo antes de que finalice el actual, haz clic en este botón. El periodo actual se extenderá hasta completar el nuevo.">
                    <i class="fa fa-circle-question text-primary"></i>
                </a>
            </div>
        </div>

        <!-- Intervalo de horarios -->
        <div class="d-flex align-items-baseline mt-4">
            <h3 class="mb-3">Opciones de Intervalo de Horarios</h3>
            <a tabindex="0" role="button" data-bs-trigger="focus" class="btn help" data-bs-toggle="popover"
                data-bs-title="Intervalo de Horarios"
                data-bs-content="Selecciona cómo se organizarán los horarios disponibles para las citas. Puedes elegir intervalos fijos de 30, 45 o 60 minutos, o basar los horarios en la duración del servicio."><i
                    class="fa fa-circle-question text-primary"></i></a>
        </div>

        <!-- Radio para usar la duración del servicio -->
        <div class="form-check">
            <input class="form-check-input" type="radio" name="time_step" id="serviceDuration" value=""
                <?php echo is_null($company['time_step']) ? 'checked' : ''; ?>>
            <label class="form-check-label" for="serviceDuration">
                Según la duración del servicio (Ejemplo: si un servicio dura 2 horas → 9:00 - 11:00, 11:00 - 13:00,
                etc.)
            </label>
        </div>

        <!-- Nota que aparece cuando se selecciona "Según la duración del servicio" -->
        <div id="serviceDurationNote" class="alert alert-info mt-2"
            style="display: <?php echo is_null($company['time_step']) ? 'block' : 'none'; ?>;">
            💡 Nota: Si configuraste una hora de descanso, el sistema intentará no asignar citas en ese periodo.
            Esto puede hacer que algunos horarios no aparezcan disponibles cuando elijas la opción "Según la duración
            del servicio".
        </div>

        <!-- Radio para usar intervalos fijos -->
        <div class="form-check">
            <input class="form-check-input" type="radio" name="time_step" id="fixedIntervals" value="fixed"
                <?php echo !is_null($company['time_step']) ? 'checked' : ''; ?>>
            <label class="form-check-label" for="fixedIntervals">
                Intervalos Fijos (Ejemplo: 9:00 - 10:00, 10:00 - 11:00, etc.)
            </label>
        </div>

        <!-- Opciones de intervalo (solo si se seleccionan intervalos fijos) -->
        <div id="fixedIntervalsOptions" class="ms-4 mb-3"
            style="display: <?php echo !is_null($company['time_step']) ? 'block' : 'none'; ?>;">
            <label class="form-label">Selecciona el intervalo:</label>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="time_step_value" id="step30" value="30"
                    <?php echo ($company['time_step'] == 30) ? 'checked' : ''; ?>>
                <label class="form-check-label" for="step30">30 minutos</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="time_step_value" id="step45" value="45"
                    <?php echo ($company['time_step'] == 45) ? 'checked' : ''; ?>>
                <label class="form-check-label" for="step45">45 minutos</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="time_step_value" id="step60" value="60"
                    <?php echo ($company['time_step'] == 60) ? 'checked' : ''; ?>>
                <label class="form-check-label" for="step60">60 minutos</label>
            </div>
        </div>
        <!-- Fin intervalo de horarios -->

        <!-- Configuracion de color de formulario de reserva -->
        <div class="container my-4">
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
                        <button type="button" class="btn btn-primary mt-3" id="resetColors">Restablecer
                            Colores</button>
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
            <input type="text" class="form-control" id="urlToCopy"
                value="<?php echo $baseUrl . 'reservas/' . $company['custom_url']; ?>" readonly>
            <button class="btn btn-outline-secondary copyToClipboard" type="button">Copiar URL</button>
        </div>
        <button type="submit" class="btn btn-success">Guardar Configuración</button>
    </form>
    <?php
    include dirname(__DIR__, 2) . '/includes/modal-nuevo-periodo.php';
    include dirname(__DIR__, 2) . '/includes/modal-configuraciones.php';
    ?>
</div>