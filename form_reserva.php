<?php
require_once __DIR__ . '/classes/ConfigUrl.php';
require_once __DIR__ . '/reservas/controller/CompanyController.php';
$baseUrl =          ConfigUrl::get();

$url            = isset($_GET['path']) ? $_GET['path'] : null;
$view = isset($_GET['view']) ? $_GET['view'] : 'form';
$reservation_id = isset($_GET['reservation_id']) ? $_GET['reservation_id'] : 0;

echo "reservation_id: " . htmlspecialchars($reservation_id);

$controller        = new CompanyController();

$data              = $controller->getCompanyData($url);
$company           = $data['company'];
$socialNetworks    = $data['socialNetworks'];
$services          = $data['services'];
$style             = $data['style'];

$primary_color     = $style['primary_color'];
$secondary_color   = $style['secondary_color'];
$background_color  = $style['background_color'];
$button_color      = $style['button_color'];
$border_color      = $style['border_color'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservar Cita</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/dark.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>assets/css/form_reserva.css?v=<?php echo time(); ?>">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
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
                    <div class="company-name mb-2"><?php echo $company['name'] ?></div>
                    <div class="company-info"><?php echo $company['address'] ?></div>
                    <div class="company-info">Teléfono: <?php echo $company['phone'] ?></div>
                    <div class="mt-3 social-icons">
                        <?php foreach ($socialNetworks as $socials) : ?>
                        <a href="<?php echo $socials['url'] ?>" target="_blank"
                            title="<?php echo $socials['name'] ?>"><i
                                class="<?php echo $socials['icon_class'] ?>"></i></a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <form id="appointmentForm" style="max-width: 600px; margin: 0 auto;">
            <!-- PASO 1 -->
            <div id="step1" class="step">
                <h4 class="text-center mb-4 pass-title">Paso 1: Escoge el Servicio</h4>
                <div class="mb-3">
                    <label for="service" class="form-label">Servicio:</label>
                    <select id="service" name="service" class="form-select" required>
                        <option value="" selected>Selecciona un servicio</option>
                        <?php foreach ($services as $service) : ?>
                        <option value="<?php echo $service['id']; ?>"
                            data-observation="<?php echo htmlspecialchars($service['observations']); ?>">
                            <?php echo htmlspecialchars($service['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div id="serviceObservation" class="mb-3 d-none">
                    <span id="serviceTextObservation"
                        class="form-control-plaintext bg-danger-subtle text-danger-emphasis p-2"></span>
                </div>
                <div id="categoryContainer" class="mb-3 d-none">
                    <label for="category" class="form-label">Categoría:</label>
                    <select id="category" name="category" class="form-select" required>
                        <option value="" selected>Selecciona una categoría</option>
                    </select>
                </div>
                <div id="categoryObservation" class="mb-3 d-none">
                    <span id="categoryTextObservation"
                        class="form-control-plaintext bg-danger-subtle text-danger-emphasis p-2"></span>
                </div>

                <button type="button" class="btn btn-secondary btn-siguiente" onclick="showStep(2)">Siguiente</button>
            </div>
            <!-- PASO 2 -->
            <div id="step2" class="step d-none">
                <h4 class="text-center mb-4 pass-title">Paso 2: Escoge Fecha y Hora</h4>
                <div class="mb-3">
                    <label for="date" class="form-label">Fecha:</label>
                    <input type="date" id="date" name="date" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="time" class="form-label">Hora:</label>
                    <input type="hidden" name="schedule_mode" id="schedule_mode"
                        value="<?php echo htmlspecialchars($company['schedule_mode']); ?>">
                    <select id="time" name="time" class="form-select" required>
                        <option value="" selected>Selecciona una hora</option>
                    </select>
                </div>
                <button type="button" class="btn btn-secondary btn-anterior" onclick="showStep(1)">Anterior</button>
                <button type="button" class="btn btn-secondary btn-siguiente" onclick="showStep(3)">Siguiente</button>
            </div>
            <!-- PASO 3 -->
            <div id="step3" class="step d-none">
                <h4 class="text-center mb-4 pass-title">Paso 3: Llena tus Datos</h4>
                <input type="hidden" name="company_id" id="company_id"
                    value="<?php echo htmlspecialchars($company['id']); ?>">
                <div class="mb-3">
                    <label for="name" class="form-label">Nombre:</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Teléfono:</label>
                    <input type="tel" id="phone" name="phone" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="mail" class="form-label">Correo:</label>
                    <input type="email" id="mail" name="mail" class="form-control" required>
                </div>
                <button type="button" class="btn btn-secondary btn-anterior" onclick="showStep(2)">Anterior</button>
                <button id="reservarBtn" class="btn btn-secondary btn-siguiente" type="submit">
                    <span class="spinner-border spinner-border-sm d-none" aria-hidden="true"></span>
                    <span class="button-text">Reservar</span>
                </button>
            </div>
        </form>
        <div class="modal fade" id="responseModal" tabindex="-1" aria-labelledby="responseModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="responseModalLabel">Reserva exitosa</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-siguiente" id="acceptButton"
                            data-bs-dismiss="modal">Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
    const baseUrl = "<?php echo $baseUrl; ?>";
    const company_days_available = <?php echo json_encode($company['calendar_days_available']); ?>;
    </script>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <script src="<?php echo $baseUrl; ?>assets/js/form_reserva/index.js?v=<?php echo time(); ?>"></script>
</body>

</html>