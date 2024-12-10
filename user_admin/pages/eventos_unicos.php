<?php
require_once dirname(__DIR__, 2) . '/configs/init.php';
require_once dirname(__DIR__, 2) . '/access-token/seguridad/JWTAuth.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';

$baseUrl = ConfigUrl::get();
$auth = new JWTAuth();
$auth->validarTokenUsuario();
?>
<style>
    @media (max-width: 1000px) {
        .eventListBody th {
            display: block;
        }

        .eventListBody tr {
            display: flex;
            justify-content: space-between;
        }

        body {
            font-size: 90%;
            /* Reduce el tamaño al 90% (14.4px) */
        }

        .btn {
            font-size: 90%;
        }

    }

    /* Ajustar aún más para pantallas muy pequeñas */
    @media (max-width: 480px) {
        body {
            font-size: 85%;
            /* Reduce al 85% (13.6px) */
        }

        .btn {
            font-size: 85%;
        }
    }
</style>
<div class="container mt-5">
    <h1 class="text-center">Crear Evento Único</h1>
    <form id="uniqueEventForm">
        <div class="mb-3">
            <label for="eventName" class="form-label">Nombre del Evento</label>
            <input type="text" class="form-control" id="eventName" name="event_name"
                placeholder="Ejemplo: Curso de Fototerapia" required>
        </div>
        <div class="mb-3">
            <label for="eventQuota" class="form-label">Cupo máximo de inscripciones</label>
            <input type="number" name="event_quota" id="eventQuota" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="eventDescription" class="form-label">Descripción del Evento (Opcional)</label>
            <textarea class="form-control" id="eventDescription" name="event_description" rows="3"
                placeholder="Agrega una breve descripción del evento..."></textarea>
        </div>

        <div class="mb-3">
            <h6>Fechas y Horarios</h6>
            <table class="table eventStylesTable" id="eventDatesTable">
                <tbody>
                    <tr>
                        <td class="form-floating"><input type="date" class="form-control" id="floatingDate"
                                name="event_dates[]" required>
                            <label for="floatingDate">Selecciona una fecha</label>
                        </td>
                        <td class="form-floating"><input type="time" id="floatingStartTime" class="form-control"
                                name="start_time[]" required>
                            <label for="floatingStartTime">Hora de inicio</label>
                        </td>
                        <td class="form-floating"><input type="time" id="floatingEndTime" class="form-control"
                                name="end_time[]" required>
                            <label for="floatingEndTime">Hora de inicio</label>
                        </td>
                        <td class="form-floating"><button type="button"
                                class="btn btn-danger removeRow">Eliminar</button></td>
                    </tr>
                </tbody>
            </table>
            <button type="button" class="btn btn-primary" id="addDateRow">Agregar Fecha y Hora</button>
            <small class="form-text text-muted">Puedes agregar múltiples fechas y horarios para el evento.</small>
        </div>

        <button type="submit" class="btn btn-primary">Crear Evento</button>
    </form>
</div>

<div class="container mt-4 event-container">

</div>