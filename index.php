<?php
require_once __DIR__ . '/classes/ConfigUrl.php';
$baseUrl = ConfigUrl::get();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendarium</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

</head>
<style>
    body {
        font-family: 'Montserrat', sans-serif;
    }
</style>
<script>
    const baseUrl = '<?php echo $baseUrl; ?>';
</script>

<body>
    <!--Crear navbar de boostrap con nombre de la empresa, 4 links y boton de comenazr y login  -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="#">Agendarium</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav justify-content-around w-50 ms-md-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#">Producto</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">¿Cómo Funciona?</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Precios</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto d-flex flex-row gap-2">
                    <li class="nav-item">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#modalInscripcion">Comenzar</button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                            data-bs-target="#modalLogin">Iniciar sesión</button>
                    </li>
                </ul>

                </ul>
            </div>
        </div>
    </nav>
    <div class="sections-container">
        <div class="section hero d-flex justify-content-center align-items-center" id="home">
            <div class="container p-4">
                <div class="row align-items-center">
                    <div class="col-md-6 order-2 order-md-1 text-center text-md-start">
                        <h1 class="mb-md-4 mt-4 mt-md-0">AGENDARIUM</h1>
                        <p class="text-info py-4">La herramienta de gestión de citas que necesitas para organizar tu
                            tiempo de manera
                            eficiente.</p>

                        <a href="#inscriptionModal" class="btn btn-primary flex-grow-1" data-bs-toggle="modal">Pruebalo
                            gratis por 15 dias</a>

                    </div>
                    <div class="col-md-6 order-1 order-md-2 text-center">
                        <img src="<?php echo $baseUrl; ?>assets/img/landing_home.png" alt="Agendarium"
                            class="img-fluid" />
                    </div>
                </div>
            </div>
        </div>
        <?php include __DIR__ . '/includes/descripcion-agendarium.php'; ?>
    </div>
    <!-- Sección ¿Cómo funciona? -->
    <div id="how-it-works" class="section d-flex justify-content-center align-items-center how-it-works">
        <div class="container text-center">
            <div class="row align-items-center">
                <div class="col-md-6 text-center">
                    <img src="<?php echo $baseUrl; ?>assets/img/como_funciona.png" alt="Agendarium"
                        class="img-fluid w-75" />
                </div>
                <div class="col-md-6 text-center text-md-start">
                    <h2>¿Cómo Funciona?</h2>
                    <p class="text-info py-4">
                        Agendarium te permite crear, modificar y eliminar citas con facilidad. Recibe
                        notificaciones y
                        recordatorios directamente en tu dispositivo.
                    </p>
                    <a href="#tu-accion" class="btn btn-primary">Más Información</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección Precios -->
    <div id="pricing" class="section d-flex justify-content-center align-items-center pricing">
        <div class="container text-center free-background-container align-content-center"
            style="background-image: url('<?php echo $baseUrl; ?>assets/img/gratis.png');">
            <div class="free-text-container">
                <h2>Nuestros Planes</h2>
                <p class="text-info py-4">
                    Prueba todas las funcionalidades gratis por 15 dias. Si te gusta puedes continuar por solo
                    $9.000
                    al mes
                </p>
                <a href="#inscriptionModal" data-bs-toggle="modal" class="btn btn-primary">Solicita Gratis</a>
            </div>
        </div>
    </div>

    <!-- Incluir el modal -->
    <?php include 'includes/modal-inscripcion.php'; ?>
    <?php include 'includes/modal-inscripcion-exitosa.php'; ?>
    <!-- Scripts de GSAP y Bootstrap -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollToPlugin.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo $baseUrl; ?>assets/js/app.js?v=<?php echo time(); ?>"></script>
</body>

</html>