<?php
require_once dirname(__DIR__, 2) . '/configs/init.php';
require_once dirname(__DIR__, 2) . '/access-token/seguridad/JWTAuth.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';
$baseUrl = ConfigUrl::get();
$auth = new JWTAuth();
$auth->validarTokenUsuario();

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
                <input type="hidden" id="user_id" name="user_id" value="">
                <!-- Campo para la foto de perfil -->
                <div class="mb-3 text-center">
                    <div class="d-flex justify-content-center mb-2">
                        <img id="profilePreview" src="<?= $baseUrl ?>assets/img/empty_user.png" class="rounded-circle"
                            style="width: 100px; height: 100px; object-fit: cover;" alt="Foto de perfil">
                    </div>
                    <div class="input-group">
                        <input type="file" name="profile_picture" id="profile_picture" class="form-control"
                            accept="image/*">
                        <label class="input-group-text bg-secondary text-light" for="profile_picture">
                            <i class="fa-solid fa-camera"></i>
                        </label>
                    </div>
                    <small class="text-muted">Formatos aceptados: JPG, PNG. Tamaño máximo: 2MB</small>
                </div>
                <!-- Campo para el nombre de usuario -->
                <div class="input-group">
                    <div class="input-group-text bg-secondary text-light">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <input type="text" id="username" name="usuario" class="form-control"
                        placeholder="Ingrese un nombre de usuario">
                </div>
                <!-- Campo para la descripción del usuario -->
                <div class="input-group mt-2">
                    <div class="input-group-text bg-secondary text-light">
                        <i class="fa-solid fa-file-lines"></i>
                    </div>
                    <textarea name="descripcion" id="descripcion" class="form-control"
                        placeholder="Descripción del usuario (opcional)" rows="2"></textarea>
                </div>
                <!-- Campo para el correo electrónico -->
                <div class=" input-group mt-2">
                    <div class="input-group-text bg-secondary text-light">
                        <i class="fa-solid fa-envelope"></i>
                    </div>
                    <input type="mail" name="correo" id="correo" class="form-control" placeholder="Ingrese un correo"
                        autocomplete="nope">
                </div>
                <!-- Campo para la clave -->
                <div class="input-group mt-2" id="passwordGroup">
                    <div class="input-group-text bg-secondary text-light">
                        <i class="fa-solid fa-key"></i>
                    </div>
                    <input type="password" name="password" id="password" class="form-control"
                        placeholder="Ingrese una clave" autocomplete="new-password">
                    <div class="input-group-text bg-light" id="seePass">
                        <a href="#" class="pe-auto text-secondary">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                    </div>
                </div>
                <!-- Campo para confirmar la clave -->
                <div class="input-group mt-2" id="confirmPasswordGroup">
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
                <div class="input-group mt-2" id="roleGroup">
                    <div class="input-group-text bg-secondary text-light">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <select id="role_id" name="role_id" class="form-select">

                    </select>
                </div>
                <div id="roleAbout" class="mb-3 d-none">
                    <span id="roleAboutText" class="form-control-plaintext bg-secondary-subtle p-2"></span>
                </div>
                <div class="form-group mt-3">
                    <button type="submit" class="btn btn-secondary w-100" id="addUser">
                        <span class="spinner-border spinner-border-sm d-none" aria-hidden="true"></span>
                        <span class="button-text-spinner">Agregar Usuario</span>
                    </button>
                    <button type="button" class="btn btn-outline-secondary w-100 mt-2 d-none" id="cancelEdit">
                        Cancelar Edición
                    </button>
                </div>

            </form>
        </div>
    </div>
    <div class="container mt-4">
        <div class="row" id="usersContainer">
            <!-- Las cards se generarán dinámicamente aquí -->
        </div>
    </div>

</div>
<?php include dirname(__DIR__, 2) . '/includes/modal-info.php';
?>