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
<!-- <link rel="stylesheet" href="<?php echo $baseUrl; ?>assets/css/table2.css?v=<?php echo time(); ?>"> -->
<div class="max-w-4xl mx-auto my-8 px-2 w-full">
    <?php if ($user_count > 1 && $datosUsuario['role_id'] == 2) : ?>
    <div class="mb-6">
        <label for="userSelect" class="block text-sm font-medium text-gray-700 mb-2">Selecciona el usuario</label>
        <select id="userSelect"
            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-500 focus:ring focus:ring-cyan-200 focus:ring-opacity-50">
            <?php foreach ($users as $user) : ?>
            <option value="<?= $user['id'] ?>" <?= $datosUsuario['role_id'] == $user['role_id'] ? 'selected' : '' ?>>
                <?= $user['name'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php else : ?>
    <input type="hidden" id="userSelect" value="<?= $datosUsuario['user_id'] ?>">
    <?php endif; ?>
    <form id="workScheduleForm" method="POST" class="border border-gray-200 p-6 rounded-lg bg-white shadow">
        <div id="unsavedChangesAlert"
            class="hidden mb-4 p-4 rounded bg-yellow-100 text-yellow-800 items-center justify-between gap-4">
            <div>
                <strong>Recuerda guardar los cambios antes de salir.</strong>
                <span class="block text-sm">Si sales sin guardar, perderás los cambios realizados.</span>
            </div>
            <button type="submit"
                class="bg-cyan-600 hover:bg-cyan-700 text-white font-semibold px-4 py-2 rounded text-sm">Guardar
                Configuración</button>
        </div>
        <div class="overflow-x-auto rounded-lg">
            <table class="hidden md:table min-w-full divide-y divide-gray-200 bg-white">
                <thead class="table-header-group">
                    <tr class="bg-cyan-50 text-cyan-800 text-sm font-semibold">
                        <th class="px-3 py-2">Estado</th>
                        <th class="px-3 py-2">Día</th>
                        <th class="px-3 py-2">Jornada</th>
                        <th class="px-3 py-2">Acción</th>
                    </tr>
                </thead>
                <tbody id="scheduleTableBodyDesktop" class="schedule-body divide-y divide-gray-100">
                    <!-- Desktop rows -->
                </tbody>
            </table>
            <div id="scheduleTableBodyMobile" class="schedule-body md:hidden space-y-2 mt-2">
                <!-- Mobile cards -->
            </div>
        </div>
        <button type="submit"
            class="mt-6 bg-cyan-600 hover:bg-cyan-700 text-white font-semibold px-6 py-2 rounded shadow">Guardar
            Configuración</button>
    </form>
    <?php include dirname(__DIR__, 2) . '/includes/modal-horarios-guardados.php'; ?>
</div>