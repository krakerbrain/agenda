<?php
require_once dirname(__DIR__, 2) . '/configs/init.php';
require_once dirname(__DIR__, 2) . '/access-token/seguridad/JWTAuth.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';
require_once dirname(__DIR__, 2) . '/classes/Users.php';

$baseUrl = ConfigUrl::get();
$auth = new JWTAuth();
$datosUsuario = $auth->validarTokenUsuario();

$userData = new Users();
$user_count = $userData->count_company_users($datosUsuario['company_id']);
if ($user_count > 1) {
    $users = $userData->get_all_users($datosUsuario['company_id']);
}

?>

<div class="container my-5">
    <!-- creamos un select con un label que estara al lado del selct que diga selecicone al ussuario este select solo estara disponible para el admin -->
    <?php if ($user_count > 1 && $datosUsuario['role_id'] == 2) : ?>
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="userSelect" class="form-label">Selecciona el usuario</label>
                <select id="userSelect" class="form-select">
                    <?php foreach ($users as $user) : ?>
                        <option value="<?= $user['id'] ?>"><?= $user['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    <?php endif; ?>
    <form id="workScheduleForm" method="POST" class="border p-4 rounded">
        <div id="unsavedChangesAlert" class="alert alert-warning d-none" role="alert">
            <strong>Recuerda guardar los cambios antes de salir.</strong>
            Si sales sin guardar, perderás los cambios realizados.
            <button type="submit" class="btn btn-primary btn-sm">Guardar Configuración</button>
        </div>

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