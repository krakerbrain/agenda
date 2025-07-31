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
    <?php if ($isDemo): ?>
        <script src="https://cdn.jsdelivr.net/npm/driver.js@latest/dist/driver.js.iife.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/driver.js@latest/dist/driver.css" />
        <script src="<?php echo $baseUrl ?>assets/js/form_reserva/driver.js"> </script>
    <?php endif; ?>
    <style>
        @font-face {
            font-family: 'CarrigPro-Regular';
            src: url("<?php echo $baseUrl; ?>assets/fonts/CarrigPro-Regular.woff2") format('woff2');
            font-display: swap;
        }

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
            font-family: 'CarrigPro-Regular', sans-serif;
        }

        .nav {
            --bs-gutter-x: 1.5rem;
            background: #fff3;
            max-width: 600px;
            margin: auto;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);

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
            display: <?php echo (!empty($selected_banner)) ? 'none' : 'block';
                        ?>;

        }

        .photo-container {
            position: relative;
            cursor: pointer;
        }

        .photo-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.3);
            opacity: 0;
            transition: opacity 0.3s ease;
            display: flex;
            align-items: flex-end;
            justify-content: flex-end;
            padding: 10px;
        }

        .photo-overlay:hover {
            opacity: 1;
        }

        .overlay-icon {
            color: white;
            font-size: 1.5rem;
            text-shadow: 0 0 5px rgba(0, 0, 0, 0.5);
        }

        /* Estilos para el icono en móvil */
        .mobile-icon {
            position: absolute;
            bottom: 0;
            right: 15px;
            background: white;
            border-radius: 50%;
            padding: 3px;
            font-size: 0.8rem;
            color: var(--bs-primary);
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
        }

        /* Ajustes para el modal */
        .modal-content {
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .agendarium-logo-text {
            font-family: 'CarrigPro-Regular', sans-serif;
            color: #1b637f;
        }

        .agendarium-logo-text span {
            font-size: 1.25rem;
        }

        .agendarium-logo-text p {
            font-size: 0.825rem;
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
        /* crear responsive para texto de agendarium-logo-text */
        @media (max-width: 576px) {
            .agendarium-logo-text span {
                font-size: 1rem;
            }

            .agendarium-logo-text p {
                font-size: 0.7rem;
            }

            .nav {
                margin-right: 0.8rem;
                margin-left: 0.8rem;
            }

        }
    </style>
</head>

<body>
    <header class="nav navbar-brand">
        <nav class="container-xxl d-flex align-items-center" style="min-height: 64px;">
            <a href="<?php echo $baseUrl; ?>" class="text-decoration-none">
                <div class="d-flex align-items-center gap-2 gap-md-3 mb-md-0">
                    <img src="<?php echo $baseUrl; ?>assets/img/landing/logo/Isotipo-Agendarium.svg"
                        alt="Logo Agendarium" class="img-fluid" style="height: 2.5rem; width: auto;" />

                    <div class="agendarium-logo-text">
                        <span class="fw-semibold">Agendarium</span>
                        <p class="mb-0">Gestión de citas simplificada</p>
                    </div>
                </div>
            </a>
        </nav>
    </header>
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
                </div>
                <div class="social-icons text-end">
                    <?php foreach ($socialNetworks as $socials) : ?>
                        <a href="<?php echo htmlspecialchars($socials['url']); ?>" target="_blank"
                            title="<?php echo htmlspecialchars($socials['name']); ?>"><i
                                class="<?php echo htmlspecialchars($socials['icon_class']); ?>"></i></a>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="text-md-start text-center">
                <p class="description mb-0 mt-1"><?php echo htmlspecialchars($company['description']); ?></p>
            </div>
        </div>