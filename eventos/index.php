<?php
require_once dirname(__DIR__) . '/classes/ConfigUrl.php';
require_once dirname(__DIR__) . '/classes/CompanyController/CompanyController.php';
require_once dirname(__DIR__) . '/classes/UniqueEvents.php';


$baseUrl = ConfigUrl::get();
$url = isset($_GET['path']) ? $_GET['path'] : null;
$pageTitle = "Eventos";

// Crear una instancia del controlador
$controller = new CompanyController();

// Obtener los datos de la empresa
$data = $controller->getCompanyData($url);
$company = $data['company'];
$socialNetworks = $data['socialNetworks'];
$style = $data['style'];

// Establecer variables de estilo
$primary_color = $style['primary_color'];
$secondary_color = $style['secondary_color'];
$background_color = $style['background_color'];
$button_color = $style['button_color'];
$border_color = $style['border_color'];

// Crear una instancia de la clase UniqueEvents
$uniqueEvents = new UniqueEvents();

// Obtener los eventos vigentes
$events = $uniqueEvents->get_upcoming_events($company['id']);

// Verificar si hay un evento seleccionado en la URL
$selectedEventId = isset($_GET['event_id']) ? intval($_GET['event_id']) : null;


include __DIR__ . '/templates/header.php';
?>
<div class="container mt-4 events-container" style="max-width: 600px;">
    <h1 class="text-center">Eventos Disponibles</h1>

    <?php if (!empty($events)): ?>
        <div class="container p-0">
            <div class="row">
                <?php foreach ($events as $event): ?>
                    <div class="col-12 mb-4 p-0">
                        <div class="card company-card p-0" style="border-radius: 8px;color:#1c1c1c">
                            <div class="row no-gutters">
                                <!-- Sección izquierda: Nombre y fechas -->
                                <div class="col-md-6 p-md-5 pt-4 px-5">
                                    <h3 class="card-title" style="font-weight: 900;"><?= htmlspecialchars($event['name']) ?>
                                    </h3>
                                    <div class="mt-3">
                                        <strong>Fechas y horarios:</strong>
                                        <ul class="list-unstyled mt-2">
                                            <?php foreach ($event['dates'] as $date): ?>
                                                <li>
                                                    <span><?= date('d/m/Y', strtotime($date['event_date'])) ?></span> -
                                                    <span><?= date('H:i', strtotime($date['event_start_time'])) ?>
                                                        a <?= date('H:i', strtotime($date['event_end_time'])) ?></span>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>

                                <!-- Sección derecha: Descripción y botón -->
                                <div class="col-md-6">
                                    <div class="bg-gradient d-flex flex-column justify-content-between h-100  p-3">
                                        <p class="card-text mb-4"><?= htmlspecialchars($event['description']) ?></p>
                                        <button class="btn btn-primary w-100 mt-auto open-registration-form"
                                            data-event-id="<?= $event['id'] ?>"
                                            style="background-color: <?= $secondary_color ?>; border:none">
                                            Inscribirse
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php else: ?>
        <p class="text-center">No hay eventos disponibles actualmente.</p>
    <?php endif; ?>



</div>
<!-- Formulario de Inscripción (oculto inicialmente) -->
<div id="registrationFormContainer" class="container mt-4" style="display: none; max-width: 600px;">
    <h3 class="text-center">Formulario de Inscripción</h3>
    <h5 class="curso text-center"></h5>
    <form id="eventRegistrationForm">
        <input type="hidden" id="selected_event_id" name="event_id">
        <div class="mb-3">
            <label for="name" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="mb-3">
            <label for="telefono" class="form-label">Teléfono</label>
            <input type="text" class="form-control" id="telefono" name="telefono" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Correo Electrónico</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <button class="btn btn-secondary" style="background-color: <?= $secondary_color ?>; border:none">
            Inscribirse
        </button>
    </form>
</div>
<?php
include __DIR__ . '/templates/footer.php';
?>