<?php
require_once __DIR__ . '/classes/ConfigUrl.php';
$baseUrl = ConfigUrl::get();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda Road</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
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
        backdrop-filter: blur(6px);
        /* Desenfoque de fondo */
        -webkit-backdrop-filter: blur(10px);
        /* Desenfoque de fondo para Safari */
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        /* Sombra sutil */


    }

    #home h1 {
        font-size: 5rem;
    }

    @media (max-width: 768px) {
        #home h1 {
            font-size: 1.5rem;
            /* Ajuste para pantallas medianas */
        }

        .free-background-container {
            background-size: 100%;
        }
    }

    .conocenos-btn {
        background-color: #EA98A2;
        border: none;
        font-weight: 500;
    }

    .conocenos-btn:hover {
        background-color: #d9a7ad;
        color: #1a1728;
    }

    .container.flip-container {
        perspective: 1000px;
    }

    .flip-container-background {
        background-image: url('<?php echo $baseUrl; ?>assets/img/que_es_cut.png');
        background-size: 70%;
        background-position: center;
        background-repeat: no-repeat;
        max-height: 600px;
    }

    .flip-container-background::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(26, 23, 40, 0.95);
        /* Asegúrate de que esté por encima de la imagen */
        pointer-events: none;
        /* Evita que el pseudo-elemento interfiera con los clics */
    }

    .flip-card {
        width: 100%;
        height: 400px;
        /* Ajusta según tu diseño; puedes usar 100% para ocupar todo el espacio */
        position: relative;
        transform-style: preserve-3d;
        /* Permite el giro en 3D */
        transition: transform 0.4s;
        /* Duración del efecto de giro más rápido */
    }

    .flip-card-front,
    .flip-card-back {
        backface-visibility: hidden;
        /* Oculta la parte trasera cuando se gira */
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        /* Para centrar contenido */
        justify-content: center;
        align-items: center;
        text-align: center;
        /* Centrado del texto */
    }

    .flip-card-front {
        z-index: 2;
    }

    .flip-card-back {
        /* background-color: #f5f5f5; */
        transform: rotateY(180deg);
        /* Coloca la parte trasera al revés */
        z-index: 1;

    }

    .responsiveMenu a {
        border: none;
        font-size: 0.95rem;
        font-weight: 300;
    }
    </style>
</head>

