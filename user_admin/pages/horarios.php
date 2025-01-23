<?php
require_once dirname(__DIR__, 2) . '/configs/init.php';
require_once dirname(__DIR__, 2) . '/access-token/seguridad/JWTAuth.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';

$baseUrl = ConfigUrl::get();
$auth = new JWTAuth();
$auth->validarTokenUsuario();
?>

<div class="container my-5">
    <form id="workScheduleForm" method="POST" class="border p-4 rounded">
        <input type="hidden" name="company_id" id="company_id" value="1"> <!-- Replace with dynamic company ID -->
        <table class="table table-borderless table-striped table-sm">
            <thead>
                <tr class="head-table">
                    <th>Día</th>
                    <th>Estado</th>
                    <th>Inicio de la jornada</th>
                    <th>Fin de la jornada</th>
                    <th>Acción</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="scheduleTableBody">

            </tbody>
        </table>
        <button type="submit" class="btn btn-primary mt-3">Guardar Configuración</button>
    </form>
    <?php include dirname(__DIR__, 2) . '/includes/modal-horarios-guardados.php';
    ?>
</div>