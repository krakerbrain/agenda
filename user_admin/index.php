<?php
require_once dirname(__DIR__) . '/configs/init.php';
require_once dirname(__DIR__) . '/configs/VersionManager.php';
require_once dirname(__DIR__) . '/access-token/seguridad/JWTAuth.php';
require_once dirname(__DIR__) . '/classes/ConfigUrl.php';
require_once dirname(__DIR__) . '/classes/Users.php';
require_once dirname(__DIR__) . '/classes/Notifications.php';

$title = "Configuraciones";

$baseUrl = ConfigUrl::get();
$auth = new JWTAuth();
$datosUsuario = $auth->validarTokenUsuario();
$role_id = $datosUsuario['role_id'];
// Obtener la instancia singleton
$versionManager = VersionManager::getInstance();

$userData = new Users();
$user_count = $userData->count_company_users($datosUsuario['company_id']);
if ($user_count > 1) {
    $users = $userData->get_all_users($datosUsuario['company_id']);
}

$notificationData = new Notifications(); // Asume que tienes esta clase
$unread_count = $notificationData->getUnreadCount($datosUsuario['user_id']);

include dirname(__DIR__) . '/partials/head.php';
?>
<script>
    const baseUrl = '<?php echo $baseUrl; ?>';
    const role_id = <?php echo $role_id; ?>;
    window.APP_VERSION = '<?= $versionManager->getVersion() ?>';
</script>
<link rel="stylesheet" href="<?php echo $baseUrl; ?>assets/css/dist/output.css">