<body>
    <!-- Navbar de Bootstrap -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Agenda Road</a>
            <!-- Botones de navegación -->
            <div class="responsiveMenu d-flex">
                <!-- <button class="navbar-toggler togler-prev d-none p-1" type="button" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-label="Toggle navigation">
                    <a id="navPrev" class="nav-link scroll-nav" href="#home"><i class="fas fa-arrow-left-long"></i>
                        Home</a>
                </button> -->
                <a id="navPrev" class="nav-link scroll-nav togler-prev d-none scroll-nav p-1" href="#about-us"
                    data-bs-target="#navbarNav" aria-controls="navbarNav" aria-label="Toggle navigation">
                    Home <i class="fas fa-arrow-left-long"></i>
                </a>
                <a id="navNext" class="nav-link scroll-nav togler-next p-1" href="#about-us" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-label="Toggle navigation">
                    ¿Qué es? <i class="fas fa-arrow-right-long"></i>
                </a>
                <!-- <button class="navbar-toggler togler-next p-1" type="button" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-label="Toggle navigation">
                    <a id="navNext" class="nav-link scroll-nav" href="#about-us">¿Qué es? <i
                            class="fas fa-arrow-right-long"></i></a>
                </button> -->
            </div>
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
                        <h1 class="mb-md-4 mt-4 mt-md-0">AGENDA ROAD</h1>
                        <p class="text-info py-4">La herramienta de gestión de citas que necesitas para organizar tu
                            tiempo de manera
                            eficiente.</p>
                        <div class="d-grid gap-2 d-md-flex mt-md-4">
                            <a href="#about-us" class="btn conocenos-btn flex-grow-1 scroll-nav">Conócenos</a>
                            <a href="#tu-accion" class="btn btn-primary flex-grow-1">Pruebalo Gratis</a>
                        </div>
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
            <div class="container text-center flip-container">
                <!-- Contenedor giratorio -->
                <div class="flip-card">
                    <!-- Contenido Frente -->
                    <div class="flip-card-front">
                        <div class="row align-items-center">
                            <div class="col-md-6 text-center">
                                <img src="<?php echo $baseUrl; ?>assets/img/que_es_cut.png" alt="Agenda Road"
                                    class="img-fluid" />
                            </div>
                            <div class="col-md-6 text-center text-md-start">
                                <h2>¿Qué es Agenda Road?</h2>
                                <p class="text-info py-4">
                                    Agenda Road es una herramienta de gestión de citas fácil de usar, diseñada para
                                    ayudarte a organizar
                                    tu tiempo de manera eficiente.
                                </p>
                                <button id="show-more-info" class="btn btn-primary">Más Información</button>
                            </div>
                        </div>
                    </div>
                    <!-- Contenido Detrás -->
                    <div class="flip-card-back d-block pt-md-3">
                        <?php include __DIR__ . '/descripcion-agenda-road.php'; ?>
                        <!-- Botones abajo -->
                        <div class="d-flex flex-column flex-md-row justify-content-center align-items-center pt-md-4">
                            <a href="#" class="btn btn-primary me-md-2 mb-3 mb-md-0">¡Prueba Agenda Road Hoy Mismo!</a>
                            <button id="show-less-info"
                                class="btn btn-outline-light ms-md-2 ms-4 align-self-md-center align-self-start">Volver</button>
                        </div>
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

    // Definir el flag para controlar el scroll
    let scrollAllowed = true;

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




    document.getElementById("show-more-info").addEventListener("click", function() {

        // Giro para mostrar la información adicional
        gsap.to(".flip-card", {
            duration: 0.4,
            rotationY: 180
        });
        // Activar el scroll horizontal
        scrollAllowed = false;
        document.querySelector(".flip-container").classList.add("flip-container-background");
    });

    document.getElementById("show-less-info").addEventListener("click", function() {
        document.querySelector(".flip-container").classList.remove("flip-container-background");
        // Giro para volver a la vista original
        gsap.to(".flip-card", {
            duration: 0.4,
            rotationY: 0
        });
        // Activar el scroll horizontal
        scrollAllowed = true;
    });

    // Controlar el scroll horizontal basado en el flag
    window.addEventListener('wheel', function(e) {
        if (!scrollAllowed) {
            e.preventDefault();
        }
    }, {
        passive: false
    });

    window.addEventListener('touchmove', function(e) {
        if (!scrollAllowed) {
            e.preventDefault();
        }
    }, {
        passive: false
    });

    // Definir las secciones y los textos
    const sectionToggler = [{
            id: 'home',
            text: 'Home'
        },
        {
            id: 'about-us',
            text: '¿Qué es?'
        },
        {
            id: 'how-it-works',
            text: 'Cómo funciona'
        },
        {
            id: 'pricing',
            text: 'Precios'
        }
    ];

    let currentIndex = 0;

    const prevButton = document.querySelector('.togler-prev');
    const nextButton = document.querySelector('.togler-next');
    const navPrev = document.getElementById('navPrev');
    const navNext = document.getElementById('navNext');

    function updateNavButtons() {
        // Actualizar botón anterior
        if (currentIndex > 0) {
            prevButton.classList.remove('d-none');
            navPrev.href = `#${sectionToggler[currentIndex - 1].id}`;
            navPrev.innerHTML = `<i class="fas fa-arrow-left-long"></i> ${sectionToggler[currentIndex - 1].text}`;
        } else {
            prevButton.classList.add('d-none');
        }

        // Actualizar botón siguiente
        if (currentIndex < sectionToggler.length - 1) {
            nextButton.classList.remove('d-none');
            navNext.href = `#${sectionToggler[currentIndex + 1].id}`;
            navNext.innerHTML = `${sectionToggler[currentIndex + 1].text} <i class="fas fa-arrow-right-long"></i>`;
        } else {
            nextButton.classList.add('d-none');
        }
    }

    // Agregar eventos de clic para los botones
    navPrev.addEventListener('click', () => {
        if (currentIndex > 0) {
            currentIndex--;
            updateNavButtons();
            localStorage.setItem('currentIndex', currentIndex);
        }
    });

    navNext.addEventListener('click', () => {
        if (currentIndex < sectionToggler.length - 1) {
            currentIndex++;
            updateNavButtons();
            localStorage.setItem('currentIndex', currentIndex);
        }
    });

    // Obtener el valor de currentIndex del localStorage al cargar la página
    window.addEventListener('load', () => {
        const storedIndex = localStorage.getItem('currentIndex');
        if (storedIndex !== null) {
            currentIndex = parseInt(storedIndex);
            updateNavButtons();
        }
    });

    // Inicializar los botones
    updateNavButtons();
    </script>
</body>

</html>