<?php
require_once dirname(__DIR__, 2) . '/configs/init.php';
require_once dirname(__DIR__, 2) . '/access-token/seguridad/JWTAuth.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';

$baseUrl = ConfigUrl::get();
$auth = new JWTAuth();
$auth->validarTokenUsuario();
?>

<div class="max-w-4xl mx-auto">
    <!-- Card contenedora principal -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden flex flex-col" style="height: 85vh;">
        <!-- Header con controles fijos -->
        <div
            class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 sticky top-0 z-10">
            <!-- Botón de agregar servicio -->
            <button type="button" id="addServiceButton"
                class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <!-- w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 -->
                <i class="fa fa-plus mr-2"></i> Agregar Nuevo Servicio
            </button>

            <!-- Botón de ayuda (opcional) -->
            <div class="hidden sm:block">
                <a tabindex="0" role="button" class="btn" data-bs-trigger="focus" data-bs-placement="left"
                    data-bs-toggle="popover" data-bs-title="Servicios"
                    data-bs-content="Aquí podrá configurar todos los servicios que prestes con sus categorías, descripciones y duración de los servicios (en horas). Estos servicios más las configuraciones del calendario le permitirán a su cliente hacer correctamente la reserva">
                    <i class="fa fa-circle-question text-blue-500 text-2xl hover:text-blue-600"></i>
                </a>
            </div>
        </div>

        <!-- Contenido con scroll - una sola columna -->
        <div class="flex-1 overflow-y-auto p-6">
            <form id="servicesForm" method="POST" class="space-y-6">
                <input type="hidden" value="" id="tempId">

                <!-- Contenedor de servicios - una card por línea -->
                <div id="servicesContainer" class="space-y-6">
                    <!-- Las cards de servicios se cargarán aquí dinámicamente -->
                </div>

                <!-- Mensaje cuando no hay servicios -->
                <div id="noServicesMessage" class="text-center py-10 text-gray-500 hidden">
                    No hay servicios configurados. Haz clic en "Agregar Nuevo Servicio" para comenzar.
                </div>
            </form>
        </div>
    </div>
</div>

<?php include dirname(__DIR__, 2) . '/includes/modal-servicios.php'; ?>