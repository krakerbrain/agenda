<!-- Modal check nuevo periodo automatico -->
<style>
#infoAppointment {
    z-index: 1056;
}
</style>
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

<!-- Modal de Eliminación -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">¿Por qué deseas eliminar esta reserva?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="reason-form">
                    <!-- Opciones predefinidas -->
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="reason" id="reason-no-show"
                            value="El cliente no asistió.">
                        <label class="form-check-label" for="reason-no-show">El cliente no asistió.</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="reason" id="reason-last-minute"
                            value="El cliente canceló a última hora.">
                        <label class="form-check-label" for="reason-last-minute">El cliente canceló a última
                            hora.</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="reason" id="reason-no-confirmation"
                            value="El cliente no confirmó la reserva.">
                        <label class="form-check-label" for="reason-no-confirmation">El cliente no confirmó la
                            reserva.</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="reason" id="reason-early-cancel"
                            value="El cliente canceló con anticipación.">
                        <label class="form-check-label" for="reason-early-cancel">El cliente canceló con
                            anticipación.</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="reason" id="reason-booking-error"
                            value="Error en la reserva.">
                        <label class="form-check-label" for="reason-booking-error">Error en la reserva.</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="reason" id="reason-business-cancel"
                            value="El negocio canceló la reserva.">
                        <label class="form-check-label" for="reason-business-cancel">El negocio canceló la
                            reserva.</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="reason" id="reason-other" value="Otro">
                        <label class="form-check-label" for="reason-other">Otro</label>
                    </div>

                    <!-- Campo para notas adicionales -->
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notas adicionales:</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="delete-button">
                    <span class="spinner-border spinner-border-sm d-none" aria-hidden="true"></span>
                    <span>Eliminar cita</span>
                </button>
                <button type="button" class="btn btn-warning" id="delete-and-incident-button" data-bs-dismiss="modal">
                    <span class="spinner-border spinner-border-sm d-none" aria-hidden="true"></span>
                    <span>Eliminar y generar incidencia</span>
                </button>
            </div>
        </div>
    </div>
</div>
<!-- Modal de Advertencia -->
<div class="modal fade" id="warningModal" tabindex="-1" aria-labelledby="warningModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="warningModalLabel">Advertencia</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Es obligatorio seleccionar una de las incidencias previamente mencionadas para generar una
                    incidencia.</p>
                <p>Si considera que no hubo problemas, haga clic en "Regresar" y luego en "Eliminar cita".</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-target="#deleteModal" data-bs-toggle="modal"
                    data-bs-dismiss="modal">Regresar</button>
            </div>
        </div>
    </div>
</div>