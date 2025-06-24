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
<div class="max-w-4xl mx-auto my-4 p-4">
    <form id="companyConfigForm">
        <input type="hidden" name="company_id" id="company_id" value="<?= $company_id; ?>">
        <div class="flex items-baseline">
            <h3 class="text-xl font-semibold mb-3">Modo de Horario</h3>
            <button type="button" class="help ml-2" data-bs-toggle="popover" data-bs-trigger="focus"
                data-bs-title="Modo de horario"
                data-bs-content="Puedes permitir que el cliente elija libremente la hora de la reserva o puedes elegir bloques de horarios acorde a la duración de los servicios">
                <i class="fa fa-circle-question text-blue-500"></i>
            </button>
        </div>
        <div class="flex items-center mb-2">
            <input class="form-radio h-4 w-4 text-blue-600" type="radio" name="schedule_mode" id="freeChoice"
                value="free" <?php echo $company['schedule_mode'] == 'free' ? 'checked' : ''; ?> disabled>
            <label class="ml-2 text-gray-700" for="freeChoice">Libre Elección</label>
        </div>
        <div class="flex items-center">
            <input class="form-radio h-4 w-4 text-blue-600" type="radio" name="schedule_mode" id="blocks" value="blocks"
                <?php echo $company['schedule_mode'] == 'blocks' ? 'checked' : ''; ?>>
            <label class="ml-2 text-gray-700" for="blocks">Bloques de Horarios</label>
        </div>

        <!-- configuración de disponibilidad del calendario -->
        <div class="flex items-baseline mt-6">
            <h3 class="text-xl font-semibold mb-3">Disponibilidad del calendario</h3>
            <button type="button" class="help ml-2" data-bs-toggle="popover" data-bs-trigger="focus"
                data-bs-title="Disponibilidad"
                data-bs-content="Define un rango de fechas en el que tu empresa estará disponible para reservas. El cliente podrá elegir entre una disponibilidad fija o continua.">
                <i class="fa fa-circle-question text-blue-500"></i>
            </button>
        </div>
        <div class="flex items-center mb-4">
            <div class="flex items-center mr-4">
                <input class="form-radio h-4 w-4 text-blue-600" type="radio" name="calendar_mode" id="corrido"
                    value="corrido" <?php echo $company['calendar_mode'] == 'corrido' ? 'checked' : ''; ?>>
                <label class="ml-2 text-gray-700" for="corrido">Contínuo</label>
            </div>
            <div class="flex items-center">
                <input class="form-radio h-4 w-4 text-blue-600" type="radio" name="calendar_mode" id="fijo" value="fijo"
                    <?php echo $company['calendar_mode'] == 'fijo' ? 'checked' : ''; ?>>
                <label class="ml-2 text-gray-700" for="fijo">Fijo</label>
            </div>
        </div>

        <!-- Input para Días Disponibles (Corrido) -->
        <div id="corridoInput"
            class="mb-4 <?php echo ($company['calendar_mode'] == 'corrido') ? 'block' : 'hidden'; ?>">
            <div class="flex items-baseline">
                <h5 class="text-lg font-medium mt-4 mb-3">Configuración de Disponibilidad Continua</h5>
                <button type="button" class="help ml-2" data-bs-toggle="popover" data-bs-trigger="focus"
                    data-bs-title="Disponibilidad Continua"
                    data-bs-content="La cantidad de días que elijas determinará el máximo de días con los que los clientes pueden reservar. Por ejemplo, si seleccionas 20 días, el cliente no podrá hacer una cita con más de 20 días de antelación. Recuerda que cada día se abrirá una nueva fecha disponible.">
                    <i class="fa fa-circle-question text-blue-500"></i>
                </button>
            </div>
            <div class="mb-4">
                <label for="calendar_days_available" class="block text-sm font-medium text-gray-700 mb-1">Días
                    disponibles para reservar:</label>
                <input type="number"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    id="calendar_days_available" name="calendar_days_available"
                    value="<?php echo $company['calendar_days_available']; ?>">
            </div>
        </div>

        <!-- Configuración de Disponibilidad Fija (Fijo) -->
        <div id="fijoInput" class="mb-6 <?php echo ($company['calendar_mode'] == 'fijo') ? 'block' : 'hidden'; ?>">
            <div class="flex items-baseline">
                <h5 class="text-lg font-medium mt-4 mb-3">Configuración de Disponibilidad Fija</h5>
                <button type="button" class="help ml-2" data-bs-toggle="popover" data-bs-trigger="focus"
                    data-bs-title="Disponibilidad Fija"
                    data-bs-content="La cantidad de días que elijas limitará el periodo durante el cual los clientes pueden hacer reservas. Al seleccionar una fecha de inicio y la cantidad de días, no se podrán realizar reservas fuera del periodo establecido hasta que se abra uno nuevo, ya sea automáticamente o manualmente. El calendario no avanzará hasta que se inicie un nuevo periodo.">
                    <i class="fa fa-circle-question text-blue-500"></i>
                </button>
            </div>
            <!-- Mostrar el periodo actual -->
            <?php if ($company['calendar_mode'] == 'fijo'): ?>
                <p class="mb-4"><strong>Periodo Actual:</strong> del <?php echo $fixed_start_day->format('d-m-Y'); ?> al
                    <?php echo $period_end->format('d-m-Y'); ?></p>
            <?php endif; ?>
            <div class="mb-4">
                <label for="fixed_start_date" class="block text-sm font-medium text-gray-700 mb-1">Fecha de
                    Inicio:</label>
                <input type="date"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    id="fixed_start_date" name="fixed_start_date" value="<?php echo $company['fixed_start_date']; ?>">
            </div>
            <div class="mb-4">
                <label for="fixed_duration" class="block text-sm font-medium text-gray-700 mb-1">Duración
                    (días):</label>
                <input type="number"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    id="fixed_duration" name="fixed_duration" min="1" value="<?php echo $company['fixed_duration']; ?>">
            </div>
            <div class="flex items-center mb-4">
                <input class="form-checkbox h-4 w-4 text-blue-600" type="checkbox" id="auto_open" name="auto_open"
                    <?php echo $company['auto_open']  ? 'checked' : ''; ?>
                    <?php echo ($company['calendar_mode'] == 'corrido') ? 'disabled' : ''; ?>>
                <label class="ml-2 text-gray-700" for="auto_open">Abrir automáticamente nuevo periodo al
                    finalizar</label>
                <button type="button" class="help ml-2" data-bs-toggle="popover" data-bs-trigger="focus"
                    data-bs-title="Nuevo periodo automático"
                    data-bs-content="Al terminar el periodo actual se iniciará automáticamente un nuevo periodo considerando la cantidad de días preestablecidos.">
                    <i class="fa fa-circle-question text-blue-500"></i>
                </button>
            </div>

            <!-- Botón "Abrir Nuevo Periodo" con ícono de información -->
            <div class="flex items-center mt-4">
                <button type="button" id="openNewPeriod"
                    class="px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2"
                    <?php echo ($company['calendar_mode'] == 'corrido') ? 'disabled' : ''; ?>>
                    Abrir Nuevo Periodo
                </button>
                <button type="button" class="help ml-2" data-bs-toggle="popover" data-bs-trigger="focus"
                    data-bs-title="Abrir Nuevo Periodo"
                    data-bs-content="Si deseas comenzar un nuevo periodo antes de que finalice el actual, haz clic en este botón. El periodo actual se extenderá hasta completar el nuevo.">
                    <i class="fa fa-circle-question text-blue-500"></i>
                </button>
            </div>
        </div>

        <!-- Intervalo de horarios -->
        <div class="flex items-baseline mt-8">
            <h3 class="text-xl font-semibold mb-3">Opciones de Intervalo de Horarios</h3>
            <button type="button" class="help ml-2" data-bs-toggle="popover" data-bs-trigger="focus"
                data-bs-title="Intervalo de Horarios"
                data-bs-content="Selecciona cómo se organizarán los horarios disponibles para las citas. Puedes elegir intervalos fijos de 30, 45 o 60 minutos, o basar los horarios en la duración del servicio.">
                <i class="fa fa-circle-question text-blue-500"></i>
            </button>
        </div>

        <!-- Radio para usar la duración del servicio -->
        <div class="flex items-center mb-2">
            <input class="form-radio h-4 w-4 text-blue-600" type="radio" name="time_step" id="serviceDuration" value=""
                <?php echo is_null($company['time_step']) ? 'checked' : ''; ?>>
            <label class="ml-2 text-gray-700" for="serviceDuration">
                Según la duración del servicio (Ejemplo: si un servicio dura 2 horas → 9:00 - 11:00, 11:00 - 13:00,
                etc.)
            </label>
        </div>

        <!-- Nota que aparece cuando se selecciona "Según la duración del servicio" -->
        <div id="serviceDurationNote"
            class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-4 <?php echo is_null($company['time_step']) ? 'block' : 'hidden'; ?>">
            <div class="flex">
                <div class="flex-shrink-0">
                    <span class="text-blue-500">💡</span>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">
                        Nota: Si configuraste una hora de descanso, el sistema intentará no asignar citas en ese
                        periodo.
                        Esto puede hacer que algunos horarios no aparezcan disponibles cuando elijas la opción "Según la
                        duración
                        del servicio".
                    </p>
                </div>
            </div>
        </div>

        <!-- Radio para usar intervalos fijos -->
        <div class="flex items-center">
            <input class="form-radio h-4 w-4 text-blue-600" type="radio" name="time_step" id="fixedIntervals"
                value="fixed" <?php echo !is_null($company['time_step']) ? 'checked' : ''; ?>>
            <label class="ml-2 text-gray-700" for="fixedIntervals">
                Intervalos Fijos (Ejemplo: 9:00 - 10:00, 10:00 - 11:00, etc.)
            </label>
        </div>

        <!-- Opciones de intervalo (solo si se seleccionan intervalos fijos) -->
        <div id="fixedIntervalsOptions"
            class="ml-6 mt-2 mb-4 <?php echo !is_null($company['time_step']) ? 'block' : 'hidden'; ?>">
            <label class="block text-sm font-medium text-gray-700 mb-2">Selecciona el intervalo:</label>
            <div class="flex items-center mb-1">
                <input class="form-radio h-4 w-4 text-blue-600" type="radio" name="time_step_value" id="step30"
                    value="30" <?php echo ($company['time_step'] == 30) ? 'checked' : ''; ?>>
                <label class="ml-2 text-gray-700" for="step30">30 minutos</label>
            </div>
            <div class="flex items-center mb-1">
                <input class="form-radio h-4 w-4 text-blue-600" type="radio" name="time_step_value" id="step45"
                    value="45" <?php echo ($company['time_step'] == 45) ? 'checked' : ''; ?>>
                <label class="ml-2 text-gray-700" for="step45">45 minutos</label>
            </div>
            <div class="flex items-center">
                <input class="form-radio h-4 w-4 text-blue-600" type="radio" name="time_step_value" id="step60"
                    value="60" <?php echo ($company['time_step'] == 60) ? 'checked' : ''; ?>>
                <label class="ml-2 text-gray-700" for="step60">60 minutos</label>
            </div>
        </div>
        <!-- Fin intervalo de horarios -->

        <!-- Configuración de bloqueo por incidencias -->
        <div class="mt-8">
            <div class="flex items-baseline">
                <h3 class="text-xl font-semibold mb-3">Bloqueo por Incidencias</h3>
                <button type="button" class="help ml-2" data-bs-toggle="popover" data-bs-trigger="focus"
                    data-bs-title="Bloqueo por Incidencias"
                    data-bs-content="Puedes configurar el sistema para bloquear automáticamente a usuarios que acumulen un número determinado de incidencias">
                    <i class="fa fa-circle-question text-blue-500"></i>
                </button>
            </div>
            <div class="flex items-center mb-4">
                <input
                    class="form-switch h-5 w-10 rounded-full bg-gray-300 checked:bg-blue-600 transition duration-200 ease-in-out"
                    type="checkbox" role="switch" id="blockUsersSwitch" name="block_users"
                    <?php echo isset($company['block_by_incidents']) && $company['block_by_incidents'] > 0 ? 'checked' : ''; ?>>
                <label class="ml-2 text-gray-700" for="blockUsersSwitch">Bloquear usuarios con más de:</label>
                <div id="incidentsThresholdContainer" class="ml-2">
                    <!-- El input se añadirá dinámicamente aquí -->
                </div>
            </div>
        </div>
        <!-- Fin configuración de bloqueo por incidencias -->

        <!-- Configuracion de color de formulario de reserva -->
        <div class="mt-8">
            <div class="flex items-baseline">
                <h3 class="text-xl font-semibold mb-3">Color del Formulario</h3>
                <button type="button" class="help ml-2" data-bs-toggle="popover" data-bs-trigger="focus"
                    data-bs-title="Color del Formulario"
                    data-bs-content="Puedes personalizar los colores del formulario de reserva para que se ajusten a la identidad de tu empresa. Puedes ver en el cuadro un ejemplo interactivo de la personalización">
                    <i class="fa fa-circle-question text-blue-500"></i>
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="color-inputs grid grid-cols-2 gap-4">
                    <div class="relative">
                        <label for="background-color"
                            class="absolute -top-2 left-2 bg-white px-1 text-xs text-gray-600">Fondo</label>
                        <input type="color" class="w-full h-16 border border-gray-300 rounded-md" id="background-color"
                            name="background-color" value="<?php echo $company['bg_color'] ?>">
                    </div>
                    <div class="relative">
                        <label for="font-color"
                            class="absolute -top-2 left-2 bg-white px-1 text-xs text-gray-600">Texto:</label>
                        <input type="color" class="w-full h-16 border border-gray-300 rounded-md" id="font-color"
                            name="font-color" value="<?php echo $company['font_color'] ?>">
                    </div>
                    <div class="relative">
                        <label for="btn-primary-color"
                            class="absolute -top-2 left-2 bg-white px-1 text-xs text-gray-600">Btn Anterior:</label>
                        <input type="color" class="w-full h-16 border border-gray-300 rounded-md" id="btn-primary-color"
                            name="btn-primary-color" value="<?php echo $company['btn1'] ?>">
                    </div>
                    <div class="relative">
                        <label for="btn-secondary-color"
                            class="absolute -top-2 left-2 bg-white px-1 text-xs text-gray-600">Btn Siguiente:</label>
                        <input type="color" class="w-full h-16 border border-gray-300 rounded-md"
                            id="btn-secondary-color" name="btn-secondary-color" value="<?php echo $company['btn2'] ?>">
                    </div>
                    <div class="col-span-2">
                        <button type="button"
                            class="w-full md:w-auto px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                            id="resetColors">
                            Restablecer Colores
                        </button>
                    </div>
                </div>
                <!-- Ejemplo de vista de card con estilos seleccionados -->
                <div class="example-card">
                    <div class="rounded-lg shadow-md p-6" id="example-card">
                        <div>
                            <h2 class="text-xl font-bold mb-4" id="card-title">Paso 1: Escoge el Servicio</h2>
                            <div>
                                <label for="service" class="block text-sm font-medium mb-1"
                                    id="card-text">Servicio:</label>
                                <select id="service" name="service"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm mb-4" disabled>
                                    <option value="" selected>Selecciona un servicio</option>
                                </select>
                                <div class="flex space-x-2">
                                    <button type="button" class="px-4 py-2 rounded-md"
                                        id="btn-primary-example">Anterior</button>
                                    <button type="button" class="px-4 py-2 rounded-md"
                                        id="btn-secondary-example">Siguiente</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-baseline mt-8">
            <h3 class="text-xl font-semibold mb-3">URL Formulario de Reserva</h3>
            <button type="button" class="help ml-2" data-bs-toggle="popover" data-bs-trigger="focus"
                data-bs-title="URL Formulario de Reserva"
                data-bs-content="Esta es la URL que debes compartir con tus clientes para que puedan hacer reservas en tu empresa. También podrás usarla para crear el botón de reservas de tu pagina web o red social">
                <i class="fa fa-circle-question text-blue-500"></i>
            </button>
        </div>
        <div class="flex mb-6">
            <input type="text"
                class="flex-1 px-3 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                id="urlToCopy" value="<?php echo $baseUrl . 'reservas/' . $company['custom_url']; ?>" readonly>
            <button
                class="px-4 py-2 bg-gray-200 text-gray-700 border border-l-0 border-gray-300 rounded-r-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 copyToClipboard"
                type="button">
                Copiar URL
            </button>
        </div>
        <button type="submit"
            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
            Guardar Configuración
        </button>
    </form>
    <?php
    include dirname(__DIR__, 2) . '/includes/modal-nuevo-periodo.php';
    include dirname(__DIR__, 2) . '/includes/modal-info.php';
    ?>
</div>