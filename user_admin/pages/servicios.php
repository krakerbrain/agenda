<?php
require_once dirname(__DIR__, 2) . '/configs/init.php';
require_once dirname(__DIR__, 2) . '/access-token/seguridad/JWTAuth.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';

$baseUrl = ConfigUrl::get();
$auth = new JWTAuth();
$auth->validarTokenUsuario();
?>
<style>
    .time-input {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        justify-content: center;
        /* Espaciado entre los campos */
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
        width: 60px;
        /* Ancho del campo */
        text-align: center;
        padding: 0.5rem;
    }
</style>
<div class="container text-end">
    <a tabindex="0" role="button" data-bs-trigger="focus" class="btn" data-bs-placement="left" data-bs-toggle="popover"
        data-bs-title="Servicios"
        data-bs-content="Aquí podrá configurar todos los servicios que prestes con sus categorías, descripciones y duración de los servicios (en horas). Estos servicios más las configuraciones del calendario le permitiran a su cliente hacer correctamente la reserva">
        <i class="fa fa-circle-question text-primary" style="font-size: 1.5rem;"></i>
    </a>
</div>
<div class="container">
    <form id="servicesForm" method="POST" class="border p-4 rounded">
        <input type="hidden" value="" id="tempId">
        <table class="table table-borderless table-striped table-sm">
            <thead>
                <tr>
                    <th>Habilitado</th>
                    <th>Nombre del Servicio</th>
                    <th class="text-center">Duración</th>
                    <th>Observaciones</th>
                    <th>Categorías</th>
                    <th>Días Disponible</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody id="servicesTableBody">


            </tbody>
        </table>
        <!-- Botón para agregar servicio más cerca de la tabla -->
        <button type="button" class="btn btn-outline-primary mb-4" id="addServiceButton">
            <i class="fa fa-plus"></i> Agregar Nuevo Servicio
        </button>
        <!-- Botón para guardar en una sección separada de acción final -->
        <div class="d-flex justify-content-end mt-3">
            <button type="submit" class="btn btn-success">
                <i class="fa fa-save"></i> Guardar Configuración
            </button>
        </div>
    </form>
    <?php include dirname(__DIR__, 2) . '/includes/modal-servicios.php';
    ?>
</div>