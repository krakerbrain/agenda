<form id="appointmentForm" style="max-width: 600px; margin: 0 auto;">
    <input type="hidden" name="company_id" id="company_id" value="<?php echo htmlspecialchars($company['id']); ?>">
    <?php if ($customerData): ?>
        <!-- Botón para volver a user_admin/index.php -->
        <div class="text-end mb-3">
            <a href="<?php echo $baseUrl . 'user_admin/index.php'; ?>">Volver a
                Configuraciones</a>
        </div>
    <?php endif; ?>
    <!-- PASO 1 -->
    <div id="step1" class="step">
        <h4 class="text-center mb-4 pass-title">Paso 1: Escoge el Servicio</h4>
        <!-- crear select para prestador de servicio -->
        <?php if (count($userProviders) > 1) : ?>
            <div class="mb-3">
                <label for="provider" class="form-label">Prestador de servicio:</label>
                <select id="provider" name="provider" class="form-select" required>
                    <option value="" selected>Selecciona un prestador de servicio</option>
                    <?php foreach ($userProviders as $provider) : ?>
                        <option value="<?php echo htmlspecialchars($provider['id']); ?>">
                            <?php echo htmlspecialchars($provider['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php else :  ?>
            <input type="hidden" name="provider_owner" id="provider_owner" value="true">
            <input type="hidden" name="provider" id="provider"
                value="<?php echo htmlspecialchars($userProviders[0]['id']); ?>">
        <?php endif; ?>
        <div class="mb-3">
            <label for="service" class="form-label">Servicio:</label>
            <select id="service" name="service" class="form-select" required>
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
    <!-- Paso 3: Confirmación de datos -->
    <div id="step3" class="step d-none">
        <h4 class="text-center mb-4 pass-title">Paso 3: Completa tus datos</h4>
        <input type="hidden" name="company_id" id="company_id" value="<?php echo htmlspecialchars($company['id']); ?>">
        <input type="hidden" name="authenticated" id="authenticated"
            value="<?php echo $authenticated ? 'true' : 'false'; ?>">
        <!-- Checkbox para editar (solo visible si $customerData existe) -->
        <?php if ($customerData): ?>
            <div class="mb-3 form-check">
                <input type="checkbox" id="editCustomer" class="form-check-input">
                <label for="editCustomer" class="form-check-label text-dark">Editar datos</label>
                <a tabindex="0" role="button" data-bs-trigger="focus" data-bs-toggle="popover" data-bs-title="Editar Datos"
                    data-bs-content="Si marcas esta casilla podrás editar los datos de tu cliente. Esta opción estará disponible solo una vez">
                    <i class="fa fa-circle-question text-secondary" style="font-size: 1.2rem;"></i>
                </a>
            </div>
        <?php endif; ?>
        <div class="mb-3">
            <label for="name" class="form-label">Nombre:</label>
            <input type="text" id="name" name="name" class="form-control customer-field"
                value="<?php echo $customerData ? htmlspecialchars($customerData['name']) : ''; ?>"
                <?php echo $customerData ? 'disabled' : ''; ?> required>
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Teléfono:</label>
            <input type="tel" id="phone" name="phone" class="form-control customer-field"
                value="<?php echo $customerData ? htmlspecialchars($customerData['phone']) : ''; ?>"
                <?php echo $customerData ? 'disabled' : ''; ?> required>
        </div>
        <div class="mb-3">
            <label for="mail" class="form-label">Correo:</label>
            <input type="email" id="mail" name="mail" class="form-control customer-field"
                value="<?php echo $customerData ? htmlspecialchars($customerData['mail']) : ''; ?>"
                <?php echo $customerData ? 'disabled' : ''; ?> required>
        </div>
        <!-- Botones de navegación -->
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
                <button type="button" class="btn btn-anterior" data-bs-dismiss="modal"
                    id="cancelReservation">Cancelar</button>
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
                <h5 class="modal-title" id="responseModalLabel"></h5>
            </div>
            <div class="modal-body" id="responseModalBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-siguiente" id="acceptButton"
                    data-bs-dismiss="modal">Aceptar</button>
            </div>
        </div>
    </div>
</div>