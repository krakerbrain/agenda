<?php
require_once dirname(__DIR__, 2) . '/configs/init.php';
require_once dirname(__DIR__, 2) . '/access-token/seguridad/JWTAuth.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';

$baseUrl = ConfigUrl::get();
$auth = new JWTAuth();
$auth->validarTokenUsuario();
?>

<div class="max-w-4xl mx-auto relative">
    <!-- Contenedor superior fijo - ahora funciona correctamente -->
    <div class="flex justify-between items-center sticky bg-white z-50 p-3 shadow rounded"
        style="top: calc(var(--spacing) * 14.3);">
        <!-- Botón de agregar servicio a la izquierda -->
        <button type="button"
            class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors shadow-md"
            id="addServiceButton">
            <i class="fa fa-plus mr-2"></i> Agregar Nuevo Servicio
        </button>
        <!-- Botón de ayuda a la derecha -->
        <div class="hidden">
            <a tabindex="0" role="button" class="btn" data-bs-trigger="focus" data-bs-placement="left"
                data-bs-toggle="popover" data-bs-title="Servicios"
                data-bs-content="Aquí podrá configurar todos los servicios que prestes con sus categorías, descripciones y duración de los servicios (en horas). Estos servicios más las configuraciones del calendario le permitirán a su cliente hacer correctamente la reserva">
                <i class="fa fa-circle-question text-primary text-2xl"></i>
            </a>
        </div>
    </div>

    <!-- Contenido desplazable -->
    <div class="pt-16">
        <!-- Espacio para el header fijo -->
        <form id="servicesForm" method="POST">
            <input type="hidden" value="" id="tempId">
            <div class="overflow-x-auto rounded-lg md:border md:border-gray-200 md:shadow-sm">
                <div id="servicesContainer" class="pt-4"></div>
            </div>
        </form>
    </div>

    <?php include dirname(__DIR__, 2) . '/includes/modal-servicios.php'; ?>
</div>