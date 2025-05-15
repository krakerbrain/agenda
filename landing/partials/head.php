<!DOCTYPE html>
<html lang="es">

<head>
    <!-- Metadatos básicos y SEO -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Agendarium | Software de Gestión de Citas para Profesionales</title>
    <meta name="description"
        content="Agendarium simplifica la gestión de citas, recordatorios por WhatsApp y organización de equipos. Prueba gratis tu sistema de agenda profesional.">
    <link rel="canonical" href="<?php echo $baseUrl; ?>">

    <!-- Favicon -->
    <link rel="icon" href="<?php echo $baseUrl; ?>assets/img/landing/favicon/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" sizes="180x180"
        href="<?php echo $baseUrl; ?>assets/img/landing/favicon/apple-touch-icon.png">

    <!-- Preconexiones críticas -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net">

    <!-- Fuentes locales primero -->
    <style>
        @font-face {
            font-family: 'CarrigPro-Regular';
            src: url(assets/fonts/CarrigPro-Regular.woff2) format('woff2');
            font-display: swap;
        }

        :root {
            font-family: 'CarrigPro-Regular', sans-serif;
        }

        body {
            font-family: 'CarrigPro-Regular', sans-serif;
            background-color: #FAFAFA;
        }

        #home {
            background-image:
                linear-gradient(to right, rgba(255, 255, 255) 0%, rgba(255, 255, 255, 0) 70%),
                url('assets/img/landing/hero_section_2.jpg');
            /* solo la imagen */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            /* background-color: rgb(248 255 233 / 22%); */
            background-blend-mode: lighten;

            position: relative;
            z-index: 0;
            overflow: hidden;
        }

        /* Gradiente encima de la imagen */
        #home::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to right, rgba(255, 255, 255, 1) 0%, rgba(255, 255, 255, 0) 70%);
            z-index: 1;
            pointer-events: none;
        }

        /* Imagen de transición */
        #home::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            opacity: 0;
            transition: opacity 1s ease-in-out;
            z-index: 0;
        }


        /* Activación del fade */
        #home.image-fading::after {
            background-image: var(--next-bg);
            opacity: 1;
        }

        /* Aseguramos que el contenido esté por encima de ::before y ::after */
        #home>.container {
            position: relative;
            z-index: 2;
        }


        /* Animación de texto */
        .hero-text-fade {
            opacity: 1;
            transition: opacity 0.8s ease-in-out;
        }

        .fade-out {
            opacity: 0;
        }

        .fade-in {
            opacity: 1;
        }


        /* Animación personalizada para Tailwind */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .smooth-section {
            animation: fadeInUp 0.8s ease-out forwards;
        }

        /* Efecto hover para nav links */
        .nav-hover-effect {
            position: relative;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .nav-hover-effect::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background-color: #1B637F;
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .nav-hover-effect:hover::after {
            width: 100%;
        }

        .nav-hover-effect.active {
            color: #1B637F;
            font-weight: 500;
        }

        .nav-hover-effect.active::after {
            width: 100%;
        }
    </style>

    <!-- Tailwind CSS (antes de otras hojas de estilo) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1B637F',
                        secondary: '#249373',
                        accent: '#FFBF2F',
                        lightbg: '#FAFAFA',
                        darkbg: '#1A1728',
                    },
                    fontFamily: {
                        sans: ['CarrigPro-Regular', 'Roboto', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <!-- Hojas de estilo externas -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

    <!-- Metadatos sociales (no críticos) -->
    <meta property="og:title" content="Agendarium - Agenda Automatizada para Negocios">
    <meta property="og:description"
        content="Sistema de gestión de citas con WhatsApp, multi-usuario y sincronización con Google Calendar.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo $baseUrl; ?>">
    <meta property="og:image" content="<?php echo $baseUrl; ?>assets/img/og-image.jpg">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Agendarium | Agenda Profesional en Línea">
    <meta name="twitter:description" content="Organiza citas, clientes y equipos con nuestra plataforma todo-en-uno.">

    <!-- Schema.org (al final del head) -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "SoftwareApplication",
            "name": "Agendarium",
            "description": "Sistema de gestión de citas para negocios en Chile con WhatsApp y Google Calendar",
            "operatingSystem": "Web",
            "applicationCategory": "BusinessApplication",
            "offers": [{
                    "@type": "Offer",
                    "name": "Plan Básico",
                    "price": "5000",
                    "priceCurrency": "CLP"
                },
                {
                    "@type": "Offer",
                    "name": "Plan Profesional",
                    "price": "10000",
                    "priceCurrency": "CLP"
                }
            ]
        }
    </script>
</head>