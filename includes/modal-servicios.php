<div id="saveServices"
    class="fixed inset-0 z-50 items-center justify-center hidden pointer-events-none transition-opacity duration-300 opacity-0">
    <div class="absolute inset-0 bg-gray-500 opacity-50 transition-opacity"></div>
    <div
        class="bg-white rounded-lg shadow-lg w-full max-w-md mx-auto overflow-hidden transform transition-all duration-300 scale-95">
        <div class="flex items-center justify-between px-4 py-3 border-b">
            <h5 class="text-lg font-semibold" id="saveServicesLabel">Servicios Guardados</h5>
            <button type="button"
                class="text-gray-400 hover:text-gray-700 text-2xl leading-none focus:outline-none close-modal"
                aria-label="Close">&times;</button>
        </div>
        <div class="px-4 py-4 text-gray-700" id="responseMessage">
            <p>Servicios guardados exitosamente</p>
        </div>
        <div class="flex justify-end px-4 py-3 border-t">
            <button type="button"
                class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold px-4 py-2 rounded close-modal">Cerrar</button>
        </div>
    </div>
</div>
<div id="deletedServiceModal"
    class="fixed inset-0 z-50 items-center justify-center hidden pointer-events-none transition-opacity duration-300 opacity-0">
    <div class="absolute inset-0 bg-gray-500 opacity-50 transition-opacity"></div>
    <div
        class="bg-white rounded-lg shadow-lg w-full max-w-md mx-auto overflow-hidden transform transition-all duration-300 scale-95">
        <div class="flex items-center justify-between px-4 py-3 border-b">
            <h5 class="text-lg font-semibold" id="deletedServiceModalLabel">Servicio Eliminado</h5>
            <button type="button"
                class="text-gray-400 hover:text-gray-700 text-2xl leading-none focus:outline-none close-modal"
                aria-label="Close">&times;</button>
        </div>
        <div class="px-4 py-4 text-gray-700" id="responseMessage">
            <p>Servicio eliminado exitosamente</p>
        </div>
        <div class="flex justify-end px-4 py-3 border-t">
            <button type="button"
                class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold px-4 py-2 rounded close-modal">Cerrar</button>
        </div>
    </div>
</div>
<div id="deleteServiceModal"
    class="fixed inset-0 z-50 items-center justify-center hidden pointer-events-none transition-opacity duration-300 opacity-0">
    <div class="absolute inset-0 bg-gray-500 opacity-50 transition-opacity"></div>
    <div
        class="bg-white rounded-lg shadow-lg w-full max-w-md mx-auto overflow-hidden transform transition-all duration-300 scale-95">
        <div class="flex items-center justify-between px-4 py-3 border-b">
            <h5 class="text-lg font-semibold" id="deleteServiceLabel">Eliminar Servicio</h5>
            <button type="button"
                class="text-gray-400 hover:text-gray-700 text-2xl leading-none focus:outline-none close-modal"
                aria-label="Close">&times;</button>
        </div>
        <div class="px-4 py-4 text-gray-700">
            <p>El servicio será eliminado. ¿Deseas continuar?</p>
        </div>
        <div class="flex justify-end px-4 py-3 border-t">
            <button type="button"
                class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold px-4 py-2 rounded close-modal"
                id="cancelAutoOpen">Cerrar</button>
            <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded"
                id="confirmServiceDelete">Confirmar</button>
        </div>
    </div>
</div>

<div id="errorModal"
    class="fixed inset-0 z-50 items-center justify-center hidden pointer-events-none transition-opacity duration-300 opacity-0">
    <div class="absolute inset-0 bg-gray-500 opacity-50 transition-opacity"></div>
    <div
        class="bg-white rounded-lg shadow-lg w-full max-w-md mx-auto overflow-hidden transform transition-all duration-300 scale-95 relative z-10">
        <div class="flex items-center justify-between px-4 py-3 border-b bg-red-100">
            <h5 class="text-lg font-semibold text-red-700" id="errorModalLabel">Error</h5>
            <button type="button"
                class="text-red-400 hover:text-red-700 text-2xl leading-none focus:outline-none close-modal"
                aria-label="Close">&times;</button>
        </div>
        <div class="px-4 py-4 text-gray-700" id="errorMessage">
            <p>Ha ocurrido un error inesperado</p>
        </div>
        <div class="flex justify-end px-4 py-3 border-t">
            <button type="button"
                class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold px-4 py-2 rounded close-modal">Cerrar</button>
        </div>
    </div>
</div>