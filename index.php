<?php
require_once __DIR__ . '/classes/ConfigUrl.php';
$baseUrl = ConfigUrl::get();

include_once __DIR__ . '/landing/partials/head.php';
?>

<body class="bg-custom-light text-gray-800">
    <?php
    include_once __DIR__ . '/landing/partials/navbar.php';
    ?>

    <!-- Sección Hero con Logo Destacado -->
    <section id="home"
        class="min-h-screen pt-20 flex items-center justify-center bg-gradient-to-b from-white to-[#249373]/5">

        <div class="container mx-auto px-4 py-10">
            <!-- Logo Superior Centrado (Mobile) -->
            <div class="lg:hidden flex mb-6">
                <div class="flex items-center">
                    <img src="<?php echo $baseUrl; ?>assets/img/landing/logo/Isotipo-Agendarium.svg"
                        alt="Logo Agendarium" class="h-12 w-auto">
                    <span class="text-2xl font-bold text-[#1B637F] ml-1 mt-6">Agendarium</span>
                </div>
            </div>

            <!-- Contenido Textual -->
            <div class="mb-16">
                <!-- Logo + Nombre (Desktop) -->
                <div class="hidden lg:flex items-end mb-8">
                    <img src="<?php echo $baseUrl; ?>assets/img/landing/logo/Isotipo-Agendarium.svg"
                        alt="Logo Agendarium" class="h-28 w-auto mr-2">
                    <span class="text-2xl font-bold text-[#1B637F]">Agendarium</span>
                </div>

                <!-- Título -->
                <h1 id="hero-title-1"
                    class="hero-text-fade text-2xl sm:text-5xl xl:text-6xl font-bold md:mb-2 leading-tight text-[#1B637F]">
                    Controla tu agenda
                </h1>
                <h1 id="hero-title-2"
                    class="hero-text-fade text-2xl sm:text-5xl xl:text-6xl font-bold mb-6 leading-tight text-[#249373]">
                    como un profesional
                </h1>

                <!-- Subtítulo -->
                <p id="hero-subtitle"
                    class="hero-text-fade text-[#2B819F] mb-10 max-w-lg mx-auto lg:mx-0 font-semibold">
                    La herramienta de gestión de citas que necesitas para organizar tu tiempo de manera eficiente.
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-5">
                    <a href="#about"
                        class="bg-[#249373] hover:bg-[#1B637F] text-white font-semibold py-4 px-8 rounded-lg shadow-lg transition-all transform hover:scale-105 text-lg text-center">
                        Ver demostración
                    </a>
                    <a href="<?php echo $baseUrl; ?>landing/inscripcion/inscripcion.php"
                        class="bg-white hover:bg-gray-50 text-[#1B637F] font-semibold py-4 px-8 rounded-lg border-2 border-[#1B637F]/30 shadow-md transition-all hover:shadow-lg text-lg text-center">
                        Probar gratis
                    </a>
                </div>
            </div>
        </div>
    </section>


    <!-- Sección ¿Cómo Funciona? - Versión Actualizada -->
    <section id="how" class="relative min-h-screen py-16 overflow-hidden">
        <!-- Slider Principal -->
        <div class="swiper howItWorks-slider h-screen w-full">
            <div class="swiper-wrapper">
                <!-- Slide 1 - Sincronización -->
                <div class="swiper-slide relative">
                    <div class="absolute inset-0 bg-[#1B637F]/90 z-10"></div>
                    <img src="<?php echo $baseUrl; ?>assets/img/landing/slides/Productive_Businesswoman_at_Desk_simple_compose_.png"
                        alt="Sincronización con Google Calendar"
                        class="absolute inset-0 w-full h-full object-cover object-top object-top">

                    <div class="container relative z-20 h-full flex items-center px-4 lg:px-20">
                        <div class="max-w-2xl text-white">
                            <span
                                class="inline-block bg-white/20 text-sm font-semibold px-4 py-1 rounded-full mb-6 backdrop-blur-sm">
                                Integración Perfecta
                            </span>
                            <h2 class="text-2xl md:text-5xl font-bold mb-6">Sincronización automática con <span
                                    class="text-[#FFBF2F]">Google Calendar</span></h2>
                            <p class="text-xl opacity-90 mb-8">
                                Todas tus citas se reflejan automáticamente en tu calendario personal, manteniendo el
                                control total de tu agenda.
                            </p>
                            <ul class="space-y-4 mb-10">
                                <li class="flex items-start">
                                    <span class="material-icons text-[#FFBF2F] mr-2">sync</span>
                                    <span>Actualización en tiempo real</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="material-icons text-[#FFBF2F] mr-2">visibility</span>
                                    <span>Acceso desde cualquier dispositivo</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="material-icons text-[#FFBF2F] mr-2">security</span>
                                    <span>Tus datos siempre en tus plataformas</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Slide 2 - Gestión de Clientes -->
                <div class="swiper-slide relative">
                    <div class="absolute inset-0 bg-[#249373]/90 z-10"></div>
                    <img src="<?php echo $baseUrl; ?>assets/img/landing/slides/Cheerful_ Groomer_ Bliss_simple_compose_55.png"
                        alt="Gestión de clientes" class="absolute inset-0 w-full h-full object-cover object-top">

                    <div class="container relative z-20 h-full flex items-center px-4 lg:px-20">
                        <div class="max-w-2xl text-white">
                            <span
                                class="inline-block bg-white/20 text-sm font-semibold px-4 py-1 rounded-full mb-6 backdrop-blur-sm">
                                Organización Total
                            </span>
                            <h2 class="text-2xl md:text-5xl font-bold mb-6">Gestión completa de <span
                                    class="text-[#FFBF2F]">clientes</span></h2>
                            <p class="text-xl opacity-90 mb-8">
                                Registra historiales, incidentes y bloquear clientes problemáticos en un solo lugar.
                            </p>
                            <div class="grid grid-cols-2 gap-4 mb-10">
                                <div class="bg-white/10 p-4 rounded-lg backdrop-blur-sm">
                                    <span class="material-icons text-[#FFBF2F] block mb-2">history</span>
                                    <p>Historial completo</p>
                                </div>
                                <div class="bg-white/10 p-4 rounded-lg backdrop-blur-sm">
                                    <span class="material-icons text-[#FFBF2F] block mb-2">warning</span>
                                    <p>Registro de incidentes</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 3 - Multi-usuario -->
                <div class="swiper-slide relative">
                    <div class="absolute inset-0 bg-[#2B819F]/90 z-10"></div>
                    <img src="<?php echo $baseUrl; ?>assets/img/landing/slides/Stylis_ at_Work_simple_compose_55.png"
                        alt="Trabajo en equipo" class="absolute inset-0 w-full h-full object-cover object-top">

                    <div class="container relative z-20 h-full flex items-center px-4 lg:px-20">
                        <div class="max-w-2xl text-white">
                            <span
                                class="inline-block bg-white/20 text-sm font-semibold px-4 py-1 rounded-full mb-6 backdrop-blur-sm">
                                Gestión de Equipos
                            </span>
                            <h2 class="text-2xl md:text-5xl font-bold mb-6">Coordina a <span class="text-[#FFBF2F]">tu
                                    equipo</span></h2>
                            <p class="text-xl opacity-90 mb-8">
                                Asigna profesionales y gestiona sus agendas desde un solo lugar. Cada miembro ve solo
                                sus citas y disponibilidad.
                            </p>
                            <div class="grid grid-cols-2 gap-4 mb-10">
                                <div class="bg-white/10 p-4 rounded-lg backdrop-blur-sm text-center">
                                    <span class="material-icons text-[#FFBF2F] block mb-2">admin_panel_settings</span>
                                    <p class="font-medium">Control centralizado</p>
                                    <p class="text-sm opacity-80 mt-1">Tú defines horarios y servicios</p>
                                </div>
                                <div class="bg-white/10 p-4 rounded-lg backdrop-blur-sm text-center">
                                    <span class="material-icons text-[#FFBF2F] block mb-2">person</span>
                                    <p class="font-medium">Vista individual</p>
                                    <p class="text-sm opacity-80 mt-1">Cada profesional ve solo su agenda</p>
                                </div>
                            </div>
                            <!-- Nota sobre roadmap (opcional) -->
                            <div class="bg-white/5 border-l-4 border-[#FFBF2F] px-4 py-2 text-sm italic">
                                <span class="material-icons align-middle text-[#FFBF2F] mr-1">update</span>
                                Próximamente: Configuración granular de permisos por usuario
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 4 - WhatsApp -->
                <div class="swiper-slide relative">
                    <div class="absolute inset-0 bg-[#1B637F]/90 z-10"></div>
                    <img src="<?php echo $baseUrl; ?>assets/img/landing/slides/Molecular_Gastronomy_Masterpiece_simple_compose.png"
                        alt="Integración con WhatsApp" class="absolute inset-0 w-full h-full object-cover object-top">

                    <div class="container relative z-20 h-full flex items-center px-4 lg:px-20">
                        <div class="max-w-2xl text-white">
                            <span
                                class="inline-block bg-white/20 text-sm font-semibold px-4 py-1 rounded-full mb-6 backdrop-blur-sm">
                                Comunicación Directa
                            </span>
                            <h2 class="text-2xl md:text-5xl font-bold mb-6">Notificaciones <span
                                    class="text-[#FFBF2F]">automáticas</span></h2>
                            <p class="text-xl opacity-90 mb-8">
                                Confirmaciones y recordatorios enviados por WhatsApp <span class="block sm:inline">+
                                    notificaciones por correo electrónico</span>
                            </p>

                            <!-- Lista de beneficios actualizada -->
                            <ul class="space-y-4 mb-10">
                                <li class="flex items-start">
                                    <span class="material-icons text-[#FFBF2F] mr-2">chat</span>
                                    <div>
                                        <span class="font-medium">WhatsApp Instantáneo</span>
                                        <p class="text-sm opacity-80">Confirmaciones directas al móvil de tus clientes
                                        </p>
                                    </div>
                                </li>
                                <li class="flex items-start">
                                    <span class="material-icons text-[#FFBF2F] mr-2">email</span>
                                    <div>
                                        <span class="font-medium">Notificaciones por Email</span>
                                        <p class="text-sm opacity-80">Registro formal para tu historial</p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Controles del Slider -->
            <div class="swiper-pagination !bottom-8"></div>
            <div class="swiper-button-next !text-[#FFBF2F] !right-8"></div>
            <div class="swiper-button-prev !text-[#FFBF2F] !left-8"></div>
        </div>
    </section>
    <!-- Sección ¿Cómo Funciona? - Versión Mejorada -->
    <section id="functions" class="min-h-screen py-16 bg-gradient-to-b from-white to-[#249373]/10">
        <div class="container mx-auto px-4">
            <!-- Encabezado -->
            <div class="text-center mb-16">
                <span class="inline-block bg-[#1B637F]/10 text-[#1B637F] font-semibold px-4 py-2 rounded-full mb-4">
                    Flujo de trabajo
                </span>
                <h2 class="text-2xl md:text-5xl font-bold text-[#1B637F] mb-4">
                    Simplificamos tu gestión diaria
                </h2>
                <p class="text-xl text-[#2B819F] max-w-3xl mx-auto">
                    Agendarium automatiza los procesos complejos para que tú puedas enfocarte en lo importante
                </p>
            </div>

            <!-- Pasos con ilustraciones -->
            <div class="grid md:grid-cols-3 gap-8 mb-16">
                <!-- Paso 1 - Registro con Soporte -->
                <div
                    class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border-t-4 border-[#1B637F]">
                    <div class="flex items-center mb-6">
                        <div
                            class="bg-[#1B637F] text-white text-2xl font-bold w-12 h-12 rounded-full flex items-center justify-center mr-4">
                            1
                        </div>
                        <h3 class="text-2xl font-bold text-[#1B637F]">Registro con Asistencia</h3>
                    </div>

                    <!-- Lista de soporte -->
                    <ul class="space-y-4 mb-6">
                        <li class="flex items-start">
                            <span class="material-icons text-[#FFBF2F] mr-2">support_agent</span>
                            <div>
                                <span class="font-medium">Guía paso a paso</span>
                                <p class="text-sm text-gray-500">Te acompañamos en cada campo requerido</p>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <span class="material-icons text-[#FFBF2F] mr-2">videocam</span>
                            <div>
                                <span class="font-medium">Tutoriales interactivos</span>
                                <p class="text-sm text-gray-500">Videos cortos explicando cada sección</p>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <span class="material-icons text-[#FFBF2F] mr-2">schedule</span>
                            <div>
                                <span class="font-medium">Asistencia rápida</span>
                                <p class="text-sm text-gray-500">Soporte técnico en menos de 1 hora</p>
                            </div>
                        </li>
                    </ul>

                    <!-- Imagen con estilo consistente -->
                    <div class="flex justify-center mt-4">
                        <img src="<?php echo $baseUrl; ?>assets/img/landing/card_funciones/registro.png"
                            alt="Registro asistido" class="h-32 w-auto">
                    </div>
                </div>

                <!-- Paso 2 - Configuración -->
                <div
                    class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border-t-4 border-[#249373]">
                    <div class="flex items-center mb-6">
                        <div
                            class="bg-[#249373] text-white text-2xl font-bold w-12 h-12 rounded-full flex items-center justify-center mr-4">
                            2
                        </div>
                        <h3 class="text-2xl font-bold text-[#1B637F]">Configura tu Estructura</h3>
                    </div>
                    <p class="text-gray-600 mb-6">
                        Define cuidadosamente tus servicios, horarios disponibles y equipo de trabajo. Esta
                        configuración es fundamental para tu operación.
                    </p>
                    <ul class="space-y-3 mb-6">
                        <li class="flex items-start">
                            <span class="material-icons text-[#249373] mr-2">schedule</span>
                            <span>Bloques horarios personalizados</span>
                        </li>
                        <li class="flex items-start">
                            <span class="material-icons text-[#249373] mr-2">groups</span>
                            <span>Asignación de profesionales</span>
                        </li>
                    </ul>
                    <div class="flex justify-center">
                        <img src="<?php echo $baseUrl; ?>assets/img/landing/card_funciones/configuracion.png"
                            alt="Configuración" class="h-40 w-auto">
                    </div>
                </div>

                <!-- Paso 3 - Operación -->
                <div
                    class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border-t-4 border-[#2B819F]">
                    <div class="flex items-center mb-6">
                        <div
                            class="bg-[#2B819F] text-white text-2xl font-bold w-12 h-12 rounded-full flex items-center justify-center mr-4">
                            3
                        </div>
                        <h3 class="text-2xl font-bold text-[#1B637F]">Operación Diaria</h3>
                    </div>
                    <p class="text-gray-600 mb-6">
                        Gestiona citas, clientes y eventos con herramientas diseñadas para simplificar tu día a día.
                    </p>
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="bg-[#F8FAFC] p-3 rounded-lg text-center">
                            <span class="material-icons text-[#2B819F] block mb-1">chat</span>
                            <p class="text-sm">Confirmaciones por WhatsApp</p>
                        </div>
                        <div class="bg-[#F8FAFC] p-3 rounded-lg text-center">
                            <span class="material-icons text-[#2B819F] block mb-1">history</span>
                            <p class="text-sm">Historial de clientes</p>
                        </div>
                    </div>
                    <div class="flex justify-center">
                        <img src="<?php echo $baseUrl; ?>assets/img/landing/card_funciones/operacion.png"
                            alt="Operación" class="h-40 w-auto">
                    </div>
                </div>
            </div>

            <!-- Sección Demo Mejorada -->
            <div id="demo" class="bg-white rounded-2xl shadow-2xl overflow-hidden border border-gray-100">
                <div class="grid lg:grid-cols-2 gap-0 items-stretch">
                    <!-- Texto descriptivo -->
                    <div class="p-8 md:p-12 flex flex-col justify-center">
                        <h3 class="text-2xl font-bold text-[#1B637F] mb-4">Conoce Agendarium en acción</h3>
                        <p class="text-xl text-[#2B819F] mb-6">
                            Descubre cómo organizamos tu agenda profesional
                        </p>

                        <!-- Lista con iconos mejorados -->
                        <ul class="space-y-4 mb-8">
                            <li class="flex items-start">
                                <div class="bg-[#249373]/10 p-1 rounded-full mr-3">
                                    <span class="material-icons text-[#249373] text-lg">calendar_view_month</span>
                                </div>
                                <div>
                                    <span class="font-medium">Vista de calendario inteligente</span>
                                    <p class="text-sm text-gray-500 mt-1">Agrupado por profesional o servicio</p>
                                </div>
                            </li>
                            <li class="flex items-start">
                                <div class="bg-[#249373]/10 p-1 rounded-full mr-3">
                                    <span class="material-icons text-[#249373] text-lg">chat</span>
                                </div>
                                <div>
                                    <span class="font-medium">WhatsApp integrado</span>
                                    <p class="text-sm text-gray-500 mt-1">Confirmaciones automáticas</p>
                                </div>
                            </li>
                            <li class="flex items-start">
                                <div class="bg-[#249373]/10 p-1 rounded-full mr-3">
                                    <span class="material-icons text-[#249373] text-lg">groups</span>
                                </div>
                                <div>
                                    <span class="font-medium">Equipos organizados</span>
                                    <p class="text-sm text-gray-500 mt-1">Cada profesional ve solo sus citas</p>
                                </div>
                            </li>
                        </ul>

                        <!-- CTA mejorado -->
                        <div class="flex flex-col sm:flex-row gap-4">
                            <a href="<?php echo $baseUrl; ?>demo/"
                                class="flex-1 bg-[#1B637F] hover:bg-[#2B819F] text-white font-semibold py-3 px-6 rounded-lg text-center transition-colors flex items-center justify-center"
                                id="btn-demo">
                                <span class="material-icons mr-2">play_circle</span>
                                Mira como funciona
                            </a>
                            <a href="#contacto"
                                class="flex-1 border border-[#1B637F] text-[#1B637F] hover:bg-[#1B637F]/5 font-medium py-3 px-6 rounded-lg text-center transition-colors">
                                Solicitar prueba gratis
                            </a>
                        </div>
                        <!-- Modal de Video -->
                        <div id="video-modal"
                            class="fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center hidden">
                            <div class="bg-white rounded-lg overflow-hidden max-w-2xl w-full relative shadow-lg">
                                <button id="close-modal"
                                    class="absolute top-2 right-2 text-gray-600 hover:text-black text-xl font-bold">×</button>
                                <video controls autoplay class="w-full h-auto" id="modal-video">
                                    <source src="<?php echo $baseUrl; ?>assets/videos/Agendarium-Web.mp4"
                                        type="video/mp4">
                                    Tu navegador no soporta el video.
                                </video>
                            </div>
                        </div>

                    </div>

                    <!-- Contenedor multimedia -->
                    <div
                        class="bg-gradient-to-br from-[#1B637F]/5 to-[#249373]/5 p-8 flex items-center justify-center relative min-h-[400px]">

                        <!-- Video demostrativo -->
                        <video controls preload="none"
                            poster="<?php echo $baseUrl; ?>assets/img/landing/video/video_poster.png"
                            class="rounded-lg shadow-md w-full h-full object-cover max-w-full border-4 border-white"
                            style="object-fit: cover;">
                            <source src="<?php echo $baseUrl; ?>assets/videos/Agendarium-Web.mp4" type="video/mp4">
                            Tu navegador no soporta el video.
                        </video>

                        <!-- Badge de "Interactivo" -->
                        <div
                            class="absolute bottom-6 right-6 bg-[#FFBF2F] text-[#1B637F] text-xs font-bold px-3 py-1 rounded-full animate-pulse">
                            INTERACTIVO
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </section>

    <!-- Sección Precios Actualizada -->
    <section id="pricing" class="min-h-screen py-16 bg-white relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-[#1B637F]/5 to-[#249373]/5 z-0"></div>
        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center mb-16">
                <h2 class="text-2xl md:text-4xl font-bold text-[#1B637F] mb-4">Planes para Todo Tipo de Negocios</h2>
                <p class="text-xl text-[#2B819F] max-w-2xl mx-auto">
                    Prueba 7 días gratis + 23 días adicionales al completar tu configuración
                </p>
            </div>

            <!-- Grid de Planes -->
            <div class="grid md:grid-cols-3 gap-8 max-w-6xl mx-auto">

                <!-- Plan Básico -->
                <div
                    class="bg-white rounded-2xl shadow-lg overflow-hidden border border-[#1B637F]/20 hover:shadow-xl transition-all">
                    <div class="p-6 border-b border-[#1B637F]/10">
                        <h3 class="text-2xl font-bold text-[#1B637F]">Básico</h3>
                        <div class="text-3xl font-bold my-4">$5,000 <span
                                class="text-lg font-normal text-gray-500">/mes</span></div>
                        <p class="text-gray-600">Perfecto para profesionales independientes</p>
                    </div>
                    <div class="p-6">
                        <ul class="space-y-4 mb-6">
                            <li class="flex items-start">
                                <span class="material-icons text-[#249373] mr-2">person</span>
                                <span>1 usuario (administrador)</span>
                            </li>
                            <li class="flex items-start">
                                <span class="material-icons text-[#249373] mr-2">email</span>
                                <span>Notificaciones por correo</span>
                            </li>
                            <li class="flex items-start">
                                <span class="material-icons text-[#249373] mr-2">event</span>
                                <span>Citas ilimitadas</span>
                            </li>
                            <li class="flex items-start">
                                <span class="material-icons text-[#249373] mr-2">sync</span>
                                <span>Sincronización con Google Calendar</span>
                            </li>
                        </ul>
                        <a href="<?php echo $baseUrl; ?>landing/inscripcion/inscripcion.php"
                            class="block w-full bg-[#1B637F] hover:bg-[#2B819F] text-white text-center font-medium py-3 px-4 rounded-lg transition-colors">
                            Comenzar prueba
                        </a>
                    </div>
                </div>

                <!-- Plan Profesional (Destacado) -->
                <div
                    class="bg-white rounded-2xl shadow-2xl overflow-hidden border-2 border-[#FFBF2F] transform hover:-translate-y-2 transition-all">
                    <div class="bg-[#FFBF2F] text-[#1B637F] text-center py-2 font-bold">
                        MÁS POPULAR
                    </div>
                    <div class="p-6 border-b border-[#1B637F]/10">
                        <h3 class="text-2xl font-bold text-[#1B637F]">Profesional</h3>
                        <div class="text-3xl font-bold my-4">$10,000 <span
                                class="text-lg font-normal text-gray-500">/mes</span></div>
                        <p class="text-gray-600">Ideal para pequeños equipos</p>
                    </div>
                    <div class="p-6">
                        <ul class="space-y-4 mb-6">
                            <li class="flex items-start">
                                <span class="material-icons text-[#249373] mr-2">groups</span>
                                <span>3 usuarios (admin + 2 colaboradores)</span>
                            </li>
                            <li class="flex items-start">
                                <span class="material-icons text-[#249373] mr-2">chat</span>
                                <span>WhatsApp ilimitado</span>
                            </li>
                            <li class="flex items-start">
                                <span class="material-icons text-[#249373] mr-2">event_available</span>
                                <div>
                                    <span class="font-medium">Eventos grupales</span>
                                    <p class="text-sm text-gray-500 mt-1">Talleres, citas masivas o cursos (hasta 5/mes)
                                    </p>
                                </div>
                            </li>
                            <li class="flex items-start">
                                <span class="material-icons text-[#249373] mr-2">support_agent</span>
                                <span>Soporte prioritario</span>
                            </li>
                        </ul>
                        <a href="<?php echo $baseUrl; ?>landing/inscripcion/inscripcion.php"
                            class="block w-full bg-[#FFBF2F] hover:bg-[#FD9B19] text-[#1B637F] text-center font-bold py-3 px-4 rounded-lg transition-colors">
                            Elegir este plan
                        </a>
                    </div>
                </div>

                <!-- Plan Avanzado -->
                <div
                    class="bg-white rounded-2xl shadow-lg overflow-hidden border border-[#1B637F]/20 hover:shadow-xl transition-all">
                    <div class="p-6 border-b border-[#1B637F]/10">
                        <h3 class="text-2xl font-bold text-[#1B637F]">Avanzado</h3>
                        <div class="text-3xl font-bold my-4">$15,000 <span
                                class="text-lg font-normal text-gray-500">/mes</span></div>
                        <p class="text-gray-600">Para negocios establecidos</p>
                    </div>
                    <div class="p-6">
                        <ul class="space-y-4 mb-6">
                            <li class="flex items-start">
                                <span class="material-icons text-[#249373] mr-2">group_add</span>
                                <span>10 usuarios incluidos</span>
                            </li>
                            <li class="flex items-start">
                                <span class="material-icons text-[#249373] mr-2">chat</span>
                                <span>WhatsApp + correo ilimitados</span>
                            </li>
                            <li class="flex items-start">
                                <span class="material-icons text-[#249373] mr-2">event_repeat</span>
                                <div>
                                    <span class="font-medium">Eventos ilimitados</span>
                                    <p class="text-sm text-gray-500 mt-1">Talleres, clases o citas grupales sin límite
                                    </p>
                                </div>
                            </li>
                            <li class="flex items-start">
                                <span class="material-icons text-[#249373] mr-2">verified_user</span>
                                <span>Soporte VIP</span>
                            </li>
                        </ul>
                        <a href="<?php echo $baseUrl; ?>landing/inscripcion/inscripcion.php"
                            class="block w-full bg-[#1B637F] hover:bg-[#2B819F] text-white text-center font-medium py-3 px-4 rounded-lg transition-colors">
                            Comenzar prueba
                        </a>
                    </div>
                </div>
            </div>

            <!-- Nota sobre prueba gratuita -->
            <div class="text-center mt-12 max-w-2xl mx-auto bg-[#F8FAFC] p-4 rounded-lg border border-[#249373]/20">
                <p class="text-gray-600">
                    <span class="material-icons align-middle text-[#249373]">info</span>
                    <strong>Prueba gratuita:</strong> 7 días automáticos + 23 días adicionales al completar tu
                    configuración inicial.
                </p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php
    include_once __DIR__ . '/landing/partials/footer.php';
    ?>
    <!-- Scripts de Swiper -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="<?php echo $baseUrl; ?>assets/js/landing/index.js"></script>
</body>

</html>