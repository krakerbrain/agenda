<?php
require_once dirname(__DIR__) . '/configs/init.php';
require_once dirname(__DIR__) . '/configs/VersionManager.php';
require_once dirname(__DIR__) . '/access-token/seguridad/JWTAuth.php';
require_once dirname(__DIR__) . '/classes/ConfigUrl.php';
$title = "Configuraciones";

$baseUrl = ConfigUrl::get();
$auth = new JWTAuth();
$userData = $auth->validarTokenUsuario();
$role_id = $userData['role_id'];
// Obtener la instancia singleton
$versionManager = VersionManager::getInstance();

include dirname(__DIR__) . '/partials/head.php';
?>
<script>
    const baseUrl = '<?php echo $baseUrl; ?>';
    const role_id = <?php echo $role_id; ?>;
    window.APP_VERSION = '<?= $versionManager->getVersion() ?>';
</script>

<body>
    <header class="nav navbar sticky-top bg-dark-subtle">
        <nav class="container-xxl">
            <a class="navbar-brand titulo" href="#"></a>
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
                        <a class="nav-link active" aria-current="page" href="#" id="dateList">Lista de citas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" id="clientes">Clientes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" id="horarios">Horarios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" id="servicios">Servicios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" id="correos">Correos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" id="datos_empresa">Datos Empresa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" id="add_user">Agregar Usuario</a>
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
                        <a class="nav-link" href="#" id="block_hour">Bloqueo de horas</a>
                    </li>
                <?php } else { ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#" id="master_add_company">Agrega Empresa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" id="master_company_list">Lista de Empresas</a>
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
</body>

</html>