<?php
require_once dirname(__DIR__, 2) . '/configs/init.php';
require_once dirname(__DIR__, 2) . '/access-token/seguridad/JWTAuth.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';

$baseUrl = ConfigUrl::get();
$auth = new JWTAuth();
$auth->validarTokenUsuario();
?>

<div class="container mt-4">
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#unconfirmed" role="tab">Por
                Confirmar</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#confirmed" role="tab">Confirmadas</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#past" role="tab">Pasadas</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#all" role="tab">Todas</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#events" role="tab">Eventos</button>
        </li>
    </ul>
    <form id="searchForm" class="d-lg-flex justify-content-between pt-2">
        <div class="d-flex mb-1">
            <div class="w-100">
                <input class="form-control form-control-sm" type="text" id="service" name="service"
                    placeholder="Buscar servicio..." autocomplete="off" style="width: 99%;">
            </div>
            <div class="w-100 ms-xxl-3">
                <input class="form-control form-control-sm" type="text" id="name" name="name"
                    placeholder="Buscar nombre..." autocomplete="off" style="width: 99%;">
            </div>
        </div>
        <div class="d-flex mb-1">
            <div class="w-100">
                <input class="form-control form-control-sm" type="text" id="phone" name="phone"
                    placeholder="Buscar teléfono..." autocomplete="off" style="width: 99%;">
            </div>
            <div class="w-100 ms-xxl-3">
                <input class="form-control form-control-sm" type="text" id="mail" name="mail"
                    placeholder="Buscar correo..." autocomplete="off" style="width: 99%;">
            </div>
        </div>
        <div class="d-flex mb-1">
            <div class="w-100">
                <input class="form-control form-control-sm" type="date" id="date" name="date" style="width: 99%;">
            </div>
            <div class="w-100 ms-xxl-3">
                <input class="form-control form-control-sm" type="time" id="hour" name="hour" style="width: 99%;">
            </div>
        </div>
        <div class="d-flex justify-content-between mb-1">
            <div class="w-100">
                <select class="form-select form-select-sm" id="status" name="status" style="width: 99%;">
                    <option value="all">Seleccionar estado</option>
                    <option value="1">Confirmada</option>
                    <option value="0">Pendiente</option>
                </select>
            </div>
            <div class="ms-xxl-3">
                <button class="btn btn-sm btn-primary" type="submit" id="searchButton">Buscar</button>
            </div>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Servicio</th>
                    <th>Nombre</th>
                    <th>Teléfono</th>
                    <th>Correo</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Estado</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody id="tableContent">
                <!-- Se llenará con JavaScript -->
            </tbody>
            <div id="paginator" class="text-end pt-1">
                <button id="prevPage" class="btn btn-sm btn-success" title="Ir a la página anterior" disabled>
                    <i class="fas fa-arrow-left"></i> <!-- Icono de flecha izquierda -->
                </button>
                <span id="currentPage" class="page-indicator">Página 1</span>
                <button id="nextPage" class="btn btn-sm btn-success" title="Ir a la página siguiente">
                    <i class="fas fa-arrow-right"></i> <!-- Icono de flecha derecha -->
                </button>
            </div>
        </table>
    </div>
</div>
<?php include dirname(__DIR__, 2) . '/includes/modal-appointments.php';
?>