<div>
    <form id="workScheduleForm">
        <input type="hidden" name="company_id" id="company_id" value="1"> <!-- Replace with dynamic company ID -->
        <table class="table table-bordered table-striped table-sm">
            <thead>
                <tr>
                    <th>Día</th>
                    <th>Estado</th>
                    <th>Inicio de la jornada</th>
                    <th>Fin de la jornada</th>
                    <th>Descanso</th>
                    <th></th>
                </tr>
            </thead>
            <tbody class="work-schedule">
                <!-- PHP code to dynamically generate rows for each day of the week -->
                <?php
                $daysOfWeek = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
                foreach ($daysOfWeek as $day) {
                    $celdaCopiaTodo = $day == 'Lunes' ? "<button type='button' class='btn btn-link copy-all'>Copiar en todos</button>" : '';
                    echo "<tr class='work-day'>
                                <td>$day</td>
                                <td>
                                    <div class='form-check form-switch'>
                                        <input class='form-check-input' type='checkbox' name='days[$day][enabled]' onchange='toggleDay(this, \"$day\")' checked>
                                    </div>
                                </td>
                                <td>
                                    <input type='time' class='form-control' name='days[$day][start]' value='09:00'>
                                    <p class='text-muted' name='days[$day][closed]' style='display: none'>Cerrado</p>
                                </td>
                                <td>
                                    <input type='time' class='form-control' name='days[$day][end]' value='20:00'>
                                    <p class='text-muted' name='days[$day][closed]' style='display: none'>Cerrado</p>
                                </td>
                                <td>
                                    <button type='button' name='days[$day][break]' class='btn btn-outline-primary btn-sm' onclick='addBreakTime(this, \"$day\")'>+ Descanso</button>
                                </td>
                                <td>
                                    $celdaCopiaTodo
                                </td>
                            </tr>";
                }
                ?>
            </tbody>
        </table>
        <button type="submit" class="btn btn-primary mt-3">Guardar Configuración</button>
    </form>
</div>