<?php
require_once dirname(__DIR__, 2) . '/configs/init.php';
require_once dirname(__DIR__, 2) . '/access-token/seguridad/JWTAuth.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';

$baseUrl = ConfigUrl::get();
$auth = new JWTAuth();
$auth->validarTokenUsuario();
?>

<div class="max-w-4xl mx-auto px-4 py-8">

    <div class="flex justify-between items-end gap-2 mb-6">
        <h2 class="font-medium text-xl text-gray-800">Notas para correos</h2>
        <a tabindex="0" role="button" data-bs-trigger="focus" class="focus:outline-none" data-bs-toggle="popover"
            data-bs-title="Notas para correos y eventos"
            data-bs-content="Las notas se agregan al final de los correos para proporcionar información adicional al cliente. En correos de reserva, puedes incluir detalles importantes como requisitos previos o pagos necesarios. Para eventos como cursos o charlas, puedes añadir indicaciones específicas, como horarios, materiales requeridos o enlaces de acceso.">
            <i class="fa fa-circle-question text-blue-600 text-xl hover:text-blue-700"></i>
        </a>
    </div>
    <!-- Encabezado con título y ayuda -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden flex flex-col" style="height: 80vh;">
        <!-- Header con controles -->
        <div
            class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex flex-col sm:flex-row justify-between items-end sm:items-center gap-4">

            <!-- Selector de tipo de correo -->
            <div class="w-full sm:w-auto">
                <label for="tipoCorreo" class="block text-sm font-medium text-gray-700 mb-1">Selecciona el tipo de
                    correo:</label>
                <select id="tipoCorreo"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="reserva" data-type="companies">Reserva de Cita</option>
                    <option value="confirmacion" data-type="companies">Confirmación de Cita</option>
                    <option value="reserva_evento" data-type="unique_events">Reserva de Evento Único</option>
                    <option value="confirmacion_evento" data-type="unique_events">Confirmación de Evento Único</option>
                </select>
            </div>
            <!-- Contenedor de botones -->
            <div class="w-full sm:w-auto flex justify-between gap-2">
                <button type="button" id="agregarNota"
                    class="w-1/2 sm:w-auto inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-xs md:text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 cursor-pointer">
                    <i class="fa fa-plus mr-2"></i> Agregar Nota
                </button>

                <button id="saveNotes"
                    class="w-1/2 sm:w-auto inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-xs md:text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 cursor-pointer">
                    Guardar Notas
                </button>
            </div>

        </div>


        <div class="flex-1 overflow-y-auto p-6">
            <!-- Contenedor para selector de evento (dinámico) -->
            <div id="eventoSelectContainer" class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 hidden">
                <!-- Se mostrará solo cuando sea necesario -->
            </div>

            <!-- Formulario de notas -->
            <form id="formNotas">
                <div id="notasContainer" class="space-y-4">
                    <!-- Las notas se cargarán aquí dinámicamente -->
                </div>
            </form>
        </div>
    </div>

    <style>
        .mailCard {
            transition: all 0.2s ease;
        }

        .mailCard:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
    </style>
    <?php include dirname(__DIR__, 2) . '/includes/modal-info.php'; ?>