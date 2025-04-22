<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>assets/vendors/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>assets/vendors/css/flatpickr/flatpickr.min.css">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>assets/vendors/css/flatpickr/theme/dark.css">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>assets/vendors/css/font-awesome/all.min.css">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>assets/css/form_reserva.css?v=<?php echo time(); ?>">
    <script src="<?php echo $baseUrl; ?>assets/vendors/js/jquery/jquery-3.6.0.min.js"></script>
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

    .banner {
        height: 150px;
        background-image: url('<?php echo $baseUrl . $selected_banner; ?>'), linear-gradient(to left, var(--secondary-color), var(--background-color));
        background-size: cover;
        background-position: top;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        border-radius: 10px 10px 0 0;
    }

    .banner::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
    }

    .banner-text {
        position: relative;
        z-index: 1;
        color: white;
        /* Color claro para el texto */
        font-size: 1.8rem;
        /* Tamaño del texto */
        font-weight: bold;
        /* Sombra para mejorar la legibilidad */
        text-align: center;
        display: <?php echo ( !empty($selected_banner) && preg_match('/^assets\/img\/banners\/user_\d+\/banner_user_prefered_\d+\.png$/', $selected_banner)) ? 'none': 'block';
        ?>;

    }

    /* si $servicesProvidersCount es mayor a 1 */

    <?php if ($servicesProvidersCount > 1) : ?>.provider-section:nth-child(odd) {
        background-color: color-mix(in srgb, var(--secondary-color) 20%, white);
    }

    .provider-section:nth-child(even) {
        background-color: color-mix(in srgb, var(--secondary-color) 40%, white);
    }

    .provider-section .provider-container {
        padding: 1rem;
    }

    <?php endif;
    ?>
    </style>
</head>

<body>
    <div class="container mt-3">
        <!-- Tarjeta de Información de la Empresa -->
        <!-- Banner -->

        <div class="company-card mb-3">
            <div class="banner mb-md-3">
                <div class="banner-text">
                    <?php echo htmlspecialchars($company['name']) ?>
                </div>
            </div>
            <div class="row">
                <!-- Columna 1: Logo y redes sociales -->
                <div class="col-6 text-md-start pt-3 pt-md-0">
                    <?php if ($company && $company['logo']) : ?>
                    <img src="<?php echo $baseUrl . $company['logo']; ?>" alt="Logo de la Empresa" class="img-fluid">
                    <?php endif; ?>
                </div>
                <!-- Columna 2: Nombre, dirección y teléfono -->
                <div class="col-6 text-end align-content-end mt-3 mt-md-0">
                    <div class="company-name"><?php echo htmlspecialchars($company['name']); ?></div>
                    <div class="company-info"><?php echo htmlspecialchars($company['address']); ?></div>
                    <div class="company-info"><i class="fas fa-phone" style="font-size: 0.6rem;"></i>
                        <?php echo htmlspecialchars($company['phone']); ?></div>
                    <div class="social-icons">
                        <?php foreach ($socialNetworks as $socials) : ?>
                        <a href="<?php echo htmlspecialchars($socials['url']); ?>" target="_blank"
                            title="<?php echo htmlspecialchars($socials['name']); ?>"><i
                                class="<?php echo htmlspecialchars($socials['icon_class']); ?>"></i></a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="text-md-start text-center">
                <p class="description mb-0 mt-1"><?php echo htmlspecialchars($company['description']); ?></p>
            </div>
        </div>