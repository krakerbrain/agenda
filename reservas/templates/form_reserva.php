<form id="appointmentForm" style="max-width: 600px; margin: 0 auto;">
    <!-- PASO 1 -->
    <div id="step1" class="step">
        <h4 class="text-center mb-4 pass-title">Paso 1: Escoge el Servicio</h4>
        <div class="mb-3">
            <label for="service" class="form-label">Servicio:</label>
            <select id="service" name="service" class="form-select" required>
                <option value="" selected>Selecciona un servicio</option>
                <?php foreach ($services as $service) : ?>
                    <option value="<?php echo htmlspecialchars($service['id']); ?>"
                        data-observation="<?php echo htmlspecialchars($service['observations']); ?>"
                        data-duration="<?php echo htmlspecialchars($service['duration']); ?>">
                        <?php echo htmlspecialchars($service['name']); ?></option>
                <?php endforeach; ?>
            </select>
            <input type="hidden" name="service_duration" id="service_duration" value>
        </div>
        <div id="serviceObservation" class="mb-3 d-none">
            <pre id="serviceTextObservation"
                class="form-control-plaintext bg-danger-subtle text-danger-emphasis p-2"></pre>
        </div>
        <div id="categoryContainer" class="mb-3 d-none">
            <label for="category" class="form-label">Categoría:</label>
            <select id="category" name="category" class="form-select" required>
                <option value="" selected>Selecciona una categoría</option>
            </select>
        </div>
        <div id="categoryObservation" class="mb-3 d-none">
            <pre id="categoryTextObservation"
                class="form-control-plaintext bg-danger-subtle text-danger-emphasis p-2"></pre>
        </div>

        <button type="button" class="btn btn-secondary btn-siguiente" onclick="showStep(2)">Siguiente</button>
    </div>
    <!-- PASO 2 -->
    <div id="step2" class="step d-none">
        <h4 class="text-center mb-4 pass-title">Paso 2: Escoge Fecha y Hora</h4>
        <div class="mb-3">
            <label for="date" class="form-label">Selecciona la fecha:</label>
            <input type="date" id="date" name="date" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="time" class="form-label">Selecciona el horario:</label>
            <input type="hidden" name="schedule_mode" id="schedule_mode"
                value="<?php echo htmlspecialchars($company['schedule_mode']); ?>">

            <!-- Contenedor para los botones de hora -->
            <div id="time" class="time-buttons">
                <!-- Los botones se generarán dinámicamente aquí -->
            </div>

            <!-- Campo oculto para almacenar la hora seleccionada -->
            <input type="hidden" id="selected_time" name="time">
        </div>
        <button type="button" class="btn btn-anterior" onclick="showStep(1)">Anterior</button>
        <button type="button" class="btn btn-siguiente" onclick="showStep(3)">Siguiente</button>
    </div>
    <!-- PASO 3 -->
    <div id="step3" class="step d-none">
        <h4 class="text-center mb-4 pass-title">Paso 3: Completa tus datos</h4>
        <input type="hidden" name="company_id" id="company_id" value="<?php echo htmlspecialchars($company['id']); ?>">
        <div class="mb-3">
            <label for="name" class="form-label">Nombre:</label>
            <input type="text" id="name" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Teléfono:</label>
            <input type="tel" id="phone" name="phone" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="mail" class="form-label">Correo:</label>
            <input type="email" id="mail" name="mail" class="form-control" required>
        </div>
        <button type="button" class="btn btn-secondary btn-anterior" onclick="showStep(2)">Anterior</button>
        <button id="reservarBtn" class="btn btn-secondary btn-siguiente" type="submit">
            <span class="spinner-border spinner-border-sm d-none" aria-hidden="true"></span>
            <span class="button-text">Reservar</span>
        </button>
    </div>
</form>


<!-- Modal de Confirmación -->
<div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel"
    data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmationModalLabel">Confirmar Reserva</h5>
            </div>
            <div class="modal-body" id="confirmationModalBody">
                <!-- Aquí se mostrarán los datos de la reserva -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-anterior" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-siguiente" id="confirmReservation">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Respuesta -->
<div class="modal fade" id="responseModal" tabindex="-1" aria-labelledby="responseModalLabel" data-bs-backdrop="static"
    data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="responseModalLabel">Reserva exitosa</h5>
            </div>
            <div class="modal-body" id="responseModalBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-siguiente" id="acceptButton"
                    data-bs-dismiss="modal">Aceptar</button>
            </div>
        </div>
    </div>
</div>