<body class="text-sm" data-base-url="<?php echo htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8'); ?>">
    <!-- Navbar -->
    <header class="sticky top-0 z-50 bg-gray-100 shadow-sm">
        <nav class="container mx-auto px-2 py-3 flex items-center justify-between max-w-7xl">
            <a class="text-xl font-semibold titulo" href="#"></a>

            <div class="flex items-center space-x-4">
                <!-- Notification Dropdown -->
                <div class="relative">
                    <button class="relative p-1 text-gray-700 hover:text-gray-900 focus:outline-none"
                        id="notificationDropdown" aria-expanded="false">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <?php if ($unread_count > 0): ?>
                            <span
                                class="absolute -top-1 -right-1 h-5 w-5 rounded-full bg-red-500 flex items-center justify-center text-white text-xs">
                                <?php echo $unread_count; ?>
                            </span>
                        <?php endif; ?>
                    </button>

                    <!-- Dropdown menu -->
                    <div id="notificationMenu"
                        class="hidden absolute right-0 mt-2 w-72 bg-white rounded-md shadow-lg overflow-hidden z-50">
                        <div class="py-1">
                            <div class="px-4 py-2 text-sm font-medium text-gray-700 border-b">Notificaciones</div>
                            <div id="notification-list" class="max-h-96 overflow-y-auto">
                                <!-- Loading spinner -->
                                <div class="px-4 py-3 text-center">
                                    <div class="inline-block h-4 w-4 animate-spin rounded-full border-2 border-solid border-blue-500 border-r-transparent"
                                        role="status">
                                        <span class="sr-only">Cargando...</span>
                                    </div>
                                </div>
                            </div>
                            <div class="border-t">
                                <a href="#" id="view-all-notifications"
                                    class="block px-4 py-2 text-sm text-center text-gray-700 hover:bg-gray-100">Ver
                                    todas</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Offcanvas toggle button -->
                <button class="p-1 text-gray-700 hover:text-gray-900 focus:outline-none" id="offcanvasToggle"
                    aria-label="Toggle menu">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </nav>
    </header>

    <!-- Offcanvas Menu -->
    <div id="offcanvasMenu"
        class="fixed inset-y-0 left-0 z-50 w-64 bg-white transform -translate-x-full transition-transform duration-300 ease-in-out overflow-y-auto">
        <div class="flex items-center justify-between px-4 py-3 border-b">
            <h5 class="text-lg font-medium">Configuraciones</h5>
            <button id="offcanvasClose" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="p-4">
            <ul class="space-y-1">
                <?php if ($role_id != 1) { ?>
                    <li>
                        <a class="nav-element block px-3 py-2 text-blue-600 font-medium rounded hover:bg-gray-100" href="#"
                            id="datesList">Lista de citas</a>
                    </li>
                    <li>
                        <a class="nav-element block px-3 py-2 text-gray-700 rounded hover:bg-gray-100" href="#"
                            id="clientes">Clientes</a>
                    </li>
                    <?php if ($datosUsuario['role_id'] == 2) : ?>
                        <li>
                            <a class="nav-element block px-3 py-2 text-gray-700 rounded hover:bg-gray-100" href="#"
                                id="horarios">Horarios</a>
                        </li>
                        <li>
                            <a class="nav-element block px-3 py-2 text-gray-700 rounded hover:bg-gray-100" href="#"
                                id="servicios">Servicios</a>
                        </li>
                        <?php if ($user_count >= 2) : ?>
                            <li>
                                <a class="nav-element block px-3 py-2 text-gray-700 rounded hover:bg-gray-100" href="#"
                                    id="services_assign">Asignar Servicios</a>
                            </li>
                        <?php endif; ?>
                        <li>
                            <a class="nav-element block px-3 py-2 text-gray-700 rounded hover:bg-gray-100" href="#"
                                id="correos">Correos</a>
                        </li>
                        <li>
                            <a class="nav-element block px-3 py-2 text-gray-700 rounded hover:bg-gray-100" href="#"
                                id="datosEmpresa">Datos
                                Empresa</a>
                        </li>
                        <li>
                            <a class="nav-element block px-3 py-2 text-gray-700 rounded hover:bg-gray-100" href="#"
                                id="addUser">Agregar
                                Usuario</a>
                        </li>
                        <li>
                            <a class="nav-element block px-3 py-2 text-gray-700 rounded hover:bg-gray-100" href="#"
                                id="configuraciones">Otras configuraciones</a>
                        </li>
                        <li>
                            <a class="nav-element block px-3 py-2 text-gray-700 rounded hover:bg-gray-100" href="#"
                                id="integrations">Servicios Integrados</a>
                        </li>
                        <li>
                            <a class="nav-element block px-3 py-2 text-gray-700 rounded hover:bg-gray-100" href="#"
                                id="eventos_unicos">Eventos Únicos</a>
                        </li>
                        <li>
                            <a class="nav-element block px-3 py-2 text-gray-700 rounded hover:bg-gray-100" href="#"
                                id="bloqueoHoras">Bloqueo de horas</a>
                        </li>
                        <li>
                            <a class="nav-element block px-3 py-2 text-gray-700 rounded hover:bg-gray-100" href="#"
                                id="notificaciones">Notificaciones del sistema</a>
                        </li>
                    <?php endif; ?>
                <?php } else { ?>
                    <li>
                        <a class="nav-element block px-3 py-2 text-gray-700 rounded hover:bg-gray-100" href="#"
                            id="master_add_company">Agrega Empresa</a>
                    </li>
                    <li>
                        <a class="nav-element block px-3 py-2 text-gray-700 rounded hover:bg-gray-100" href="#"
                            id="master_company_list">Lista de Empresas</a>
                    </li>
                    <li>
                        <a class="nav-element block px-3 py-2 text-gray-700 rounded hover:bg-gray-100" href="#"
                            id="master_add_notification">Notificaciones</a>
                    </li>
                <?php } ?>
                <li>
                    <a class="nav-element block px-3 py-2 text-gray-700 rounded hover:bg-gray-100" href="#"
                        id="logout">Cerrar
                        sesión</a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Backdrop -->
    <div id="offcanvasBackdrop" class="fixed inset-0 z-40 bg-black opacity-50 hidden"></div>

    <div id="main-content" class="container max-w-7xl mx-auto px-4 py-6"></div>

    <!-- Usado en configuracion de eventos únicos para múltiples fechas -->
    <script src="<?php echo $baseUrl; ?>assets/vendors/js/flatpickr/flatpickr.min.js"></script>
    <script src="<?php echo $baseUrl; ?>assets/vendors/js/flatpickr/es.js"></script>
    <!-- JavaScript de Cropper.js -->
    <script src="<?php echo $baseUrl; ?>assets/vendors/js/jquery/cropperjs/cropper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>


    <script type="module"
        src="<?php echo $baseUrl; ?>assets/js/navbar.js?v=<?php echo $versionManager->getVersion() ?>"></script>
    <script type="module"
        src="<?php echo $baseUrl; ?>assets/js/navbar-notification/notification_badge.js?v=<?php echo $versionManager->getVersion() ?>">
    </script>
</body>

</html>