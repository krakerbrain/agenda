<div class="container text-end">
    <a tabindex="0" role="button" data-bs-trigger="focus" class="btn" data-bs-placement="left" data-bs-toggle="popover"
        data-bs-title="Servicios"
        data-bs-content="Aquí podrá configurar todos los servicios que preste con sus categorías, descripciones y duración de los servicios (en horas). Estos servicios más las configuraciones del calendario le permitiran a su cliente hacer correctamente la reserva">
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
                    <th>Duración (horas)</th>
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