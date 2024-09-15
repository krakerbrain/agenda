<?php
require_once dirname(__DIR__, 2) . '/classes/DatabaseSessionManager.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';
require_once dirname(__DIR__, 2) . '/access-token/seguridad/jwt.php';
$baseUrl = ConfigUrl::get();
$manager = new DatabaseSessionManager();
$conn = $manager->getDB();
$datosUsuario = validarToken();
if (!$datosUsuario) {
    header("Location: " . $baseUrl . "login/index.php");
}
$company_id = $datosUsuario['company_id'];
$query = $conn->prepare("SELECT id, type, about_role FROM user_role WHERE id > 2");
$query->execute();
$roles = $query->fetchAll(PDO::FETCH_ASSOC);

?>
<div class="col-lg-6 mx-auto vh-100">
    <div class="text-end">
        <a tabindex="0" role="button" data-bs-trigger="focus" class="btn" data-bs-toggle="popover"
            data-bs-title="Agregar usuario"
            data-bs-content="Esta opción permite agregar un nuevo usuario que podrá usar la agenda ya sea como rol de administrador o rol de usuario que solo podría ver, confirmar y eliminar citas">
            <i class="fa fa-circle-question text-primary" style="font-size: 1.5rem;"></i>
        </a>
    </div>
    <div class="bg-light p-5 rounded ">
        <div class="justify-content-center">
            <form action="" method="post" class="form-group" id="addUserForm">
                <div class="alert alert-secondary error d-none mb-2" role="alert"></div>
                <input type="hidden" name="company_id" id="company_id" value="<?= $company_id; ?>">
                <div class="input-group">
                    <div class="input-group-text bg-secondary text-light">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <input type="text" id="username" name="usuario" class="form-control"
                        placeholder="Ingrese un nombre de usuario">
                </div>
                <div class=" input-group mt-2">
                    <div class="input-group-text bg-secondary text-light">
                        <i class="fa-solid fa-envelope"></i>
                    </div>
                    <input type="mail" name="correo" id="correo" class="form-control" placeholder="Ingrese un correo">
                </div>
                <div class="input-group mt-2">
                    <div class="input-group-text bg-secondary text-light">
                        <i class="fa-solid fa-key"></i>
                    </div>
                    <input type="password" name="password" id="password" class="form-control"
                        placeholder="Ingrese una clave">
                    <div class="input-group-text bg-light" id="seePass">
                        <a href="#" class="pe-auto text-secondary">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                    </div>
                </div>
                <div class="input-group mt-2">
                    <div class="input-group-text bg-secondary text-light">
                        <i class="fa-solid fa-key"></i>
                    </div>
                    <input type="password" name="password2" id="password2" class="form-control"
                        placeholder="Ingrese otra vez">
                    <div class="input-group-text bg-light" id="seeConfirm">
                        <a href="#" class="pe-auto text-secondary">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                    </div>
                </div>
                <div class="input-group mt-2">
                    <div class="input-group-text bg-secondary text-light">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <select id="role_id" name="role_id" class="form-select">
                        <option selected>Seleccione rol del usuario</option>
                        <?php foreach ($roles as $role) { ?>
                            <option value="<?php echo $role['id']; ?>"
                                data-about="<?php echo htmlspecialchars($role['about_role']); ?>">
                                <?php echo $role['type']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div id="roleAbout" class="mb-3 d-none">
                    <span id="roleAboutText" class="form-control-plaintext bg-secondary-subtle p-2"></span>
                </div>
                <div class="form-group mt-3">
                    <button type="submit" class="btn btn-secondary w-100" id="addUser">
                        <span class="spinner-border spinner-border-sm d-none" aria-hidden="true"></span>
                        <span class="button-text">Agregar Usuario</span>
                    </button>
                </div>

            </form>
        </div>
    </div>
    <div class="container mt-4">
        <table class="table">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Correo</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="usersTable">

            </tbody>
        </table>
    </div>

</div>