<?php
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';
require_once dirname(__DIR__, 2) . '/classes/CompanyManager.php';

$baseUrl = ConfigUrl::get();
$company_id = isset($_GET['company_id']) ? $_GET['company_id'] : null;
$controller = new CompanyManager();
$data = $controller->getAllCompanyData($company_id);

// $company = $data['company'];
$socialNetworks = $data['socialNetworks'];
$reservation = $data['reservation']; // Datos de la reserva
$notes = explode(',', $reservation['notas_correo_reserva']); // Convertir las notas en array

$style = $data['style'];
$primary_color = $style['primary_color'];
$secondary_color = $style['secondary_color'];
$background_color = $style['background_color'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver mi Reserva</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>assets/css/form_reserva.css?v=<?php echo time(); ?>">
    <style>
        :root {
            --primary-color: <?php echo htmlspecialchars($primary_color);
                                ?>;
            --background-color: <?php echo htmlspecialchars($background_color);
                                ?>;
        }
    </style>
</head>

<body>
    <div class="container mt-4">
        <!-- Información de la Empresa -->
        <div class="company-card mb-4 p-3 bg-light rounded">
            <div class="row">
                <div class="col-md-5 text-center">
                    <?php if ($company['logo']) : ?>
                        <img src="<?php echo $baseUrl . $company['logo']; ?>" class="img-fluid mb-3" alt="Logo">
                    <?php endif; ?>
                </div>
                <div class="col-md-7 text-center text-md-end">
                    <h3><?php echo htmlspecialchars($company['name']); ?></h3>
                    <p><?php echo htmlspecialchars($company['address']); ?></p>
                    <p>Tel: <?php echo htmlspecialchars($company['phone']); ?></p>
                    <div class="social-icons">
                        <?php foreach ($socialNetworks as $social) : ?>
                            <a href="<?php echo $social['url']; ?>" target="_blank">
                                <i class="<?php echo $social['icon_class']; ?>"></i>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información de la Reserva -->
        <div class="reservation-card p-4 bg-white rounded shadow">
            <h4 class="text-center mb-4">Detalles de la Reserva</h4>
            <ul class="list-group">
                <li class="list-group-item">
                    <strong>Cliente:</strong> <?php echo htmlspecialchars($reservation['name']); ?>
                </li>
                <li class="list-group-item">
                    <strong>Teléfono:</strong> <?php echo htmlspecialchars($reservation['phone']); ?>
                </li>
                <li class="list-group-item">
                    <strong>Correo:</strong> <?php echo htmlspecialchars($reservation['email']); ?>
                </li>
                <li class="list-group-item">
                    <strong>Servicio:</strong> <?php echo htmlspecialchars($reservation['service_name']); ?>
                </li>
                <li class="list-group-item">
                    <strong>Fecha:</strong> <?php echo htmlspecialchars($reservation['date']); ?>
                </li>
                <li class="list-group-item">
                    <strong>Hora:</strong> <?php echo htmlspecialchars($reservation['time']); ?>
                </li>
            </ul>

            <!-- Notas de la Reserva -->
            <div class="notes-section mt-4">
                <h5>Notas:</h5>
                <?php if (!empty($notes[0])) : ?>
                    <ul class="list-group">
                        <?php foreach ($notes as $note) : ?>
                            <li class="list-group-item"><?php echo htmlspecialchars($note); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php else : ?>
                    <p>No se agregaron notas para esta reserva.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>