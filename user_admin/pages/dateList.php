<?php
require_once dirname(__DIR__, 2) . '/classes/Database.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';
require_once dirname(__DIR__, 2) . '/access-token/seguridad/jwt.php';
$baseUrl = ConfigUrl::get();

$datosUsuario = validarToken();

if (!$datosUsuario) {
    header("Location: " . $baseUrl . "login/index.php");
}

// $company_id = $datosUsuario['company_id'];
?>


<div class="container mt-4">
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="">
                <tr>
                    <th scope="col">Servicio</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Teléfono</th>
                    <th scope="col">Correo</th>
                    <th scope="col">Fecha</th>
                    <th scope="col">Hora</th>
                    <th scope="col">Estado</th>
                    <th scope="col">Acción</th>
                </tr>
            </thead>
            <tbody id="appointmentsList">

            </tbody>
        </table>
    </div>
</div>