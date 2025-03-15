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
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>