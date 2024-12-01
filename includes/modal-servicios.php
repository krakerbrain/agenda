<div class="modal fade" id="saveServices" tabindex="-1" aria-labelledby="saveServicesLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="saveServicesLabel">Servicios Guardados</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="responseMessage">
                <p>Servicios guardados exitosamente</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="deletedServiceModal" tabindex="-1" aria-labelledby="deletedServiceModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deletedServiceModalLabel">Servicio Eliminado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="responseMessage">
                <p>Servicio eliminado exitosamente</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal check nuevo periodo automatico -->
<div class="modal fade" id="deleteServiceModal" tabindex="-1" aria-labelledby="deleteServiceLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteServiceLabel">Eliminar Servicio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                El servicio será eliminado. ¿Deseas continuar?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                    id="cancelAutoOpen">Cerrar</button>
                <button type="button" class="btn btn-primary" id="confirmServiceDelete">Confirmar</button>
            </div>
        </div>
    </div>
</div>