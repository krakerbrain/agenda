<?php
// require_once dirname(__DIR__, 2) . '/configs/init.php';
// require_once dirname(__DIR__, 2) . '/access-token/seguridad/JWTAuth.php';
// require_once dirname(__DIR__, 2) . '/classes/Database.php';
// require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';

// $baseUrl = ConfigUrl::get();
// $auth = new JWTAuth();
// $auth->validarTokenUsuario();
?>

<div class="container mt-4">
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#unconfirmed" role="tab">Por
                Confirmar</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#confirmed" role="tab">Confirmadas</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#past" role="tab">Pasadas</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#all" role="tab">Todas</button>
        </li>
    </ul>

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Servicio</th>
                    <th>Nombre</th>
                    <th>Teléfono</th>
                    <th>Correo</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Estado</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody id="tableContent">
                <!-- Se llenará con JavaScript -->
            </tbody>
        </table>
    </div>
</div>
<?php include dirname(__DIR__, 2) . '/includes/modal-appointments.php';
?>