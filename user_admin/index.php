<?php
require_once dirname(__DIR__) . '/classes/DatabaseSessionManager.php';
require_once dirname(__DIR__) . '/classes/ConfigUrl.php';
$baseUrl = ConfigUrl::get();
$manager = new DatabaseSessionManager();
$manager->startSession();
$title = "Configuraciones";


$sesion = isset($_SESSION['company_id']);


if (!$sesion) {
    header("Location: " . $baseUrl . "login/index.php");
}


include dirname(__DIR__) . '/partials/head.php';
?>
<script>
    const baseUrl = '<?php echo $baseUrl; ?>';
</script>

<body>
    <div class="container mt-4">
        <ul class="nav nav-pills nav-fill">
            <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="#" id="admin">Lista de citas</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" id="horarios">Horarios</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" id="servicios">Servicios</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" id="configuraciones">Otras configuraciones</a>
            </li>
        </ul>
    </div>
    <div id="main-content" class="container mt-5"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <script type="module" src="<?php echo $baseUrl; ?>assets/js/navbar.js"></script>
</body>

</html>