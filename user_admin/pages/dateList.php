<?php
require_once dirname(__DIR__, 2) . '/classes/DatabaseSessionManager.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';
require_once dirname(__DIR__, 2) . '/access-token/seguridad/jwt.php';
$baseUrl = ConfigUrl::get();
$manager = new DatabaseSessionManager();
// $manager->startSession();
// session_start();

// $sesion = isset($_SESSION['company_id']);
$datosUsuario = validarToken();
// $_SESSION['companyID'] = $_SESSION['company_id'];
if (!$datosUsuario) {
    header("Location: " . $baseUrl . "login/index.php");
}
// Usar $datosUsuario['company_id'] donde sea necesario
$company_id = $datosUsuario['company_id'];
$conn = $manager->getDB();
$sql = $conn->prepare("SELECT a.*, s.name AS service FROM appointments a 
                        inner join services s 
                        on a.id_service = s.id
                        WHERE a.company_id = :company_id
                        AND status != 2
                        ORDER BY date DESC");
$sql->bindParam(':company_id', $company_id);
$sql->execute();
$result = $sql->fetchAll(PDO::FETCH_ASSOC);

?>


<div class="container mt-4">
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="">
                <tr>
                    <th scope="col">Servicio</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Teléfono</th>
                    <th scope="col">Correo</th>
                    <th scope="col">Fecha</th>
                    <th scope="col">Hora</th>
                    <th scope="col">Estado</th>
                    <th scope="col">Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($result as $row) : ?>
                <tr>
                    <td data-cell="servicio" class="data"><?php echo htmlspecialchars($row['service']); ?></td>
                    <td data-cell="nombre" class="data"><?php echo htmlspecialchars($row['name']); ?></td>
                    <td data-cell="telefono" class="data"><?php echo htmlspecialchars($row['phone']); ?></td>
                    <td data-cell="correo" class="data"><?php echo htmlspecialchars($row['mail']); ?></td>
                    <td data-cell="fecha" class="data"><?php echo htmlspecialchars($row['date']); ?></td>
                    <td data-cell="hora" class="data"><?php echo htmlspecialchars($row['start_time']); ?></td>
                    <td data-cell="estado" class="data"><?php echo $row['status'] ? 'Confirmada' : 'Pendiente'; ?></td>
                    <td class="d-flex justify-content-around">
                        <?php if (!$row['status']) : ?>
                        <button id="confirmarBtn<?php echo htmlspecialchars($row['id']); ?>"
                            class="btn btn-success btn-sm confirm" title="Confirmar reserva"
                            data-id="<?php echo htmlspecialchars($row['id']); ?>">
                            <i class="fas fa-check"></i>
                            <span class="spinner-border spinner-border-sm d-none" aria-hidden="true"></span>
                            <span class="button-text"></span>
                        </button>
                        <?php endif; ?>
                        <button id="eliminarBtn<?php echo htmlspecialchars($row['id']); ?>"
                            class="btn btn-danger btn-sm eliminarReserva" title="Eliminar reserva"
                            data-id="<?php echo htmlspecialchars($row['id']); ?>"
                            data-eventid="<?php echo htmlspecialchars($row['event_id']); ?>">
                            <i class="fas fa-times"></i>
                            <span class="spinner-border spinner-border-sm d-none" aria-hidden="true"></span>
                            <span class="button-text"></span>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>