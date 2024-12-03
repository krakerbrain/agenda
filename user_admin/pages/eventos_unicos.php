<?php
require_once dirname(__DIR__, 2) . '/configs/init.php';
require_once dirname(__DIR__, 2) . '/access-token/seguridad/JWTAuth.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';

$baseUrl = ConfigUrl::get();
$auth = new JWTAuth();
$auth->validarTokenUsuario();
?>

<div class="container mt-5">
    <h1 class="text-center">Crear Evento Único</h1>
    <form id="uniqueEventForm">
        <div class="mb-3">
            <label for="eventName" class="form-label">Nombre del Evento</label>
            <input type="text" class="form-control" id="eventName" name="event_name"
                placeholder="Ejemplo: Curso de Fototerapia" required>
        </div>
        <div class="mb-3">
            <label for="eventDescription" class="form-label">Descripción del Evento (Opcional)</label>
            <textarea class="form-control" id="eventDescription" name="event_description" rows="3"
                placeholder="Agrega una breve descripción del evento..."></textarea>
        </div>

        <div class="mb-3">
            <label for="eventDates" class="form-label">Fechas y Horarios</label>
            <table class="table" id="eventDatesTable">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Hora de Inicio</th>
                        <th>Hora de Fin</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="date" class="form-control" name="event_dates[]" required></td>
                        <td><input type="time" class="form-control" name="start_time[]" required></td>
                        <td><input type="time" class="form-control" name="end_time[]" required></td>
                        <td><button type="button" class="btn btn-danger removeRow">Eliminar</button></td>
                    </tr>
                </tbody>
            </table>
            <button type="button" class="btn btn-primary" id="addDateRow">Agregar Fecha y Hora</button>
            <small class="form-text text-muted">Puedes agregar múltiples fechas y horarios para el evento.</small>
        </div>

        <button type="submit" class="btn btn-primary">Crear Evento</button>
    </form>
</div>

<div class="container mt-4">
    <table class="table">
        <thead>
            <tr>
                <th>Evento</th>
                <th>Fecha y Hora</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="events-list">
            <!-- Aquí se cargarán los eventos creados -->
        </tbody>
    </table>
</div>