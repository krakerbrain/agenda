<?php
require_once __DIR__ . '/classes/ConfigUrl.php';
$baseUrl = ConfigUrl::get();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scroll Horizontal con GSAP</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            height: 100vh;
            margin: 0;
            font-family: "Roboto", sans-serif;
            background: #1a1728;
            color: #f5f5f5;

        }

        .container {
            width: 90%;
            max-width: 1200px;
        }

        .sections-container {
            display: flex;
            width: 100vw;
        }

        .section {
            min-width: 100vw;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;

        }

        .free-background-container {
            background-size: 70%;
            background-position: center;
            background-repeat: no-repeat;
            height: 100%;
            max-height: 600px;
        }

        .free-text-container {
            background: rgba(255, 255, 255, 0.0);
            /* Color de fondo blanco con opacidad */
            border-radius: 15px;
            /* Bordes redondeados */
            padding: 20px;
            /* Espaciado interno */
            backdrop-filter: blur(10px);
            /* Desenfoque de fondo */
            -webkit-backdrop-filter: blur(10px);
            /* Desenfoque de fondo para Safari */
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            /* Sombra sutil */


        }
    </style>
</head>

<body>
    <!-- Navbar de Bootstrap -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Agenda Road</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link scroll-nav" href="#home">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link scroll-nav" href="#about-us">¿Qué es?</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link scroll-nav" href="#how-it-works">¿Cómo funciona?</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link scroll-nav" href="#pricing">Precios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-info" href="<?php echo $baseUrl; ?>login/index.php">Inicia Sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="sections-container">
        <div class="section hero" id="home">
            <div class="container p-4">
                <div class="row align-items-center">
                    <div class="col-md-6 order-2 order-md-1 text-center text-md-start">
                        <h1>Agenda Road</h1>
                        <p class="text-info py-4">La herramienta de gestión de citas que necesitas para organizar tu
                            tiempo de manera
                            eficiente.</p>
                        <a href="#tu-accion" class="btn btn-primary">Comienza Ahora</a>
                    </div>
                    <div class="col-md-6 order-1 order-md-2 text-center">
                        <img src="<?php echo $baseUrl; ?>assets/img/landing_home.png" alt="Agenda Road"
                            class="img-fluid" />
                    </div>
                </div>
            </div>
        </div>
        <!-- Sección ¿Qué es? -->
        <div id="about-us" class="section about-us">
            <div class="container text-center">
                <div class="row align-items-center">
                    <div class="col-md-6 text-center">
                        <img src="<?php echo $baseUrl; ?>assets/img/que_es_cut.png" alt="Agenda Road"
                            class="img-fluid" />
                    </div>
                    <div class="col-md-6 text-center text-md-start">
                        <h2>¿Qué es Agenda Road?</h2>
                        <p class="text-info py-4">
                            Agenda Road es una herramienta de gestión de citas fácil de usar, diseñada para ayudarte a
                            organizar
                            tu tiempo de manera eficiente.
                        </p>
                        <a href="#tu-accion" class="btn btn-primary">Más Información</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Sección ¿Cómo funciona? -->
        <div id="how-it-works" class="section how-it-works">
            <div class="container text-center">

                <div class="row align-items-center">
                    <div class="col-md-6 text-center">
                        <img src="<?php echo $baseUrl; ?>assets/img/como_funciona.png" alt="Agenda Road"
                            class="img-fluid w-75" />
                    </div>
                    <div class="col-md-6 text-center text-md-start">
                        <h2>¿Cómo Funciona?</h2>
                        <p class="text-info py-4">
                            Agenda Road te permite crear, modificar y eliminar citas con facilidad. Recibe
                            notificaciones y
                            recordatorios directamente en tu dispositivo.
                        </p>
                        <a href="#tu-accion" class="btn btn-primary">Más Información</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Sección Precios -->
        <div id="pricing" class="section pricing">
            <div class="container text-center free-background-container align-content-center"
                style="background-image: url('<?php echo $baseUrl; ?>assets/img/gratis.png');">
                <div class="free-text-container">
                    <h2>Nuestros Planes</h2>
                    <p class="text-info py-4">
                        Estamos en periodo de pruebas, por lo que NO HAY PLANES. Solicita tu prueba gratuita de 1 mes.
                    </p>
                    <a href=" #tu-accion" class="btn btn-primary">Solicita Gratis</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Agrega más secciones aquí -->
    </div>

    <!-- Scripts de GSAP y Bootstrap -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollToPlugin.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Scroll horizontal con GSAP
        const sections = document.querySelectorAll('.section');
        gsap.registerPlugin(ScrollTrigger, ScrollToPlugin);

        let panelsSection = document.querySelector(".sections-container"),
            panels = document.querySelectorAll(".section"),
            tween;

        document.querySelectorAll(".scroll-nav").forEach((anchor) => {
            anchor.addEventListener("click", function(e) {
                e.preventDefault();
                let targetElem = document.querySelector(e.target.getAttribute("href"));
                // Calcula el total del scroll disponible y el movimiento total
                let totalScroll = tween.scrollTrigger.end - tween.scrollTrigger.start,
                    totalMovement = (panels.length - 1) * targetElem.offsetWidth;

                // Calcula la nueva posición del scroll basada en la posición del elemento objetivo
                let y = Math.round(tween.scrollTrigger.start + (targetElem.offsetLeft / totalMovement) *
                    totalScroll);

                // Realiza el desplazamiento
                gsap.to(window, { // Se usa `y` como en el código de GSAP
                    scrollTo: {
                        y: y,
                        autoKill: false,
                    },
                    duration: 1,
                });
            });
        });

        tween = gsap.to('.sections-container', {
            xPercent: -100 * (sections.length - 1),
            ease: 'none',
            scrollTrigger: {
                trigger: '.sections-container',
                pin: true,
                scrub: 1,
                snap: {
                    snapTo: 1 / (sections.length - 1),
                    inertia: false,
                    duration: {
                        min: 0.1,
                        max: 0.1,
                    }
                },
                end: () => "+=" + document.querySelector('.section').offsetWidth * sections.length
            }
        });
    </script>
</body>

</html>