<?php
require_once __DIR__ . '/classes/ConfigUrl.php';
$baseUrl = ConfigUrl::get();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Agenda Road</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
    body {
        background-color: #D4D4D4 !important;
    }

    .hero {
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
    }

    .hero img {
        max-width: 100%;
        height: auto;
    }

    .login-link {
        margin-top: 20px;
    }
    </style>
</head>

<body>
    <section class="hero">
        <h1 class="text-center display-4">Agenda Road</h1>
        <div class="container text-center">
            <!-- Espacio para la imagen central -->
            <img src="<?php echo $baseUrl; ?>assets/img/landing_agenda_road.png" alt="Agenda Road" />
            <!-- Link para el login -->
            <div class="login-link">
                <a href="<?php echo $baseUrl; ?>login/index.php" class="btn btn-info w-50">Iniciar sesión</a>
            </div>
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
</body>

</html>