<!-- Modal de información -->
<div id="infoModal"
    class="fixed inset-0 z-50 items-center justify-center hidden pointer-events-none transition-opacity duration-300 opacity-0">
    <div class="absolute inset-0 bg-black opacity-50"></div>
    <div
        class="bg-white rounded-lg shadow-lg w-full max-w-md mx-auto overflow-hidden transform transition-all duration-300 scale-95 translate-y-4 opacity-0">
        <div class="flex items-center justify-between px-4 py-3 border-b">
            <h5 class="text-lg font-semibold" id="infoModalLabel"></h5>
            <button type="button"
                class="text-gray-400 hover:text-gray-700 text-2xl leading-none focus:outline-none close-modal"
                aria-label="Close">&times;</button>
        </div>
        <div class="px-4 py-4 text-gray-700" id="infoModalMessage"></div>
        <div class="flex justify-end px-4 py-3 border-t">
            <button type="button"
                class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold px-4 py-2 rounded close-modal">Cerrar</button>
        </div>
    </div>
</div>

<!-- Modal de detalles del cliente -->
<div id="customerDetailModal"
    class="fixed inset-0 z-50 items-center justify-center hidden pointer-events-none transition-opacity duration-300 opacity-0">
    <div class="absolute inset-0 bg-black opacity-50"></div>
    <div
        class="bg-white rounded-lg shadow-lg w-full max-w-lg mx-auto overflow-hidden transform transition-all duration-300 scale-95 translate-y-4 opacity-0">
        <div class="flex items-center justify-between px-4 py-3 border-b bg-cyan-600 text-white">
            <h5 class="text-lg font-semibold" id="customerDetailModalLabel">Detalles del Cliente</h5>
            <button type="button"
                class="text-white hover:text-gray-200 text-2xl leading-none focus:outline-none close-modal"
                aria-label="Close">&times;</button>
        </div>
        <div class="px-4 py-4">
            <div class="mb-4">
                <h6 class="font-semibold mb-1">Información Básica</h6>
                <p><strong>Nombre:</strong> <span id="customerName"></span></p>
                <p><strong>Teléfono:</strong> <a id="customerPhone" href="#" target="_blank"></a></p>
                <p><strong>Correo:</strong> <span id="customerEmail"></span></p>
                <p><strong>Estado:</strong> <span id="customerStatus"></span></p>
                <p><strong>Incidencias:</strong> <span id="customerIncidents"></span></p>
            </div>
            <div class="mb-4">
                <h6 class="font-semibold mb-1">Últimos Servicios</h6>
                <ul class="list-disc pl-5 text-sm" id="customerLastServices"></ul>
            </div>
            <div class="mb-4">
                <h6 class="font-semibold mb-1">Incidencias</h6>
                <ul class="list-disc pl-5 text-sm" id="customerIncidentsList"></ul>
            </div>
            <div class="mb-4">
                <h6 class="font-semibold mb-1">Notas</h6>
                <div class="text-sm" id="customerNotes"></div>
            </div>
        </div>
        <div class="flex justify-end px-4 py-3 border-t">
            <button type="button"
                class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold px-4 py-2 rounded close-modal">Cerrar</button>
        </div>
    </div>
</div>

<!-- Modal de edición de cliente -->
<div id="editCustomerModal"
    class="fixed inset-0 z-50 items-center justify-center hidden pointer-events-none transition-opacity duration-300 opacity-0">
    <div class="absolute inset-0 bg-black opacity-50"></div>
    <div
        class="bg-white rounded-lg shadow-lg w-full max-w-lg mx-auto overflow-hidden transform transition-all duration-300 scale-95 translate-y-4 opacity-0">
        <div class="flex items-center justify-between px-4 py-3 border-b">
            <h5 class="text-lg font-semibold" id="editCustomerModalLabel">Editar Cliente</h5>
            <button type="button"
                class="text-gray-400 hover:text-gray-700 text-2xl leading-none focus:outline-none close-modal"
                aria-label="Close">&times;</button>
        </div>
        <div class="px-4 py-4">
            <form id="editCustomerForm">
                <input type="hidden" id="editCustomerId" name="id">
                <div class="mb-3">
                    <label for="editCustomerName" class="block text-sm font-medium mb-1">Nombre:</label>
                    <input type="text" class="form-input w-full border rounded px-3 py-2" id="editCustomerName"
                        name="name" required>
                </div>
                <div class="mb-3">
                    <label for="editCustomerPhone" class="block text-sm font-medium mb-1">Teléfono:</label>
                    <input type="text" class="form-input w-full border rounded px-3 py-2" id="editCustomerPhone"
                        name="phone">
                </div>
                <div class="mb-3">
                    <label for="editCustomerMail" class="block text-sm font-medium mb-1">Correo:</label>
                    <input type="email" class="form-input w-full border rounded px-3 py-2" id="editCustomerMail"
                        name="mail">
                </div>
                <div class="mb-3 flex items-center">
                    <input type="checkbox" class="form-checkbox mr-2" id="editCustomerBlocked" name="blocked">
                    <label class="text-sm" for="editCustomerBlocked">Bloqueado</label>
                </div>
                <div class="mb-3">
                    <label for="editCustomerNotes" class="block text-sm font-medium mb-1">Notas:</label>
                    <textarea class="form-input w-full border rounded px-3 py-2" id="editCustomerNotes" name="notes"
                        rows="4"></textarea>
                </div>
                <div class="flex justify-end gap-2 mt-4">
                    <button type="button"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold px-4 py-2 rounded close-modal">Cancelar</button>
                    <button type="submit"
                        class="bg-cyan-600 hover:bg-cyan-700 text-white font-semibold px-4 py-2 rounded">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para agregar la nota de bloqueo -->
