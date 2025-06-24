<!-- Modal para Confirmar Nuevo Periodo -->
<div id="newPeriodModal"
    class="fixed inset-0 z-50 items-center justify-center hidden pointer-events-none transition-opacity duration-300 opacity-0">
    <div class="absolute inset-0 bg-black opacity-50"></div>
    <div
        class="bg-white rounded-lg shadow-lg w-full max-w-md mx-auto overflow-hidden transform transition-all duration-300 scale-95 translate-y-4 opacity-0 p-4">

        <div class="flex items-center justify-between px-4 border-b">
            <h5 class="text-lg font-semibold">Confirmar Nuevo Periodo</h5>
            <button type="button"
                class="text-gray-400 hover:text-gray-700 text-2xl leading-none focus:outline-none close-modal"
                aria-label="Close">&times;</button>
        </div>
        <div class="px-4 py-4 text-gray-700">
            <p>
                El nuevo periodo se establecerá automáticamente por <strong id="diasSeleccionados"></strong> días
                más, según el valor previamente seleccionado.
            </p>
            <p>
                Si deseas ajustar la duración del nuevo periodo, cierra este mensaje, modifica la cantidad de días
                en el campo correspondiente y presiona nuevamente "Abrir Nuevo Periodo".
            </p>
            <p>
                Este periodo se unirá al actual, extendiendo la disponibilidad para reservas de manera continua.
            </p>
        </div>
        <div class="flex justify-end gap-2 mt-4">
            <button type="button"
                class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold px-4 py-2 rounded close-modal">Cerrar</button>
            <button type="button" id="confirmNewPeriod"
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded">Confirmar</button>
        </div>

    </div>
</div>

<!-- Modal check nuevo periodo automatico -->
<div id="autoOpenModal"
    class="fixed inset-0 z-50 items-center justify-center hidden pointer-events-none transition-opacity duration-300 opacity-0">
    <div class="absolute inset-0 bg-black opacity-50"></div>
    <div
        class="bg-white rounded-lg shadow-lg w-full max-w-md mx-auto overflow-hidden transform transition-all duration-300 scale-95 translate-y-4 opacity-0 p-4">

        <div class="flex items-center justify-between px-4 border-b">
            <h5 class="text-lg font-semibold">Confirmación de apertura automática</h5>
            <button type="button"
                class="text-gray-400 hover:text-gray-700 text-2xl leading-none focus:outline-none close-modal"
                aria-label="Close">&times;</button>
        </div>
        <div class="px-4 py-4 text-gray-700">
            Al marcar esta opción, se abrirá automáticamente un nuevo periodo al finalizar el actual. Esto será
            permanente mientras esta casilla esté seleccionada. ¿Deseas continuar?
        </div>
        <div class="modal-footer">
            <button type="button"
                class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold px-4 py-2 rounded close-modal"
                id="cancelAutoOpen">Cerrar</button>
            <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded"
                id="confirmAutoOpen">Confirmar</button>
        </div>
    </div>
</div>
</div>