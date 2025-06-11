<div class="max-w-4xl mx-auto">
    <!-- Card contenedora principal -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden flex flex-col" style="height: 80vh;">
        <!-- Header con controles -->
        <div
            class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex flex-col sm:flex-row justify-between items-end sm:items-center gap-4">
            <div class="w-full sm:w-auto">
                <label for="userSelect" class="block text-sm font-medium text-gray-700 mb-1">Seleccionar Usuario</label>
                <select id="userSelect"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    <!-- Opciones de usuarios se cargarán dinámicamente -->
                </select>
            </div>

            <button id="saveAssignments"
                class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Guardar Asignaciones
            </button>
        </div>

        <!-- Contenido con scroll -->
        <div class="flex-1 overflow-y-auto p-6">
            <div id="servicesContainer" class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
                <!-- Contenido se cargará dinámicamente -->
            </div>

            <!-- Mensaje cuando no hay servicios -->
            <div id="noServicesMessage" class="hidden text-center py-10 text-gray-500">
                Seleccione un usuario para ver los servicios disponibles
            </div>
        </div>
    </div>
</div>

<!-- Estilos adicionales -->
<style>
    .disabled-day {
        opacity: 0.5;
    }

    .day {
        padding: 0.3125rem;
        border-radius: 0.25rem;
    }

    .day:hover:not(.disabled-day) {
        background-color: #f9fafb;
    }

    .service-card {
        transition: all 0.2s ease;
    }

    .service-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
</style>

<?php include dirname(__DIR__, 2) . '/includes/modal-info.php'; ?>