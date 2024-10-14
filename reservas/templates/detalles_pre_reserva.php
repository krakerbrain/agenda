<?php if ($preReserva) : ?>
<ul class=" details-page" style="max-width: 600px; margin: 0 auto; color: #525252">
    <h4 class="text-center mb-4"><?php echo htmlspecialchars($preReserva['title']); ?></h4>
    <li class="mb-2"><strong>Nombre:</strong> <?php echo htmlspecialchars($preReserva['nombre']); ?></li>
    <li class="mb-2"><strong>Servicio:</strong> <?php echo htmlspecialchars($preReserva['servicio']); ?>
    </li>
    <li class="mb-2"><strong>Fecha:</strong> <?php echo htmlspecialchars($preReserva['fecha']); ?></li>
    <li class="mb-2"><strong>Hora:</strong> <?php echo htmlspecialchars($preReserva['hora']); ?></li>
    <li class="mb-2"><strong>Notas:</strong>
        <ul>
            <?php if (!empty($preReserva['notas']) && is_array($preReserva['notas'])) : ?>
            <?php foreach ($preReserva['notas'] as $nota) : ?>
            <li class="mb-2"><?php echo htmlspecialchars($nota); ?></li>
            <?php endforeach; ?>
            <?php else : ?>
            <li>No hay notas disponibles.</li>
            <?php endif; ?>
        </ul>
    </li>
    <li class="mb-2"><strong>Estado de reserva:</strong>
        <?php echo htmlspecialchars($preReserva['estado']); ?></li>
</ul>
<?php else : ?>
<div class="alert alert-warning" role="alert">
    No se encontraron detalles para la pre-reserva.
</div>
<?php endif; ?>