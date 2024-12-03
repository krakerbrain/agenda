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
            data-bs-title="Notas para correos"
            data-bs-content="Los correos de reserva y confirmación tienen una estructura fija. Aquí puedes agregar notas que se van a agregar al correo para darle más información al cliente. Por ejemplo: 'Recuerda que debes depositar un adelanto de $10.000 para poder confirmar la reserva.'">
            <i class="fa fa-circle-question text-primary" style="font-size: 1.5rem;"></i>
        </a>
    </div>

    <!-- Formulario para el correo de reserva -->
    <form id="reservaForm" class="mb-5">
        <h4 class="mb-3">Correo de Reserva</h4>
        <div id="reservaNotas">

        </div>
        <div class="mb-3">
            <button type="button" class="btn btn-secondary" id="agregarNota">Agregar Nota</button>
        </div>
        <div class="mb-3">
            <button type="submit" class="btn btn-primary">Guardar Notas</button>
        </div>
    </form>

    <!-- Formulario para el correo de confirmación -->
    <form id="confirmacionForm">
        <h4 class="mb-3">Correo de Confirmación</h4>
        <div id="confirmacionNotas">

        </div>
        <div class="mb-3">
            <button type="button" class="btn btn-secondary" id="agregarNotaConfirmacion">Agregar Nota</button>
        </div>
        <div class="mb-3">
            <button type="submit" class="btn btn-primary">Guardar Notas</button>
        </div>
    </form>
</div>