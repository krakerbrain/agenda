<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/dark.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>assets/css/form_reserva.css?v=<?php echo time(); ?>">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
    :root {
        --primary-color: <?php echo htmlspecialchars($primary_color);
        ?>;
        --secondary-color: <?php echo htmlspecialchars($secondary_color);
        ?>;
        --background-color: <?php echo htmlspecialchars($background_color);
        ?>;
        --button-color: <?php echo htmlspecialchars($button_color);
        ?>;
        --border-color: <?php echo htmlspecialchars($border_color);
        ?>;
    }
    </style>
</head>

<body>
    <div class="container mt-3">
        <!-- Tarjeta de Información de la Empresa -->
        <div class="company-card mb-3">
            <div class="row">
                <!-- Columna 1: Logo y redes sociales -->
                <div class="col-md-5 text-center">
                    <?php if ($company && $company['logo']) : ?>
                    <img src="<?php echo $baseUrl . $company['logo']; ?>" alt="Logo de la Empresa" class="img-fluid">
                    <?php endif; ?>
                </div>
                <!-- Columna 2: Nombre, dirección y teléfono -->
                <div class="col-md-7 text-center text-md-end align-content-end mt-3 mt-md-0">
                    <div class="company-name mb-2"><?php echo htmlspecialchars($company['name']); ?></div>
                    <div class="company-info"><?php echo htmlspecialchars($company['address']); ?></div>
                    <div class="company-info">Teléfono: <?php echo htmlspecialchars($company['phone']); ?></div>
                    <div class="mt-3 social-icons">
                        <?php foreach ($socialNetworks as $socials) : ?>
                        <a href="<?php echo htmlspecialchars($socials['url']); ?>" target="_blank"
                            title="<?php echo htmlspecialchars($socials['name']); ?>"><i
                                class="<?php echo htmlspecialchars($socials['icon_class']); ?>"></i></a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>