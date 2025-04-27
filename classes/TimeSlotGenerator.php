<?php


class TimeSlotGenerator
{
    public static function generate($horaInicio, $horaFin, $duracion, $break_start = null, $break_end = null, $time_step = null)
    {
        $rangos = [];

        // Convertimos las horas a timestamp
        $horaInicioTimestamp = strtotime($horaInicio);
        $horaFinTimestamp = strtotime($horaFin);
        $breakStartTimestamp = $break_start ? strtotime($break_start) : null;
        $breakEndTimestamp = $break_end ? strtotime($break_end) : null;

        // Definir el paso de tiempo (30 o 60 minutos según la duración)
        $paso = $time_step ?? $duracion;

        // Si el servicio dura 4 horas o más, se restringe su disponibilidad
        if ($duracion >= 240) {
            // Solo permitir reservas a la primera hora de la mañana o de la tarde
            if ($duracion >= 480) {
                // Servicios de 8 horas solo pueden comenzar a las 09:30
                $posiblesHorarios = [$horaInicioTimestamp];
            } else {

                // Servicios de 4 a 7 horas pueden empezar a las 09:30 o 15:00

                // Solo agregamos el horario después del break si existe un break definido
                if ($breakEndTimestamp) {
                    $posiblesHorarios = [$horaInicioTimestamp, $breakEndTimestamp];
                } else {
                    $horarioActual = $horaInicioTimestamp;
                    $duracionSegundos = $duracion * 60; // duración viene en minutos

                    while (($horarioActual + $duracionSegundos) <= $horaFinTimestamp) {
                        $posiblesHorarios[] = $horarioActual;
                        $horarioActual += $duracionSegundos;
                    }
                }
            }

            foreach ($posiblesHorarios as $inicio) {
                $fin = $inicio + ($duracion * 60);
                if ($fin <= $horaFinTimestamp) {
                    $rangos[] = date('H:i', $inicio) . ' - ' . date('H:i', $fin);
                }
            }
        } else {
            if (!$breakStartTimestamp || !$breakEndTimestamp) {
                for ($inicio = $horaInicioTimestamp; $inicio < $horaFinTimestamp; $inicio += ($paso * 60)) {
                    $fin = $inicio + ($paso * 60);

                    // $finFormateado = date('H:i', $fin);
                    // $horaFinTimestampFormateado = date('H:i', $horaFinTimestamp);
                    // Evitar que el rango supere la hora de cierre
                    if ($fin > $horaFinTimestamp) {
                        break;
                    }

                    // Verificar si el servicio supera la hora de cierre
                    if ($duracion > 60 && $inicio > ($horaFinTimestamp - $duracion * 60)) {
                        // Si el servicio supera el cierre, no lo agregamos
                        break;
                    }

                    $rangos[] = date('H:i', $inicio) . ' - ' . date('H:i', $fin);
                }
            } else {
                // Iteramos para generar los rangos antes del descanso
                for ($inicio = $horaInicioTimestamp; $inicio < $breakStartTimestamp; $inicio += ($paso * 60)) {
                    $fin = $inicio + ($paso * 60);

                    // $inicioFormateado = date('H:i', $inicio);
                    // $finFormateado = date('H:i', $fin);
                    // $breakStartTimestampFormateado = date('H:i', $breakStartTimestamp);
                    // $horaFinTimestampFormateado = date('H:i', $horaFinTimestamp);

                    if ($fin > $breakStartTimestamp || $fin > $horaFinTimestamp) {
                        break;
                    }

                    $rangos[] = date('H:i', $inicio) . ' - ' . date('H:i', $fin);
                }

                // Iteramos para generar los rangos después del descanso
                for ($inicio = $breakEndTimestamp; $inicio < $horaFinTimestamp; $inicio += ($paso * 60)) {
                    $fin = $inicio + ($paso * 60);

                    // $finFormateado = date('H:i', $fin);
                    // $horaFinTimestampFormateado = date('H:i', $horaFinTimestamp);
                    if ($fin > $horaFinTimestamp) {
                        break;
                    }

                    // Verificar si el servicio supera la hora de cierre
                    if ($duracion > 60 && $inicio > ($horaFinTimestamp - $duracion * 60)) {
                        // Si el servicio supera el cierre, no lo agregamos
                        break;
                    }

                    $rangos[] = date('H:i', $inicio) . ' - ' . date('H:i', $fin);
                }
            }
        }

        return $rangos;
    }
}
