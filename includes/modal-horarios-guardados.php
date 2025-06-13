<!-- Modal wrapper -->
<div id="saveSchedules"
    class="fixed inset-0 z-50 items-center justify-center hidden pointer-events-none transition-opacity duration-300 opacity-0">

    <!-- Fondo oscuro semi-transparente -->
    <div class="absolute inset-0 bg-black opacity-50"></div>

    <!-- Contenido del modal -->
    <div
        class="relative bg-white rounded-lg shadow-lg w-full max-w-md mx-auto overflow-hidden transform transition-all duration-300 scale-95 translate-y-4 opacity-0">
        <div class="flex items-center justify-between px-4 py-3 border-b">
            <h5 class="text-lg font-semibold" id="saveSchedulesLabel">Horarios Guardados</h5>
            <button type="button"
                class="text-gray-400 hover:text-gray-700 text-2xl leading-none focus:outline-none close-modal"
                aria-label="Close">&times;</button>
        </div>
        <div class="px-4 py-4" id="responseMessage">
            <p>Horario guardado exitosamente</p>
        </div>
        <div class="flex justify-end px-4 py-3 border-t">
            <button type="button"
                class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold px-4 py-2 rounded close-modal">
                Cerrar
            </button>
        </div>
    </div>
</div>