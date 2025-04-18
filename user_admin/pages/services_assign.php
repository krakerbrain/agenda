<div class="container mt-4">
    <!-- Selector de Usuario -->
    <div class="row mb-4">
        <div class="col-md-6">
            <label for="userSelect" class="form-label">Seleccionar Usuario</label>
            <select id="userSelect" class="form-select">
                <!-- Opciones de usuarios se cargarán dinámicamente -->
            </select>
        </div>
    </div>

    <!-- Tabla de Servicios Disponibles -->
    <div class="card">
        <div class="card-header">
            <h5>Servicios Disponibles</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="servicesTable" class="table table-hover">
                    <thead>
                        <tr>
                            <th width="5%">Asignar</th>
                            <th width="35%">Servicio</th>
                            <th width="60%">Días Disponibles</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Contenido se cargará dinámicamente -->
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer text-end">
            <button id="saveAssignments" class="btn btn-primary">Guardar Asignaciones</button>
        </div>
    </div>
</div>

<?php include dirname(__DIR__, 2) . '/includes/modal-info.php';
?>

<!-- Estilos adicionales -->
<style>
    .disabled-day {
        opacity: 0.5;
        pointer-events: none;
    }

    .day {
        padding: 5px;
        border-radius: 4px;
    }

    .day:hover:not(.disabled-day) {
        background-color: #f8f9fa;
    }

    .days-container {
        display: flex;
        gap: 10px;
    }
</style>