<div id="modalBloquearCliente"
    class="fixed inset-0 z-50 items-center justify-center hidden pointer-events-none transition-opacity duration-300 opacity-0">
    <div class="absolute inset-0 bg-black opacity-50"></div>
    <div
        class="bg-white rounded-lg shadow-lg w-full max-w-md mx-auto overflow-hidden transform transition-all duration-300 scale-95 translate-y-4 opacity-0">
        <div class="flex items-center justify-between px-4 py-3 border-b">
            <h5 class="text-lg font-semibold" id="modalBloquearClienteLabel">Bloquear Cliente</h5>
            <button type="button"
                class="text-gray-400 hover:text-gray-700 text-2xl leading-none focus:outline-none close-modal"
                aria-label="Close">&times;</button>
        </div>
        <div class="px-4 py-4">
            <form id="formBloquearCliente">
                <div class="mb-3">
                    <label for="notaBloqueo" class="block text-sm font-medium mb-1">Razón del bloqueo</label>
                    <textarea class="form-input w-full border rounded px-3 py-2" id="notaBloqueo" name="notaBloqueo"
                        required></textarea>
                </div>
                <div class="flex justify-end">
                    <button type="submit"
                        class="bg-cyan-600 hover:bg-cyan-700 text-white font-semibold px-4 py-2 rounded">Confirmar
                        Bloqueo</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal eliminar cliente -->
<div id="modalEliminarCliente"
    class="fixed inset-0 z-50 items-center justify-center hidden pointer-events-none transition-opacity duration-300 opacity-0">
    <div class="absolute inset-0 bg-black opacity-50"></div>
    <div
        class="bg-white rounded-lg shadow-lg w-full max-w-md mx-auto overflow-hidden transform transition-all duration-300 scale-95 translate-y-4 opacity-0">
        <div class="flex items-center justify-between px-4 py-3 border-b">
            <h5 class="text-lg font-semibold" id="modalEliminarClienteLabel">¿Estás seguro de que quieres eliminar este
                cliente?</h5>
            <button type="button"
                class="text-gray-400 hover:text-gray-700 text-2xl leading-none focus:outline-none close-modal"
                aria-label="Close">&times;</button>
        </div>
        <div class="px-4 py-4">
            <p>Se eliminarán todos los datos del cliente, incluyendo:</p>
            <ul class="list-disc pl-5 text-sm mb-2">
                <li>Información personal (nombre, teléfono, etc.).</li>
                <li>Citas futuras (se liberarán los horarios).</li>
                <li>Citas pasadas (no se conservará el historial).</li>
            </ul>
            <p><strong>El cliente no será bloqueado.</strong> Podrá registrarse nuevamente en el futuro si lo desea. Si
                quieres evitar que reserve citas, te recomendamos usar la opción <strong>Bloquear</strong>.</p>
            <p class="text-red-600 font-semibold mt-2">Esta acción no se puede deshacer. Asegúrate de que es lo que
                quieres hacer.</p>
        </div>
        <div class="flex justify-end gap-2 px-4 py-3 border-t">
            <button type="button"
                class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold px-4 py-2 rounded close-modal">Cancelar</button>
            <button type="button" class="bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded"
                id="btnEliminarCliente">Eliminar</button>
        </div>
    </div>
</div>

<!-- Modal eliminar incidencias -->
<div id="deleteIncidentsModal"
    class="fixed inset-0 z-50 items-center justify-center hidden pointer-events-none transition-opacity duration-300 opacity-0">
    <div class="absolute inset-0 bg-black opacity-50"></div>
    <div
        class="bg-white rounded-lg shadow-lg w-full max-w-md mx-auto overflow-hidden transform transition-all duration-300 scale-95 translate-y-4 opacity-0">
        <div class="flex items-center justify-between px-4 py-3 border-b">
            <h5 class="text-lg font-semibold" id="deleteIncidentsModalLabel">Eliminar Incidencias</h5>
            <button type="button"
                class="text-gray-400 hover:text-gray-700 text-2xl leading-none focus:outline-none close-modal"
                aria-label="Close">&times;</button>
        </div>
        <div class="px-4 py-4">
            <p>Seleccione las incidencias que desea eliminar:</p>
            <div id="incidents-list" class="mb-3"></div>
        </div>
        <div class="flex justify-end gap-2 px-4 py-3 border-t">
            <button type="button"
                class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold px-4 py-2 rounded close-modal">Cancelar</button>
            <button type="button" id="confirm-delete"
                class="bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded" disabled>Eliminar
                seleccionadas</button>
        </div>
    </div>
</div>