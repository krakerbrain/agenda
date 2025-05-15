<?php
require_once dirname(__DIR__) . '/classes/ConfigUrl.php';
$baseUrl = ConfigUrl::get();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demo App</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/table.css">
    <script src="https://cdn.jsdelivr.net/npm/driver.js@latest/dist/driver.js.iife.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/driver.js@latest/dist/driver.css" />
    <script src="<?= $baseUrl ?>assets/js/demo/driver.js"> </script>
    <!-- Estilos personalizados -->
    <style>
    .local-badge-style {
        font-size: 0.6rem;
        top: -5px;
    }

    .navbar-brand.titulo {
        font-size: 1.5rem;
    }

    .nav.navbar {
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .time-field {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .time-label {
        font-size: 0.75rem;
        /* Tamaño pequeño para el texto */
        margin-bottom: 0.25rem;
        /* Separación entre la etiqueta y el input */
        color: #6c757d;
        /* Color neutro */
    }

    .time-box {
        width: 50px;
        /* Ancho del campo */
        text-align: center;
        padding: 0.25rem;
    }
    </style>

</head>

<body>
    <!-- Navbar -->
    <header class="nav navbar sticky-top bg-dark-subtle">
        <nav class="container-xxl">
            <a class="navbar-brand titulo" href="#" id="app-title">Demo App</a>
            <div class="d-flex align-items-center">
                <!-- Botón de notificaciones (solo visual) -->
                <div class="dropdown me-3">
                    <button class="btn btn-link text-dark p-0" type="button" id="notificationDropdown"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-envelope fs-6 position-relative"></i>
                        <span
                            class="position-absolute start-100 translate-middle badge rounded-pill bg-danger local-badge-style">
                            0
                            <span class="visually-hidden">notificaciones no leídas</span>
                        </span>
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown"
                        style="min-width: 300px; max-height: 400px; overflow-y: auto;">
                        <li>
                            <h6 class="dropdown-header">Notificaciones</h6>
                        </li>
                        <div id="notification-list">
                            <li class="px-3 py-2 text-center text-muted">
                                No hay notificaciones nuevas
                            </li>
                        </div>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item text-center small" href="#">Ver todas</a></li>
                    </ul>
                </div>
                <!-- Botón para abrir el offcanvas (solo visual) -->
                <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasMenu"
                    aria-controls="offcanvasMenu" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
        </nav>
    </header>

    <!-- Contenedor principal de horarios -->
    <div class="container my-5" id="workScheduleContainer">
        <!-- Formulario de horarios -->
        <form id="workScheduleForm" class="border p-4 rounded">
            <div id="unsavedChangesAlert" class="alert alert-warning d-none" role="alert">
                <strong>Recuerda guardar los cambios antes de salir.</strong>
                Si sales sin guardar, perderás los cambios realizados.
                <button type="submit" class="btn btn-primary btn-sm">Guardar Configuración</button>
            </div>

            <!-- Tabla de horarios -->
            <table class="table table-borderless table-striped table-sm">
                <thead>
                    <tr class="head-table">
                        <th>Día</th>
                        <th>Estado</th>
                        <th>Inicio de la jornada</th>
                        <th>Fin de la jornada</th>
                        <th>Acción</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="scheduleTableBody">
                    <!-- Los horarios se cargarán aquí con JavaScript -->
                </tbody>
            </table>
            <button type="submit" class="btn btn-primary mt-3 goToServicesBtn">Guardar Configuración</button>
        </form>
    </div>

    <div class="container my-5 d-none" id="servicesContainer">
        <!-- Botón de información -->
        <div class="text-end mb-2">
            <a tabindex="0" role="button" class="btn" data-bs-toggle="popover" data-bs-trigger="focus"
                data-bs-placement="left" data-bs-title="Servicios"
                data-bs-content="Aquí podrá configurar todos los servicios que prestes con sus categorías, descripciones y duración">
                <i class="fa fa-circle-question text-primary" style="font-size: 1.5rem;"></i>
            </a>
        </div>

        <!-- Formulario de servicios -->
        <form id="servicesForm" class="border p-4 rounded">
            <table class="table table-borderless table-striped table-sm">
                <thead>
                    <tr class="head-table">
                        <th>Habilitado<i class="fa fa-info-circle ps-1" data-bs-toggle="tooltip" data-bs-placement="top"
                                title="Define si el servicio está disponible para todos los usuarios"></i></th>
                        <th>Nombre del Servicio</th>
                        <th class="text-center">Duración</th>
                        <th>Observaciones</th>
                        <th>Categorías</th>
                        <th>Días Disponible</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody id="servicesTableBody">
                    <!-- Fila de servicio única (demo) -->
                    <tr class="service-row body-table">
                        <!-- Habilitado -->
                        <td data-cell="Habilitado" class="data">
                            <div class="form-check form-switch service-switch">
                                <input type="checkbox" class="form-check-input" name="service_enabled[1]" checked>
                            </div>
                        </td>

                        <!-- Nombre -->
                        <td data-cell="nombre servicio" class="data">
                            <input type="text" class="form-control service-name" name="service_name[1]"
                                value="Corte de cabello">
                        </td>

                        <!-- Duración -->
                        <td data-cell="horas duración" class="data service-duration">
                            <div class="time-input d-flex align-items-center justify-content-xl-center gap-2">
                                <div class="time-field">
                                    <input type="number" id="hours" name="service_duration_hours[1]"
                                        class="form-control time-box" min="0" step="1" value="1">
                                    <label for="hours" class="time-label">Horas</label>
                                </div>
                                <div class="time-field">
                                    <input type="number" id="minutes" name="service_duration_minutes[1]"
                                        class="form-control time-box" min="0" max="59" step="1" value="30">
                                    <label for="minutes" class="time-label">Minutos</label>
                                </div>
                            </div>
                        </td>

                        <!-- Observaciones -->
                        <td data-cell="observaciones" class="data service-observations">
                            <textarea class="form-control"
                                name="service_observations[1]">Corte de cabello para adultos</textarea>
                        </td>

                        <!-- Categorías -->
                        <td data-cell="agrega categorías" class="data">
                            <button type="button"
                                class="btn btn-outline-primary btn-sm add-category">+Categoría</button>
                        </td>

                        <!-- Días disponibles -->
                        <td data-cell="días disponibles" class="data">
                            <div class="days-container d-flex gap-1">

                                <div class="day align-items-center d-flex flex-column text-center ">
                                    <input type="checkbox" class="form-check-input"
                                        name="available_service_day[new-service-2][]" value="1" checked>
                                    <label class="mt-1">L</label>
                                </div>
                                <div class="day align-items-center d-flex flex-column text-center ">
                                    <input type="checkbox" class="form-check-input"
                                        name="available_service_day[new-service-2][]" value="2" checked>
                                    <label class="mt-1">M</label>
                                </div>
                                <div class="day align-items-center d-flex flex-column text-center ">
                                    <input type="checkbox" class="form-check-input"
                                        name="available_service_day[new-service-2][]" value="3" checked>
                                    <label class="mt-1">M</label>
                                </div>
                                <div class="day align-items-center d-flex flex-column text-center ">
                                    <input type="checkbox" class="form-check-input"
                                        name="available_service_day[new-service-2][]" value="4" checked>
                                    <label class="mt-1">J</label>
                                </div>
                                <div class="day align-items-center d-flex flex-column text-center ">
                                    <input type="checkbox" class="form-check-input"
                                        name="available_service_day[new-service-2][]" value="5" checked>
                                    <label class="mt-1">V</label>
                                </div>
                                <div class="day align-items-center d-flex flex-column text-center ">
                                    <input type="checkbox" class="form-check-input"
                                        name="available_service_day[new-service-2][]" value="6" checked>
                                    <label class="mt-1">S</label>
                                </div>
                                <div class="day align-items-center d-flex flex-column text-center ">
                                    <input type="checkbox" class="form-check-input"
                                        name="available_service_day[new-service-2][]" value="7">
                                    <label class="mt-1">D</label>
                                </div>
                            </div>
                        </td>

                        <!-- Acción -->
                        <td>
                            <button type="button" class="btn btn-danger btn-sm delete-service">Eliminar</button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Botones -->
            <button type="button" class="btn btn-outline-primary mb-4" id="addServiceButton">
                <i class="fa fa-plus"></i> Agregar Nuevo Servicio
            </button>

            <div class="d-flex justify-content-end mt-3">
                <button type="button" class="btn btn-secondary backToScheduleBtn mb-3">
                    <i class="fa fa-arrow-left"></i> Volver a Horarios
                </button>
                <button class="btn btn-success goToBookingForm">
                    <i class="fa fa-save"></i> Guardar Configuración
                </button>
            </div>
        </form>
    </div>


    <!-- Offcanvas menu (solo visual) -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasMenu" aria-labelledby="offcanvasMenuLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasMenuLabel">Menú</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <p>Menú de navegación (sin funcionalidad en este demo)</p>
        </div>
    </div>

    <!-- Bootstrap JS y dependencias -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $baseUrl ?>assets/js/demo/app.js"> </script>

</body>

</html>