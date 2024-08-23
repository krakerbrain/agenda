<?php
require_once dirname(__DIR__) . '/classes/ConfigUrl.php';
require_once dirname(__DIR__) . '/access-token/seguridad/jwt.php';
require_once dirname(__DIR__) . '/classes/DatabaseSessionManager.php';
$baseUrl = ConfigUrl::get();
$manager = new DatabaseSessionManager();
$datosUsuario = validarTokenSuperUser();
if (!$datosUsuario) {
    header("Location: " . $baseUrl . "login/index.php");
}

$conn = $manager->getDB();

$sql = $conn->prepare("SELECT id, name,logo, is_active, token FROM companies");
$sql->execute();
$result = $sql->fetchAll(PDO::FETCH_ASSOC);

$title = "Lista de Empresas";

include dirname(__DIR__) . '/partials/head.php';
include dirname(__DIR__) . '/master_admin/navbar.php';
?>


<div class="container mt-4">
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="">
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Estado</th>
                    <th scope="col">Logo</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">URL</th>
                    <th scope="col">Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($result as $row) : ?>

                <tr>
                    <td data-cell="id" class="data"><?php echo htmlspecialchars($row['id']); ?></td>
                    <td data-cell="Habilitado" class="data">
                        <div class="form-check form-switch">
                            <input type="checkbox" class="form-check-input"
                                <?php echo $row['is_active'] ? 'checked' : '' ?>>
                        </div>
                    </td>
                    <td data-cell="logo" class="data"><img class="" src="<?php echo $baseUrl . $row['logo'] ?>"
                            alt="Logo" style="width:70px"></td>
                    <td data-cell="nombre" class="data"><?php echo htmlspecialchars($row['name']); ?></td>
                    <td data-cell="url" class="data"><a href="<?php echo $baseUrl . $row['token'] ?>" target="blank">URL
                            FORM</a></td>
                    <td data-cell="accion">
                        <button id="eliminarBtn<?php echo htmlspecialchars($row['id']); ?>"
                            class="btn btn-danger btn-sm eliminarReserva" title="Eliminar reserva"
                            data-id="<?php echo htmlspecialchars($row['id']); ?>"><i class="fas fa-times"></i>
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