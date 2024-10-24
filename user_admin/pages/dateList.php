<?php
require_once dirname(__DIR__, 2) . '/configs/init.php';
require_once dirname(__DIR__, 2) . '/access-token/seguridad/JWTAuth.php';
require_once dirname(__DIR__, 2) . '/classes/Database.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';

$baseUrl = ConfigUrl::get();
$auth = new JWTAuth();
$auth->validarTokenUsuario();
?>

<div class="container mt-4">

    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="unconfirmed-tab" data-bs-toggle="tab" data-bs-target="#unconfirmed"
                type="button" role="tab" aria-controls="unconfirmed" aria-selected="true">Por Confirmar</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="confirmed-tab" data-bs-toggle="tab" data-bs-target="#confirmed" type="button"
                role="tab" aria-controls="confirmed" aria-selected="false">Confirmadas</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="past-tab" data-bs-toggle="tab" data-bs-target="#past" type="button" role="tab"
                aria-controls="past" aria-selected="false">Pasadas</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab"
                aria-controls="all" aria-selected="false">Todas</button>
        </li>
    </ul>
    <!-- Contenido de las pestañas -->
    <div class="tab-content" id="appointmentsTabContent">
        <!-- Agregar aquí las otras pestañas según sea necesario -->
        <div class="tab-pane fade show active" id="unconfirmed" role="tabpanel" aria-labelledby="unconfirmed-tab">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
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
                    <tbody id="unconfirmedAppointmentsList">
                        <!-- Las citas no confirmadas se llenarán aquí mediante JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
        <div class="tab-pane fade" id="confirmed" role="tabpanel" aria-labelledby="confirmed-tab">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
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
                    <tbody id="confirmedAppointmentsList">
                        <!-- Las citas confirmadas se llenarán aquí mediante JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
        <div class="tab-pane fade" id="past" role="tabpanel" aria-labelledby="past-tab">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
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
                    <tbody id="pastAppointmentsList">
                        <!-- Las citas pasadas se llenarán aquí mediante JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
        <div class="tab-pane fade" id="all" role="tabpanel" aria-labelledby="all-tab">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
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
                        <!-- Las citas se llenarán aquí mediante JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>