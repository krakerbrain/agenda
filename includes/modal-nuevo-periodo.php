<!-- Modal para Confirmar Nuevo Periodo -->
<div class="modal fade" id="newPeriodModal" tabindex="-1" aria-labelledby="newPeriodModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newPeriodModalLabel">Confirmar Nuevo Periodo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
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
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" id="confirmNewPeriod" class="btn btn-warning">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal check nuevo periodo automatico -->
<div class="modal fade" id="autoOpenModal" tabindex="-1" aria-labelledby="autoOpenModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="autoOpenModalLabel">Confirmación de apertura automática</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Al marcar esta opción, se abrirá automáticamente un nuevo periodo al finalizar el actual. Esto será
                permanente mientras esta casilla esté seleccionada. ¿Deseas continuar?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                    id="cancelAutoOpen">Cerrar</button>
                <button type="button" class="btn btn-primary" id="confirmAutoOpen">Confirmar</button>
            </div>
        </div>
    </div>
</div>