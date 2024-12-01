<!-- Modal check nuevo periodo automatico -->
<div class="modal fade" id="googleAuthenticateModal" tabindex="-1" aria-labelledby="googleAuthenticateModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="googleAuthenticateModalLabel">Autenticación necesario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Autenticación inválida. Por favor, vuelve a iniciar sesión
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                    id="cancelAutoOpen">Cerrar</button>
                <button type="button" class="btn btn-primary" id="confirmAuthenticate">Aceptar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="infoAppointment" tabindex="-1" aria-labelledby="infoAppointmentLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="infoAppointmentLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="infoAppointmentMessage">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>