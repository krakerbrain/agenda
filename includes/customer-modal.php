<!-- modal generico de informacion -->
<div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="infoModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="infoModalMessage">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- customer_modal.php -->
<div class="modal fade" id="customerDetailModal" tabindex="-1" aria-labelledby="customerDetailModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="customerDetailModalLabel">Detalles del Cliente</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <!-- Sección de Información Básica -->
                <div class="mb-4">
                    <h6>Información Básica</h6>
                    <p><strong>Nombre:</strong> <span id="customerName"></span></p>
                    <p><strong>Teléfono:</strong> <a id="customerPhone" href="#" target="_blank"></a></p>
                    <p><strong>Correo:</strong> <span id="customerEmail"></span></p>
                    <p><strong>Estado:</strong> <span id="customerStatus"></span></p>
                    <p><strong>Incidencias:</strong> <span id="customerIncidents"></span></p>
                </div>

                <!-- Sección de Últimos Servicios -->
                <div class="mb-4">
                    <h6>Últimos Servicios</h6>
                    <ul class="list-group" id="customerLastServices"></ul>
                </div>

                <!-- Sección de Incidencias -->
                <div class="mb-4">
                    <h6>Incidencias</h6>
                    <ul class="list-group" id="customerIncidentsList"></ul>
                </div>

                <!-- Sección de Notas -->
                <div class="mb-4"></div>
                <h6>Notas</h6>
                <ul class="list-group" id="customerNotes"></ul>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
    </div>
</div>
</div>

<!-- modal de edicion -->
<div class="modal fade" id="editCustomerModal" tabindex="-1" aria-labelledby="editCustomerModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCustomerModalLabel">Editar Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editCustomerForm">
                    <input type="hidden" id="editCustomerId" name="id">
                    <div class="mb-3">
                        <label for="editCustomerName" class="form-label">Nombre:</label>
                        <input type="text" class="form-control" id="editCustomerName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="editCustomerPhone" class="form-label">Teléfono:</label>
                        <input type="text" class="form-control" id="editCustomerPhone" name="phone">
                    </div>
                    <div class="mb-3">
                        <label for="editCustomerMail" class="form-label">Correo:</label>
                        <input type="email" class="form-control" id="editCustomerMail" name="mail">
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="editCustomerBlocked" name="blocked">
                        <label class="form-check-label" for="editCustomerBlocked">Bloqueado</label>
                    </div>
                    <div class="mb-3">
                        <label for="editCustomerNotes" class="form-label">Notas:</label>
                        <textarea class="form-control" id="editCustomerNotes" name="notes" rows="4"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary" form="editCustomerForm">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para agregar la nota de bloqueo -->
<div class="modal fade" id="modalBloquearCliente" tabindex="-1" aria-labelledby="modalBloquearClienteLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalBloquearClienteLabel">Bloquear Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formBloquearCliente">
                    <div class="mb-3">
                        <label for="notaBloqueo" class="form-label">Razón del bloqueo</label>
                        <textarea class="form-control" id="notaBloqueo" name="notaBloqueo" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Confirmar Bloqueo</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Modal eliminar cliente -->
<div class="modal fade" id="modalEliminarCliente" tabindex="-1" aria-labelledby="modalEliminarClienteLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEliminarClienteLabel">¿Estás seguro de que quieres eliminar este
                    cliente?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Se eliminarán todos los datos del cliente, incluyendo:</p>
                <ul>
                    <li>Información personal (nombre, teléfono, etc.).</li>
                    <li>Citas futuras (se liberarán los horarios).</li>
                    <li>Citas pasadas (no se conservará el historial).</li>
                </ul>
                <p><strong>El cliente no será bloqueado.</strong> Podrá registrarse nuevamente en el futuro si lo desea.
                    Si quieres evitar que reserve citas, te recomendamos usar la opción <strong>Bloquear</strong>.</p>
                <p class="text-danger"><strong>Esta acción no se puede deshacer.</strong> Asegúrate de que es lo que
                    quieres hacer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btnEliminarCliente">Eliminar</button>
            </div>
        </div>
    </div>
</div>