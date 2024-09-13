<?php
require_once dirname(__DIR__) . '/classes/DatabaseSessionManager.php';
require_once dirname(__DIR__) . '/classes/ConfigUrl.php';
require_once dirname(__DIR__) . '/access-token/seguridad/jwt.php';
$baseUrl = ConfigUrl::get();
$manager = new DatabaseSessionManager();
$title = "Configuraciones";
$datosUsuario = validarToken();
if (!$datosUsuario) {
    header("Location: " . $baseUrl . "login/index.php");
}

include dirname(__DIR__) . '/partials/head.php';
?>
<script>
const baseUrl = '<?php echo $baseUrl; ?>';
</script>
<style>
@media(max-width:1000px) {

    th {
        display: none;
    }

    td {
        display: block;
    }

    .data::before {
        content: attr(data-cell) ": ";
        font-weight: 700;
        text-transform: capitalize;
    }
}
</style>

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
    <!-- <div class="container mt-5 d-flex justify-content-between">
        
    </div> -->

    <!-- Offcanvas -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasMenu" aria-labelledby="offcanvasMenuLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasMenuLabel">Configuraciones</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <ul class="nav nav-underline flex-column">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="#" id="dateList">Lista de citas</a>
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
                    <a class="nav-link" href="#" id="logout">Cerrar sesión</a>
                </li>
            </ul>
        </div>
    </div>
    <div id="main-content" class="container mt-5"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <script type="module" src="<?php echo $baseUrl; ?>assets/js/navbar.js?v=" <?php echo time(); ?>></script>
</body>

</html>