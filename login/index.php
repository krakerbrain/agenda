<?php
require_once dirname(__DIR__) . '/classes/DatabaseSessionManager.php';
require_once dirname(__DIR__) . '/classes/ConfigUrl.php';
require_once dirname(__DIR__) . '/access-token/seguridad/jwt.php';

$baseUrl = ConfigUrl::get();
$manager = new DatabaseSessionManager();
$conn = $manager->getDB();
$title = "Login";

$error = "";
$creado = isset($_REQUEST['creado']) ? $_REQUEST['creado'] : "";
$cambio_clave = isset($_REQUEST['cambio_clave']) ? $_REQUEST['cambio_clave'] : "";

if (isset($_POST['usuario']) && isset($_POST['contrasenia'])) {
    $pass = $_POST['contrasenia'];
    $usuario = $_POST['usuario'];

    if (empty($pass) || empty($usuario)) {
        $error = "Debe llenar todos los campos.";
    } else {
        // Validación del correo electrónico
        if (!preg_match('/^[\w.%+-]+@[A-Za-z0-9.-]+\.[A-Z]{2,}$/i', $usuario)) {
            $error = "Formato de correo incorrecto.";
            exit;
        } else {
            try {
                $query = $conn->prepare("SELECT name, password, company_id, role_id, token_sha256 FROM users WHERE email = :usuario LIMIT 1");
                $query->bindParam(':usuario', $usuario);
                $query->execute();
                $datos = $query->fetch(PDO::FETCH_ASSOC);

                if ($datos) {
                    // Generar el token para verificación
                    $tokenVerificacion = hash('sha256', $datos['name'] . $usuario);
                    if (hash_equals($datos['token_sha256'], $tokenVerificacion)) {
                        if (password_verify($pass, $datos['password'])) {
                            if ($datos['role_id'] === 1) {
                                generarTokenSuperUser();
                                header("Location: " . $baseUrl . 'master_admin/admin.php');
                            } else {
                                generarTokenYConfigurarCookie($datos['company_id']);
                                header("Location: " . $baseUrl . $_ENV['URL_LOGIN']);
                            }
                            exit;
                        } else {
                            $error = "Credenciales incorrectas.";
                        }
                    } else {
                        $error = "Token de verificación incorrecto.";
                    }
                } else {
                    $error = "Usuario no existe.";
                }
            } catch (PDOException $e) {
                $error = "Error de conexión: " . $e->getMessage();
            }
        }
    }
}
include '../partials/head.php';
?>

<!-- Código HTML -->
<style>
    .login-container {
        background: rgba(255, 255, 255, 0.2);
        /* Color de fondo blanco con opacidad */
        border-radius: 15px;
        /* Bordes redondeados */

        backdrop-filter: blur(10px);
        /* Desenfoque de fondo */
        -webkit-backdrop-filter: blur(10px);
        /* Desenfoque de fondo para Safari */
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        /* Sombra sutil */
        color: #f5f5f5;
        /* Color del texto */
    }
</style>

<body style="background-color: #1a1728;" class="d-flex justify-content-center align-items-center vh-100">
    <div class="login-container p-5 ">
        <div class="justify-content-center">
            <form action="" method="post" class="form-group">
                <div class="text-center">
                    <h4 class="display-6">LOGIN AGENDA ROAD</h4>
                </div>
                <?php if ($creado == "true") { ?>
                    <div class="text-center mt-3 mb-3">
                        <span class="text-success fw-semibold">¡Se ha registrado correctamente!</span><br>
                        <small>Por favor ingrese al sistema.</small>
                    </div>
                <?php } else if ($cambio_clave == "true") { ?>
                    <div class="text-center mt-3 mb-3">
                        <span class="text-success fw-semibold">¡El cambio de clave ha sido exitoso!</span><br>
                        <small>Por favor ingrese al sistema.</small>
                    </div>
                <?php } ?>

                <!-- Inputs del formulario -->
                <div class="input-group">
                    <div class="input-group-text">
                        <i class="fa-solid fa-user text-secondary"></i>
                    </div>
                    <input type="email" name="usuario" id="usuario" class="form-control" placeholder="Ingrese correo"
                        autocomplete="email" required>
                </div>
                <div class="input-group mt-3">
                    <div class="input-group-text">
                        <i class="fa-solid fa-key text-secondary"></i>
                    </div>
                    <input type="password" name="contrasenia" id="contrasenia" class="form-control"
                        placeholder="Ingrese su contraseña" autocomplete="current-password" required>
                    <div class="input-group-text bg-light">
                        <a href="#" class="text-secondary pe-auto">
                            <i class="fa-solid fa-eye" onclick="verpass()"></i>
                        </a>
                    </div>
                </div>
                <div class="form-group mt-3">
                    <input type="submit" value="Ingresar" class="btn btn-info w-100">
                </div>

                <!-- Mostrar mensaje de error -->
                <?php if (!empty($error)) { ?>
                    <span class="d-flex justify-content-center mt-1 text-danger"><?= $error ?></span>
                <?php } ?>
            </form>
        </div>
    </div>

    <script>
        function verpass() {
            var pass = document.getElementById('contrasenia');
            pass.type = pass.type == "password" ? "text" : "password";
        }
    </script>
</body>