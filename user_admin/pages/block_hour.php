<?php
require_once dirname(__DIR__, 2) . '/access-token/seguridad/JWTAuth.php';
require_once dirname(__DIR__, 2) . '/classes/CompanyManager.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';

$baseUrl = ConfigUrl::get();
$company = new CompanyManager();
$auth = new JWTAuth();
$datosUsuario = $auth->validarTokenUsuario();
?>

<!-- Fechas bloqueadas -->
<form id="block-date-form" class="mb-4">
    <div class="text-end">
        <a tabindex="0" role="button" data-bs-trigger="focus" class="btn help" data-bs-toggle="popover"
            data-bs-title="Fechas Bloqueadas"
            data-bs-content="Puedes bloquear fechas específicas o un rango de horas para que no se puedan hacer reservas en esos días">
            <i class="fa fa-circle-question text-primary" style="font-size: 1.5rem;"></i>
        </a>
    </div>

    <!-- Selección de fecha y horas -->
    <div class="mb-3">
        <label for="block-date" class="form-label">Seleccionar Fecha</label>
        <input type="date" id="block-date" name="block_date" class="form-control" required>
    </div>

    <div class="form-check mb-3">
        <input type="checkbox" class="form-check-input" id="all-day" name="all_day" checked>
        <label class="form-check-label" for="all-day">Todo el día</label>
    </div>

    <div id="hour-range" class="row mb-3" style="display: none;">
        <div class="col">
            <label for="start-hour" class="form-label">Inicio</label>
            <input type="time" id="start-hour" name="start_hour" class="form-control">
        </div>
        <div class="col">
            <label for="end-hour" class="form-label">Término</label>
            <input type="time" id="end-hour" name="end_hour" class="form-control">
        </div>
    </div>

    <button type="submit" class="btn btn-primary mb-4">Guardar Hora Bloqueada</button>
</form>
<div>
    <!-- Tabla para mostrar fechas y horas bloqueadas -->
    <h5 class="mb-3">Listado de Fechas y Horas Bloqueadas</h5>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Inicio</th>
                <th>Término</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody id="blocked-dates-list">
            <tr>
                <td colspan="4" class="text-center">No hay fechas bloqueadas.</td>
            </tr>
        </tbody>
    </table>
</div>