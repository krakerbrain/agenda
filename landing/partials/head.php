<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendarium - Gestión de Citas</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet"> -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#4F46E5',
                        secondary: '#EA98A2',
                        accent: '#0DCAF0',
                        lightbg: '#FAFAFA',
                        darkbg: '#1A1728',
                    },
                    fontFamily: {
                        sans: ['Roboto', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <style>
        @font-face {
            font-family: 'CarrigPro-Regular';
            src: url(assets/fonts/CarrigPro-Regular.woff2) format('woff2');
        }

        :root {
            font-family: 'CarrigPro-Regular', sans-serif;
        }

        body {
            font-family: 'CarrigPro-Regular', sans-serif;
        }

        .bg-custom-light {
            background-color: #FAFAFA;
        }
    </style>
</head>