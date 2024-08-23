<?php
require_once dirname(__DIR__) . '/classes/DatabaseSessionManager.php';
require_once dirname(__DIR__) . '/classes/ConfigUrl.php';
$baseUrl = ConfigUrl::get();
include dirname(__DIR__) . '/partials/head.php';
?>

<body>
    <nav class="navbar navbar-expand-lg bg-dark navbar-dark">
        <div class="container-fluid col-md-4">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup"
                aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-between" id="navbarNavAltMarkup">
                <div class="navbar-nav">
                    <a class="nav-link active" aria-current="page"
                        href="<?php echo $baseUrl . 'master_admin/admin.php' ?>">Home</a>
                    <a class="nav-link disabled" href="#" disabled>Empresas</a>
                </div>
                <div class="navbar-nav">
                    <a class="nav-link" href="<?php echo $baseUrl . 'login/logout.php' ?>">Cerrar Sesión</a>
                </div>
            </div>
        </div>
    </nav>
</body>

</html>