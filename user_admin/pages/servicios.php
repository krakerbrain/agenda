<div class="container my-5">
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