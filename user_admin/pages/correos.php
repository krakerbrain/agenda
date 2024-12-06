<?php
require_once dirname(__DIR__, 2) . '/configs/init.php';
require_once dirname(__DIR__, 2) . '/access-token/seguridad/JWTAuth.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';

$baseUrl = ConfigUrl::get();
$auth = new JWTAuth();
$auth->validarTokenUsuario();
?>
<div class="container mt-5">

    <div class="d-flex align-items-baseline">
        <h2 class="mb-4">Notas para correos</h2>
        <a tabindex="0" role="button" data-bs-trigger="focus" class="btn" data-bs-toggle="popover"
            data-bs-title="Notas para correos y eventos"
            data-bs-content="Las notas se agregan al final de los correos para proporcionar información adicional al cliente. En correos de reserva, puedes incluir detalles importantes como requisitos previos o pagos necesarios. Para eventos como cursos o charlas, puedes añadir indicaciones específicas, como horarios, materiales requeridos o enlaces de acceso.">
            <i class="fa fa-circle-question text-primary" style="font-size: 1.5rem;"></i>
        </a>

    </div>
    <!-- Selector para elegir el tipo de correo -->
    <div class="mb-4">
        <label for="tipoCorreo" class="form-label">Selecciona el tipo de correo:</label>
        <select id="tipoCorreo" class="form-select">
            <option value="reserva" data-type="companies">Reserva de Cita</option>
            <option value="confirmacion" data-type="companies">Confirmación de Cita</option>
            <option value="reserva_evento" data-type="unique_events">Reserva de Evento Único</option>
            <option value="confirmacion_evento" data-type="unique_events">Confirmación de Evento Único</option>
        </select>
    </div>
    <div id="eventoSelectContainer" class="mb-3"></div>

    <!-- Formulario dinámico -->
    <form id="formNotas" class="mb-5">
        <div id="notasContainer">
            <!-- Aquí se cargarán las notas dinámicamente -->
        </div>
        <div class="mb-3">
            <button type="button" class="btn btn-secondary" id="agregarNota">Agregar Nota</button>
        </div>
        <div class="mb-3">
            <button type="submit" class="btn btn-primary">Guardar Notas</button>
        </div>
    </form>
</div>

</div>