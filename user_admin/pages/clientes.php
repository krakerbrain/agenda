<?php
require_once dirname(__DIR__, 2) . '/configs/init.php';
require_once dirname(__DIR__, 2) . '/access-token/seguridad/JWTAuth.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';

$baseUrl = ConfigUrl::get();
$auth = new JWTAuth();
$auth->validarTokenUsuario();

?>

<div class="container mt-4">
    <ul class="nav nav-tabs" id="customerTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#todos" role="tab">Todos</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#incidencias" role="tab">Con
                Incidencias</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#blocked" role="tab">Bloqueados</button>
        </li>
    </ul>
    <form id="searchCustomerForm" class="d-lg-flex justify-content-between pt-2">
        <div class="w-100">
            <input class="form-control form-control-sm" type="text" id="name" name="name" placeholder="Buscar nombre..."
                autocomplete="off">
        </div>
        <div class="w-100">
            <input class="form-control form-control-sm" type="text" id="phone" name="phone"
                placeholder="Buscar teléfono..." autocomplete="off">
        </div>
        <div class="w-100">
            <input class="form-control form-control-sm" type="text" id="mail" name="mail" placeholder="Buscar correo..."
                autocomplete="off">
        </div>
        <div class="w-25">
            <button class="btn btn-sm btn-primary w-100" type="submit" id="searchButton">Buscar</button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr class="head-table">
                    <th>Nombre</th>
                    <th>Teléfono</th>
                    <th>Correo</th>
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
<!-- agregar un modal para clientes -->