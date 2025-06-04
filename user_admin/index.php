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

<body data-base-url="<?php echo htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8'); ?>">
    <header class="nav navbar sticky-top bg-dark-subtle">
        <nav class="container-xxl">
            <a class="navbar-brand titulo" href="#"></a>
            <div class="d-flex align-items-center">
                <!-- Botón de notificaciones -->
                <div class="dropdown me-3">
                    <button class="btn btn-link text-dark p-0" type="button" id="notificationDropdown"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-envelope fs-6 position-relative"></i>
                        <?php if ($unread_count > 0): ?>
                            <span
                                class="position-absolute start-100 translate-middle badge rounded-pill bg-danger local-badge-style">
                                <?php echo $unread_count; ?>
                                <span class="visually-hidden">notificaciones no leídas</span>
                            </span>
                        <?php endif; ?>
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown"
                        style="min-width: 300px; max-height: 400px; overflow-y: auto;">
                        <li>
                            <h6 class="dropdown-header">Notificaciones</h6>
                        </li>
                        <div id="notification-list">
                            <!-- Las notificaciones se cargarán aquí dinámicamente -->
                            <li class="px-3 py-2 text-center">
                                <div class="spinner-border spinner-border-sm" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                            </li>
                        </div>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item text-center small" href="#" id="view-all-notifications">Ver
                                todas</a></li>
                    </ul>
                </div>
                <!-- Botón para abrir el offcanvas -->
                <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasMenu"
                    aria-controls="offcanvasMenu" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
        </nav>
    </header>
    <!-- Offcanvas -->
    <div class="offcanvas offcanvas-start overflow-auto" tabindex="-1" id="offcanvasMenu"
        aria-labelledby="offcanvasMenuLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasMenuLabel">Configuraciones</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <ul class="nav nav-underline flex-column">
                <?php if ($role_id != 1) { ?>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#" id="datesList">Lista de citas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" id="clientes">Clientes</a>
                    </li>
                    <?php if ($datosUsuario['role_id'] == 2) : ?>
                        <li class="nav-item">
                            <a class="nav-link" href="#" id="horarios">Horarios</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" id="servicios">Servicios</a>
                        </li>
                        <?php if ($user_count >= 2) : ?>
                            <li class="nav-item">
                                <a class="nav-link" href="#" id="services_assign">Asignar Servicios</a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link" href="#" id="correos">Correos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" id="datosEmpresa">Datos Empresa</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" id="addUser">Agregar Usuario</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" id="configuraciones">Otras configuraciones</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" id="integrations">Servicios Integrados</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" id="eventos_unicos">Eventos Únicos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" id="bloqueoHoras">Bloqueo de horas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" id="notificaciones">Notificaciones del sistema</a>
                        </li>
                    <?php endif; ?>
                <?php } else { ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#" id="master_add_company">Agrega Empresa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" id="master_company_list">Lista de Empresas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" id="master_add_notification">Notificaciones</a>
                    </li>
                <?php } ?>
                <li class="nav-item">
                    <a class="nav-link" href="#" id="logout">Cerrar sesión</a>
                </li>
            </ul>
        </div>
    </div>
    <div id="main-content" class="container mt-5"></div>
    <!-- Usado en configuracion de eventos únicos para múltiples fechas -->
    <script src="<?php echo $baseUrl; ?>assets/vendors/js/flatpickr/flatpickr.min.js"></script>
    <script src="<?php echo $baseUrl; ?>assets/vendors/js/flatpickr/es.js"></script>
    <script src="<?php echo $baseUrl; ?>assets/vendors/js/bootstrap/bootstrap.bundle.min.js"></script>
    <!-- JavaScript de Cropper.js -->
    <script src="<?php echo $baseUrl; ?>assets/vendors/js/jquery/cropperjs/cropper.min.js"></script>

    <script type="module"
        src="<?php echo $baseUrl; ?>assets/js/navbar.js?v=<?php echo $versionManager->getVersion() ?>"></script>
    <script type="module"
        src="<?php echo $baseUrl; ?>assets/js/navbar-notification/notification_badge.js?v=<?php echo $versionManager->getVersion() ?>">
    </script>
</body>

</html>