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
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody id="servicesTableBody">


            </tbody>
        </table>
        <div class="d-flex flex-column row-cols-md-4">
            <button type="button" class="btn btn-primary mb-4" id="addServiceButton">Agregar Servicio</button>
            <button type="submit" class="btn btn-success">Guardar Configuración</button>
        </div>
    </form>
</div>