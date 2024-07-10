<?php
require_once dirname(__DIR__) . '/classes/DatabaseSessionManager.php';
require_once dirname(__DIR__) . '/classes/ConfigUrl.php';
$baseUrl = ConfigUrl::get();
$manager = new DatabaseSessionManager();
$manager->startSession();
$conn = $manager->getDB();

$sesion = isset($_SESSION['company_id']);
if (!$sesion) {
    header("Location: " . $baseUrl . "login/index.php");
}
$sql = $conn->prepare("SELECT a.*, s.name AS service FROM appointments a 
                        inner join services s 
                        on a.id_service = s.id
                        WHERE a.company_id = $_SESSION[company_id]
                        AND status != 2
                        ORDER BY date DESC");
$sql->execute();
$result = $sql->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Reservas</title>
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>assets/css/form.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        table {
            font-size: small;
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid black;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .confirm-button {
            font-size: 1.3em;
            color: #4CAF50;
            padding-right: 10px;
            cursor: pointer;
        }

        .delete-button {
            font-size: 1.3em;
            color: #af4c4c;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <h2>Administración de Reservas</h2>
        <table>
            <thead>
                <tr>
                    <th>Servicio</th>
                    <th>Nombre</th>
                    <th>Teléfono</th>
                    <th>Correo</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Estado</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($result as $row) : ?>
                    <tr>
                        <td><?php echo $row['service']; ?></td>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['phone']; ?></td>
                        <td><?php echo $row['mail']; ?></td>
                        <td><?php echo $row['date']; ?></td>
                        <td><?php echo $row['start_time']; ?></td>
                        <td><?php echo $row['status'] ? 'Confirmada' : 'Pendiente'; ?></td>
                        <td style="display: flex; justify-content: space-around;">
                            <?php if (!$row['status']) : ?>
                                <i class="fas fa-check confirm-button" onclick="confirmReservation(<?php echo $row['id']; ?>)" title="Confirmar reserva"></i>
                            <?php endif; ?>
                            <i class="fas fa-times delete-button" title="Eliminar reserva" onclick="deleteAppointment('<?php echo $row['event_id']; ?>', <?php echo $row['id']; ?>)"></i>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>