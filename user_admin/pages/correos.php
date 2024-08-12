<div class="container mt-5">
    <h2 class="mb-4">Configuración de Correos</h2>

    <!-- Formulario para el correo de reserva -->
    <form id="reservaForm" class="mb-5">
        <h4 class="mb-3">Correo de Reserva</h4>
        <div class="mb-3">
            <label for="reservaSubject" class="form-label">Asunto:</label>
            <input type="text" class="form-control" id="reservaSubject" name="subject"
                placeholder="Asunto del correo de reserva">
        </div>
        <div class="mb-3">
            <label for="reservaBody" class="form-label">Cuerpo:</label>
            <textarea class="form-control" id="reservaBody" name="body" rows="6"
                placeholder="Cuerpo del correo de reserva"></textarea>
        </div>
        <div class="mb-3">
            <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
    </form>

    <!-- Formulario para el correo de confirmación -->
    <form id="confirmacionForm">
        <h4 class="mb-3">Correo de Confirmación</h4>
        <div class="mb-3">
            <label for="confirmacionSubject" class="form-label">Asunto:</label>
            <input type="text" class="form-control" id="confirmacionSubject" name="subject"
                placeholder="Asunto del correo de confirmación">
        </div>
        <div class="mb-3">
            <label for="confirmacionBody" class="form-label">Cuerpo:</label>
            <textarea class="form-control" id="confirmacionBody" name="body" rows="6"
                placeholder="Cuerpo del correo de confirmación"></textarea>
        </div>
        <div class="mb-3">
            <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
    </form>
</div>