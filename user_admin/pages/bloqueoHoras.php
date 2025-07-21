<?php
require_once dirname(__DIR__, 2) . '/access-token/seguridad/JWTAuth.php';
require_once dirname(__DIR__, 2) . '/classes/CompanyManager.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';
require_once dirname(__DIR__, 2) . '/classes/Users.php';

$baseUrl = ConfigUrl::get();
$company = new CompanyManager();
$auth = new JWTAuth();
$datosUsuario = $auth->validarTokenUsuario();

$userData = new Users();
$user_count = $userData->count_company_users($datosUsuario['company_id']);
if ($user_count > 1) {
    $users = $userData->get_all_users($datosUsuario['company_id']);
}
?>

<!-- Fechas bloqueadas -->
<div class="max-w-4xl mx-auto mt-8">
    <form id="block-date-form" class="mb-8">
        <div class="flex justify-end">
            <a tabindex="0" role="button" data-bs-trigger="focus" class="help" data-bs-toggle="popover"
                data-bs-title="Fechas Bloqueadas"
                data-bs-content="Puedes bloquear fechas específicas o un rango de horas para que no se puedan hacer reservas en esos días">
                <i class="fa fa-circle-question text-blue-600 text-2xl"></i>
            </a>
        </div>

        <?php if ($user_count > 1 && $datosUsuario['role_id'] == 2) : ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="userSelect" class="block text-sm font-medium text-gray-700 mb-1">Selecciona el
                        usuario</label>
                    <select name="user_id" id="userSelect"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <?php foreach ($users as $user) : ?>
                            <option value="<?= $user['id'] ?>"
                                <?= $datosUsuario['role_id'] == $user['role_id'] ? 'selected' : '' ?>>
                                <?= $user['name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        <?php else : ?>
            <input type="hidden" name="user_id" id="userSelect" value="<?= $datosUsuario['user_id'] ?>">
        <?php endif; ?>

        <!-- Selección de fecha y horas -->
        <div class="mb-4">
            <label for="block-date" class="block text-sm font-medium text-gray-700 mb-1">Seleccionar Fecha</label>
            <input type="date" id="block-date" name="block_date"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                required>
        </div>

        <div class="flex items-center mb-4">
            <input type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                id="all-day" name="all_day">
            <label for="all-day" class="ml-2 block text-sm text-gray-700">Todo el día</label>
        </div>

        <div id="hour-range" class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label for="start-hour" class="block text-sm font-medium text-gray-700 mb-1">Inicio</label>
                <input type="time" id="start-hour" name="start_hour"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label for="end-hour" class="block text-sm font-medium text-gray-700 mb-1">Término</label>
                <input type="time" id="end-hour" name="end_hour"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>

        <button type="submit"
            class="w-full md:w-auto px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mb-8">
            Guardar Hora Bloqueada
        </button>
    </form>

    <div class="mt-8">
        <!-- Listado de fechas bloqueadas -->
        <div class="mt-8">
            <h5 class="text-lg font-medium text-gray-900 mb-4">Listado de Fechas y Horas Bloqueadas</h5>

            <div id="blocked-dates-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Mensaje cuando no hay fechas -->
                <div class="col-span-full text-center py-4 text-gray-500">
                    No hay fechas bloqueadas.
                </div>
            </div>
        </div>
    </div>
</div>
<?php include dirname(__DIR__, 2) . '/includes/modal-info.php'; ?>