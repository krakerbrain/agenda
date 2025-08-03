<?php
require_once dirname(__DIR__, 2) . '/configs/init.php';
require_once dirname(__DIR__, 2) . '/access-token/seguridad/JWTAuth.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';
$baseUrl = ConfigUrl::get();
$auth = new JWTAuth();
$auth->validarTokenUsuario();
?>

<div class="max-w-xl mx-auto min-h-screen flex flex-col justify-center">
    <!-- Botón info -->
    <div class="text-end mb-4">
        <button tabindex="0" role="button" class="text-blue-600 hover:text-blue-800 focus:outline-none"
            data-popover-target="popover-usuario">
            <i class="fa fa-circle-question text-xl"></i>
        </button>
    </div>

    <!-- Formulario -->
    <div class="bg-white p-6 rounded-lg shadow-md">
        <form action="" method="post" id="addUserForm" enctype="multipart/form-data" class="space-y-4">
            <input type="hidden" id="user_id" name="user_id" value="">

            <!-- Imagen de perfil -->
            <div class="text-center">
                <div class="flex justify-center mb-2">
                    <img id="profilePreview" src="<?= $baseUrl ?>assets/img/empty_user.png"
                        class="w-25 rounded-full object-cover" alt="Foto de perfil">
                </div>
                <div class="flex flex-col items-center">
                    <label for="profile_picture"
                        class="cursor-pointer bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-800 transition duration-200 flex items-center gap-2">
                        <i class="fa-solid fa-camera"></i>
                        <span>Subir Foto</span>
                    </label>
                    <input type="file" name="profile_picture" id="profile_picture" accept="image/*" class="hidden">
                    <p class="text-sm text-gray-500 mt-2">Formatos aceptados: JPG, PNG. Tamaño máximo: 2MB</p>
                </div>
            </div>

            <!-- Usuario -->
            <div class="flex items-center border rounded px-3 py-2">
                <i class="fa-solid fa-user text-gray-500 mr-2"></i>
                <input type="text" name="usuario" id="username" class="w-full focus:outline-none"
                    placeholder="Ingrese un nombre de usuario">
            </div>

            <!-- Descripción -->
            <div class="flex items-start border rounded px-3 py-2">
                <i class="fa-solid fa-file-lines text-gray-500 mt-1 mr-2"></i>
                <textarea name="descripcion" id="descripcion" class="w-full focus:outline-none resize-none" rows="2"
                    placeholder="Descripción del usuario (opcional)"></textarea>
            </div>

            <!-- Correo -->
            <div class="flex items-center border rounded px-3 py-2">
                <i class="fa-solid fa-envelope text-gray-500 mr-2"></i>
                <input type="email" name="correo" id="correo" class="w-full focus:outline-none"
                    placeholder="Ingrese un correo" autocomplete="nope">
            </div>

            <!-- Contraseña -->
            <div class="flex items-center border rounded px-3 py-2 passwordGroup">
                <i class="fa-solid fa-key text-gray-500 mr-2"></i>
                <input type="password" name="password" id="password" class="w-full focus:outline-none"
                    placeholder="Ingrese una clave" autocomplete="new-password">
                <button type="button" id="seePass" class="ml-2 text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-eye"></i>
                </button>
            </div>

            <!-- Confirmar contraseña -->
            <div class="flex items-center border rounded px-3 py-2 passwordConfirmGroup">
                <i class="fa-solid fa-key text-gray-500 mr-2"></i>
                <input type="password" name="password2" id="password2" class="w-full focus:outline-none"
                    placeholder="Ingrese otra vez">
                <button type="button" id="seeConfirm" class="ml-2 text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-eye"></i>
                </button>
            </div>

            <!-- Rol -->
            <div class="flex items-center border rounded px-3 py-2" id="roleGroup">
                <i class="fa-solid fa-user text-gray-500 mr-2"></i>
                <select id="role_id" name="role_id" class="w-full focus:outline-none bg-transparent">
                    <!-- Opciones dinámicas -->
                </select>
            </div>

            <!-- Info sobre el rol -->
            <div id="roleAbout" class="hidden mt-1">
                <p id="roleAboutText" class="text-sm bg-gray-100 p-2 rounded text-gray-700"></p>
            </div>

            <!-- Botones -->
            <div class="space-y-2">
                <button type="submit" id="addUser"
                    class="w-full bg-gray-500 hover:bg-gray-800 text-white py-2 rounded transition flex justify-center items-center gap-2">
                    <span
                        class="spinner-border hidden w-4 h-4 border-2 border-t-white rounded-full animate-spin"></span>
                    <span class="button-text-spinner">Agregar Usuario</span>
                </button>
                <button type="button" id="cancelEdit"
                    class="w-full border border-gray-400 text-gray-700 py-2 rounded hidden">
                    Cancelar Edición
                </button>
            </div>
        </form>
    </div>

    <!-- Contenedor de usuarios -->
    <div class="mt-6">
        <div class="grid grid-cols-1 gap-4" id="usersContainer">
            <!-- Cards de usuarios dinámicas -->
        </div>
    </div>
</div>

<?php include dirname(__DIR__, 2) . '/includes/modal-info.php'; ?>