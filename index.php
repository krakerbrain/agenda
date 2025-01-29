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
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>assets/css/index.css?v=<?php echo time(); ?>">
</head>
<script>
    const baseUrl = '<?php echo $baseUrl; ?>';
</script>

<body>
    <nav class="navbar fixed-top">
        <div class="container">
            <!-- Marca -->
            <a class="navbar-brand" href="#">Agendarium</a>
            <!-- Botón de inicio de sesión -->
            <div class="d-flex login">
                <a href="<?php echo $baseUrl; ?>login/index.php" class="btn text-info d-flex">
                    Iniciar Sesión
                    <i class="material-icons ms-1">login</i>
                </a>
            </div>
        </div>
    </nav>
    <!-- Navbar de Bootstrap -->
    <nav class="navbar fixed-bottom">
        <div class="container">
            <a href="#home" class="nav-item scroll-nav active">
                <i class="material-icons">home</i>
                Inicio
            </a>
            <a href="#about-us" class="nav-item scroll-nav">
                <i class="material-icons">info</i>
                ¿Qué es?
            </a>
            <a href="#how-it-works" class="nav-item scroll-nav">
                <i class="material-icons">help_outline</i>
                ¿Cómo funciona?
            </a>
            <a href="#pricing" class="nav-item scroll-nav">
                <i class="material-icons">attach_money</i>
                Precios
            </a>
        </div>
    </nav>
    <div class="sections-container d-flex">
        <div class="section hero d-flex justify-content-center align-items-center" id="home">
            <div class="container p-4">
                <div class="row align-items-center">
                    <div class="col-md-6 order-2 order-md-1 text-center text-md-start">
                        <h1 class="mb-md-4 mt-4 mt-md-0">AGENDARIUM</h1>
                        <p class="text-info py-4">La herramienta de gestión de citas que necesitas para organizar tu
                            tiempo de manera
                            eficiente.</p>
                        <div class="d-grid gap-2 d-md-flex mt-md-4">
                            <a href="#about-us" class="btn conocenos-btn flex-grow-1 scroll-nav">Conócenos</a>
                            <a href="#inscriptionModal" class="btn btn-primary flex-grow-1"
                                data-bs-toggle="modal">Pruebalo gratis por 15 dias</a>
                        </div>
                    </div>
                    <div class="col-md-6 order-1 order-md-2 text-center">
                        <img src="<?php echo $baseUrl; ?>assets/img/landing_home.png" alt="Agendarium"
                            class="img-fluid" />
                    </div>
                </div>
            </div>
        </div>
        <!-- Sección ¿Qué es? -->
        <div id="about-us" class="section d-flex justify-content-center align-items-center about-us">
            <div class="container text-center flip-container">
                <!-- Contenedor giratorio -->
                <div class="flip-card">
                    <!-- Contenido Frente -->
                    <div class="flip-card-front d-flex justify-content-center align-items-center">
                        <div class="row align-items-center">
                            <div class="col-md-6 text-center">
                                <img src="<?php echo $baseUrl; ?>assets/img/que_es_cut.png" alt="Agendarium"
                                    class="img-fluid" />
                            </div>
                            <div class="col-md-6 text-center text-md-start">
                                <h2>¿Qué es Agendarium?</h2>
                                <p class="text-info py-4">
                                    Agendarium es una herramienta de gestión de citas fácil de usar, diseñada para
                                    ayudarte a organizar
                                    tu tiempo de manera eficiente.
                                </p>
                                <button id="show-more-info" class="btn btn-primary">Más Información</button>
                            </div>
                        </div>
                    </div>
                    <!-- Contenido Detrás -->
                    <div class="flip-card-back d-block pt-md-3">
                        <?php include __DIR__ . '/includes/descripcion-agendarium.php'; ?>
                        <!-- Botones abajo -->
                        <div class="pt-md-4">
                            <a id="show-less-info" class="btn btn-outline-light p-1 material-icons">reply</a>
                            <a href="#inscriptionModal" class="btn btn-primary flex-grow-1"
                                data-bs-toggle="modal">¡Prueba Agendarium Hoy Mismo!</a>
                        </div>
                    </div>
                </div>
            </div>
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
    </div>
    <!-- Agrega más secciones aquí -->
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