<?php
require_once __DIR__ . '/classes/ConfigUrl.php';
$baseUrl = ConfigUrl::get();

include_once __DIR__ . '/landing/partials/head.php';
?>

<body class="bg-custom-light font-sans text-gray-800">
    <?php
    include_once __DIR__ . '/landing/partials/navbar.php';
    ?>

    <!-- Sección Hero con Logo Destacado -->
    <section id="home" class="min-h-screen flex items-center bg-gradient-to-b from-white to-[#249373]/5">
        <div class="container mx-auto px-4 py-12">
            <!-- Logo Superior Centrado (Mobile) -->
            <div class="lg:hidden flex justify-center mb-8">
                <div class="flex items-center">
                    <img src="<?php echo $baseUrl; ?>assets/img/landing/logo_agendarium.png" alt="Logo Agendarium"
                        class="h-24 w-auto">
                    <span class="text-4xl font-bold text-[#1B637F] ml-3">AGENDARIUM</span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                <!-- Contenido Textual -->
                <div class="order-2 lg:order-1 text-center lg:text-left">
                    <!-- Logo + Nombre (Desktop) -->
                    <div class="hidden lg:flex items-center mb-8">
                        <img src="<?php echo $baseUrl; ?>assets/img/landing/logo_agendarium.png" alt="Logo Agendarium"
                            class="h-28 w-auto mr-4">
                        <span class="text-5xl font-bold text-[#1B637F]">AGENDARIUM</span>
                    </div>

                    <!-- Título -->
                    <h1 class="text-4xl sm:text-5xl xl:text-6xl font-bold mb-6 leading-tight text-[#1B637F]">
                        Controla tu agenda <br>
                        <span class="text-[#249373]">como un profesional</span>
                    </h1>

                    <!-- Subtítulo -->
                    <p class="text-xl md:text-2xl text-[#2B819F] mb-10 max-w-lg mx-auto lg:mx-0">
                        La solución todo-en-uno para gestionar citas, clientes y pagos
                    </p>

                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row gap-5 justify-center lg:justify-start">
                        <a href="#about"
                            class="bg-[#249373] hover:bg-[#1B637F] text-white font-semibold py-4 px-8 rounded-lg shadow-lg transition-all transform hover:scale-105 text-lg">
                            Ver demostración
                        </a>
                        <a href="<?php echo $baseUrl; ?>landing/inscripcion/inscripcion.php"
                            class="bg-white hover:bg-gray-50 text-[#1B637F] font-semibold py-4 px-8 rounded-lg border-2 border-[#1B637F]/30 shadow-md transition-all hover:shadow-lg text-lg">
                            Probar gratis
                        </a>
                    </div>
                </div>

                <!-- Imagen Hero -->
                <div class="order-1 lg:order-2 flex justify-center relative">
                    <!-- Badge sobre imagen -->
                    <div
                        class="absolute -top-5 -right-5 lg:right-0 bg-[#FFBF2F] text-[#1B637F] font-bold py-2 px-4 rounded-full shadow-lg z-10 text-sm rotate-6">
                        ¡Más de 5.000 profesionales!
                    </div>
                    <img src="<?php echo $baseUrl; ?>assets/img/landing/hero_section.png"
                        alt="Profesionales usando Agendarium"
                        class="w-full max-w-md rounded-2xl shadow-2xl border-8 border-white">
                </div>
            </div>
        </div>
    </section>

    <!-- Sección ¿Cómo Funciona? - Versión Slider Fullscreen -->
    <section id="how" class="relative min-h-screen py-16 overflow-hidden">
        <!-- Slider Principal -->
        <div class="swiper howItWorks-slider h-screen w-full">
            <div class="swiper-wrapper">
                <!-- Slide 1 - Registro -->
                <div class="swiper-slide relative">
                    <div class="absolute inset-0 bg-[#1B637F]/90 z-10"></div>
                    <img src="<?php echo $baseUrl; ?>assets/img/landing/slides/slider_1.jpg"
                        alt="Registro en Agendarium" class="absolute inset-0 w-full h-full object-cover">

                    <div class="container relative z-20 h-full flex items-center px-20">
                        <div class="max-w-2xl text-white">
                            <span
                                class="inline-block bg-white/20 text-sm font-semibold px-4 py-1 rounded-full mb-6 backdrop-blur-sm">
                                Paso 1 de 3
                            </span>
                            <h2 class="text-4xl md:text-6xl font-bold mb-6">Registro en <span class="text-[#FFBF2F]">2
                                    minutos</span></h2>
                            <p class="text-xl md:text-2xl opacity-90 mb-8">
                                Solo necesitas tu email y datos básicos. Sin configuraciones complicadas.
                            </p>
                            <ul class="space-y-3 mb-10">
                                <li class="flex items-center">
                                    <span class="material-icons text-[#FFBF2F] mr-2">check</span>
                                    <span>Sin requisitos técnicos</span>
                                </li>
                                <li class="flex items-center">
                                    <span class="material-icons text-[#FFBF2F] mr-2">check</span>
                                    <span>Guía paso a paso incluida</span>
                                </li>
                            </ul>
                            <a href="<?php echo $baseUrl; ?>landing/inscripcion/inscripcion.php"
                                class="inline-flex items-center bg-[#FFBF2F] hover:bg-[#FD9B19] text-[#1B637F] font-bold py-3 px-8 rounded-full transition-colors">
                                Comenzar ahora
                                <span class="material-icons ml-2">arrow_forward</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Slide 2 - Configuración -->
                <div class="swiper-slide relative">
                    <div class="absolute inset-0 bg-[#249373]/90 z-10"></div>
                    <img src="<?php echo $baseUrl; ?>assets/img/landing/slides/slider_2.jpg"
                        alt="Configuración de agenda" class="absolute inset-0 w-full h-full object-cover">

                    <div class="container relative z-20 h-full flex items-center px-20">
                        <div class="max-w-2xl text-white">
                            <span
                                class="inline-block bg-white/20 text-sm font-semibold px-4 py-1 rounded-full mb-6 backdrop-blur-sm">
                                Paso 2 de 3
                            </span>
                            <h2 class="text-4xl md:text-6xl font-bold mb-6">Configuración <span
                                    class="text-[#FFBF2F]">intuitiva</span></h2>
                            <p class="text-xl md:text-2xl opacity-90 mb-8">
                                Define horarios, servicios y políticas en nuestra interfaz optimizada.
                            </p>
                            <div class="grid grid-cols-2 gap-4 mb-10">
                                <div class="bg-white/10 p-4 rounded-lg backdrop-blur-sm">
                                    <span class="material-icons text-[#FFBF2F] block mb-2">schedule</span>
                                    <p>Horarios flexibles</p>
                                </div>
                                <div class="bg-white/10 p-4 rounded-lg backdrop-blur-sm">
                                    <span class="material-icons text-[#FFBF2F] block mb-2">paid</span>
                                    <p>Múltiples métodos de pago</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 3 - Compartir -->
                <div class="swiper-slide relative">
                    <div class="absolute inset-0 bg-[#2B819F]/90 z-10"></div>
                    <img src="<?php echo $baseUrl; ?>assets/img/landing/slides/slider_3.jpg" alt="Compartir agenda"
                        class="absolute inset-0 w-full h-full object-cover">

                    <div class="container relative z-20 h-full flex items-center px-20">
                        <div class="max-w-2xl text-white">
                            <span
                                class="inline-block bg-white/20 text-sm font-semibold px-4 py-1 rounded-full mb-6 backdrop-blur-sm">
                                Paso 3 de 3
                            </span>
                            <h2 class="text-4xl md:text-6xl font-bold mb-6">Comparte tu <span
                                    class="text-[#FFBF2F]">enlace único</span></h2>
                            <p class="text-xl md:text-2xl opacity-90 mb-8">
                                Envíalo por WhatsApp, intégralo en tu web o muestra el QR en tu local.
                            </p>
                            <div class="flex space-x-4 mb-10">
                                <div class="bg-white/10 p-3 rounded-full backdrop-blur-sm">
                                    <span class="material-icons text-[#FFBF2F]">qr_code</span>
                                </div>
                                <div class="bg-white/10 p-3 rounded-full backdrop-blur-sm">
                                    <span class="material-icons text-[#FFBF2F]">link</span>
                                </div>
                                <div class="bg-white/10 p-3 rounded-full backdrop-blur-sm">
                                    <span class="material-icons text-[#FFBF2F]">chat</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Controles del Slider -->
            <div class="swiper-pagination !bottom-8"></div>
            <div class="swiper-button-next !text-[#FFBF2F] !right-8"></div>
            <div class="swiper-button-prev !text-[#FFBF2F] !left-8"></div>
        </div>

        <!-- Miniaturas (Opcional) -->
        <div class="container px-4 mt-8 hidden md:block">
            <div class="swiper howItWorks-thumbs">
                <div class="swiper-wrapper">
                    <div class="swiper-slide !w-auto !mr-4">
                        <button
                            class="text-[#1B637F] bg-white/80 hover:bg-white px-6 py-2 rounded-full font-medium transition-colors">
                            Registro express
                        </button>
                    </div>
                    <div class="swiper-slide !w-auto !mr-4">
                        <button
                            class="text-[#249373] bg-white/80 hover:bg-white px-6 py-2 rounded-full font-medium transition-colors">
                            Configuración
                        </button>
                    </div>
                    <div class="swiper-slide !w-auto">
                        <button
                            class="text-[#2B819F] bg-white/80 hover:bg-white px-6 py-2 rounded-full font-medium transition-colors">
                            Compartir agenda
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Scripts de Swiper -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
    // Inicialización del slider
    const howItWorksSlider = new Swiper('.howItWorks-slider', {
        effect: 'fade',
        speed: 800,
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        thumbs: {
            swiper: {
                el: '.howItWorks-thumbs',
                slidesPerView: 'auto',
            }
        }
    });
    </script>

    <!-- Sección ¿Cómo Funciona? - Versión Mejorada -->
    <section id="how" class="min-h-screen py-16 bg-gradient-to-b from-white to-[#249373]/10">
        <div class="container mx-auto px-4">
            <!-- Encabezado -->
            <div class="text-center mb-16">
                <span class="inline-block bg-[#1B637F]/10 text-[#1B637F] font-semibold px-4 py-2 rounded-full mb-4">
                    Flujo de trabajo
                </span>
                <h2 class="text-4xl md:text-5xl font-bold text-[#1B637F] mb-4">
                    Simplificamos tu gestión diaria
                </h2>
                <p class="text-xl text-[#2B819F] max-w-3xl mx-auto">
                    Agendarium automatiza los procesos complejos para que tú puedas enfocarte en lo importante
                </p>
            </div>

            <!-- Pasos con ilustraciones -->
            <div class="grid md:grid-cols-3 gap-8 mb-16">
                <!-- Paso 1 -->
                <div
                    class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border-t-4 border-[#1B637F]">
                    <div class="flex items-center mb-6">
                        <div
                            class="bg-[#1B637F] text-white text-2xl font-bold w-12 h-12 rounded-full flex items-center justify-center mr-4">
                            1
                        </div>
                        <h3 class="text-2xl font-bold text-[#1B637F]">Registro Express</h3>
                    </div>
                    <p class="text-gray-600 mb-6">
                        Completa tu perfil en menos de 3 minutos y accede inmediatamente a tu panel de control.
                    </p>
                    <div class="flex justify-center">
                        <img src="<?php echo $baseUrl; ?>assets/img/icons/registro.svg" alt="Registro"
                            class="h-40 w-auto">
                    </div>
                </div>

                <!-- Paso 2 -->
                <div
                    class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border-t-4 border-[#249373]">
                    <div class="flex items-center mb-6">
                        <div
                            class="bg-[#249373] text-white text-2xl font-bold w-12 h-12 rounded-full flex items-center justify-center mr-4">
                            2
                        </div>
                        <h3 class="text-2xl font-bold text-[#1B637F]">Configuración Inteligente</h3>
                    </div>
                    <p class="text-gray-600 mb-6">
                        Define horarios, servicios y políticas con nuestra interfaz intuitiva que aprende de tus
                        preferencias.
                    </p>
                    <div class="flex justify-center">
                        <img src="<?php echo $baseUrl; ?>assets/img/icons/configuracion.svg" alt="Configuración"
                            class="h-40 w-auto">
                    </div>
                </div>

                <!-- Paso 3 -->
                <div
                    class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border-t-4 border-[#2B819F]">
                    <div class="flex items-center mb-6">
                        <div
                            class="bg-[#2B819F] text-white text-2xl font-bold w-12 h-12 rounded-full flex items-center justify-center mr-4">
                            3
                        </div>
                        <h3 class="text-2xl font-bold text-[#1B637F]">Comparte y Gestiona</h3>
                    </div>
                    <p class="text-gray-600 mb-6">
                        Envía tu enlace personalizado o integra directamente en tu web. Recibe pagos y confirmaciones
                        automáticas.
                    </p>
                    <div class="flex justify-center">
                        <img src="<?php echo $baseUrl; ?>assets/img/icons/compartir.svg" alt="Compartir"
                            class="h-40 w-auto">
                    </div>
                </div>
            </div>

            <!-- Demo integrada -->
            <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
                <div class="grid lg:grid-cols-2 gap-8 items-center">
                    <div class="p-8 md:p-12">
                        <h3 class="text-3xl font-bold text-[#1B637F] mb-4">Vista previa interactiva</h3>
                        <p class="text-xl text-[#2B819F] mb-6">
                            Así de simple es tu nuevo panel de control
                        </p>
                        <ul class="space-y-4 mb-8">
                            <li class="flex items-start">
                                <span class="material-icons text-[#249373] mr-2">check_circle</span>
                                <span>Visualización de citas en calendario</span>
                            </li>
                            <li class="flex items-start">
                                <span class="material-icons text-[#249373] mr-2">check_circle</span>
                                <span>Recordatorios automáticos</span>
                            </li>
                            <li class="flex items-start">
                                <span class="material-icons text-[#249373] mr-2">check_circle</span>
                                <span>Reportes de productividad</span>
                            </li>
                        </ul>
                        <a href="<?php echo $baseUrl; ?>landing/inscripcion/inscripcion.php"
                            class="inline-block bg-[#1B637F] hover:bg-[#2B819F] text-white font-semibold py-3 px-8 rounded-lg transition-all transform hover:scale-105">
                            Probar ahora
                        </a>
                    </div>
                    <div class="bg-gray-50 p-4 flex justify-center">
                        <img src="<?php echo $baseUrl; ?>assets/img/demo-panel.png" alt="Panel de control Agendarium"
                            class="rounded-lg shadow-md w-full max-w-lg border-8 border-white">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Sección Precios -->
    <section id="pricing" class="min-h-screen py-16 bg-white relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <img src="<?php echo $baseUrl; ?>assets/img/gratis.png" alt="Fondo" class="w-full h-full object-cover">
        </div>
        <div class="container mx-auto px-4 relative">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-darkbg mb-4">Nuestros Planes</h2>
                <p class="text-xl text-accent max-w-2xl mx-auto">Prueba todas las funcionalidades gratis por 15 días</p>
            </div>

            <div
                class="max-w-4xl mx-auto bg-white/90 backdrop-blur-sm rounded-xl shadow-lg p-8 md:p-12 border border-gray-100">
                <div class="grid md:grid-cols-2 gap-8">
                    <div class="border-r border-gray-200 pr-8">
                        <h3 class="text-2xl font-bold text-primary mb-4">Prueba Gratis</h3>
                        <div class="text-5xl font-bold text-darkbg mb-6">$0<span
                                class="text-lg font-normal text-gray-500">/15 días</span></div>
                        <ul class="space-y-3 mb-8">
                            <li class="flex items-center">
                                <span class="material-icons text-green-500 mr-2">check_circle</span>
                                <span>Acceso a todas las funciones</span>
                            </li>
                            <li class="flex items-center">
                                <span class="material-icons text-green-500 mr-2">check_circle</span>
                                <span>Hasta 50 citas mensuales</span>
                            </li>
                            <li class="flex items-center">
                                <span class="material-icons text-green-500 mr-2">check_circle</span>
                                <span>Soporte por correo</span>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-2xl font-bold text-primary mb-4">Plan Premium</h3>
                        <div class="text-5xl font-bold text-darkbg mb-6">$9.000<span
                                class="text-lg font-normal text-gray-500">/mes</span></div>
                        <ul class="space-y-3 mb-8">
                            <li class="flex items-center">
                                <span class="material-icons text-green-500 mr-2">check_circle</span>
                                <span>Citas ilimitadas</span>
                            </li>
                            <li class="flex items-center">
                                <span class="material-icons text-green-500 mr-2">check_circle</span>
                                <span>Recordatorios automáticos</span>
                            </li>
                            <li class="flex items-center">
                                <span class="material-icons text-green-500 mr-2">check_circle</span>
                                <span>Soporte prioritario</span>
                            </li>
                        </ul>
                    </div>
                    <div
                        class="bg-white p-6 rounded-xl border-l-4 border-[#249373] shadow-sm hover:shadow-[0_4px_12px_rgba(43,129,159,0.1)]">
                        <div
                            class="w-10 h-10 bg-[#1B637F]/10 text-[#1B637F] rounded-full flex items-center justify-center mb-4">
                            <span class="material-icons">notifications</span>
                        </div>
                        <h3 class="text-[#1B637F] font-semibold mb-2">Recordatorios automáticos</h3>
                        <p class="text-gray-600">Notificaciones inteligentes para reducir inasistencias</p>
                    </div>
                </div>

                <div class="text-center mt-8">
                    <a href="<?php echo $baseUrl; ?>landing/inscripcion/inscripcion.php"
                        class="inline-block bg-primary hover:bg-primary/90 text-white font-medium py-3 px-8 rounded-lg transition-colors">
                        Comenzar Prueba Gratis
                    </a>
                    <p class="text-sm text-gray-500 mt-3">Sin tarjeta de crédito requerida</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php
    include_once __DIR__ . '/landing/partials/footer.php';
    ?>


    <!-- JavaScript -->
    <script>
    // const baseUrl = '<?php echo $baseUrl; ?>';

    // // Scroll suave para los enlaces del navbar
    // document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    //     anchor.addEventListener('click', function(e) {
    //         e.preventDefault();

    //         const targetId = this.getAttribute('href');
    //         if (targetId === '#inscriptionModal') return;

    //         const targetElement = document.querySelector(targetId);
    //         if (targetElement) {
    //             window.scrollTo({
    //                 top: targetElement.offsetTop - 80,
    //                 behavior: 'smooth'
    //             });

    //             // Actualizar el item activo del navbar
    //             document.querySelectorAll('.nav-item').forEach(item => {
    //                 item.classList.remove('text-primary', 'font-medium');
    //                 item.classList.add('text-gray-600');
    //             });

    //             this.classList.add('text-primary', 'font-medium');
    //             this.classList.remove('text-gray-600');
    //         }
    //     });
    // });

    // // Cambiar el navbar al hacer scroll
    // window.addEventListener('scroll', function() {
    //     const sections = document.querySelectorAll('section');
    //     const scrollPosition = window.scrollY + 100;

    //     sections.forEach(section => {
    //         const sectionTop = section.offsetTop;
    //         const sectionHeight = section.offsetHeight;
    //         const sectionId = section.getAttribute('id');

    //         if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
    //             document.querySelectorAll('.nav-item').forEach(item => {
    //                 item.classList.remove('text-primary', 'font-medium');
    //                 item.classList.add('text-gray-600');
    //             });

    //             document.querySelector(`.nav-item[href="#${sectionId}"]`).classList.add('text-primary',
    //                 'font-medium');
    //             document.querySelector(`.nav-item[href="#${sectionId}"]`).classList.remove(
    //                 'text-gray-600');
    //         }
    //     });
    // });

    // // Manejar el formulario de inscripción (similar al anterior)
    // document.querySelector("#companyForm")?.addEventListener("submit", async function(e) {
    //     e.preventDefault();
    //     const formData = new FormData(this);

    //     try {
    //         const response = await fetch(`${baseUrl}inscripcion/controller/procesar_inscripcion.php`, {
    //             method: "POST",
    //             body: formData,
    //         });

    //         const {
    //             success,
    //             message,
    //             error
    //         } = await response.json();

    //         if (success) {
    //             // Cerrar modal de inscripción si está abierto
    //             const inscriptionModal = bootstrap.Modal.getInstance(document.getElementById(
    //                 "inscriptionModal"));
    //             if (inscriptionModal) inscriptionModal.hide();

    //             // Mostrar modal de éxito
    //             document.getElementById("responseMessage").innerText = message;
    //             new bootstrap.Modal(document.getElementById("responseModal")).show();
    //         } else {
    //             document.getElementById("responseMessage").innerText = message || error;
    //             new bootstrap.Modal(document.getElementById("responseModal")).show();
    //         }
    //     } catch (error) {
    //         console.error("Error:", error);
    //         document.getElementById("responseMessage").innerText =
    //             "Hubo un error al procesar la solicitud.";
    //         new bootstrap.Modal(document.getElementById("responseModal")).show();
    //     }
    // });
    </script>
</body>

</html>