<?php
require_once dirname(__DIR__) . '/classes/DatabaseSessionManager.php';
require_once dirname(__DIR__) . '/classes/ConfigUrl.php';
require_once dirname(__DIR__) . '/access-token/seguridad/jwt.php';
$baseUrl = ConfigUrl::get();
$manager = new DatabaseSessionManager();
$conn = $manager->getDB();
$title = "Login";

$error = "false";
$creado = isset($_REQUEST['creado']) ? $_REQUEST['creado'] : "";
$cambio_clave = isset($_REQUEST['cambio_clave']) ? $_REQUEST['cambio_clave'] : "";

if (isset($_POST['usuario']) && isset($_POST['contrasenia'])) {
    $pass = $_POST['contrasenia'];
    $usuario = $_POST['usuario'];
    if ($pass != "" && $usuario != "") {
        try {
            $query = $conn->prepare("SELECT password, company_id FROM users WHERE email = :usuario LIMIT 1");
            $query->bindParam(':usuario', $usuario);
            $query->execute();
            $datos = $query->fetch(PDO::FETCH_ASSOC);

            if ($datos) {
                if (password_verify($pass, $datos['password'])) {
                    generarTokenYConfigurarCookie($datos['company_id']);
                    header("Location: " . $baseUrl . $_ENV['URL_LOGIN']);
                    exit;
                } else {
                    $error = "true";
                }
            } else {
                $error = "noexiste";
            }
        } catch (PDOException $e) {
            echo "Error de conexión: " . $e->getMessage();
        }
    }
}

include '../partials/head.php';
?>
<!-- correo pruebas: user1@gmail.com -->

<body style="background-color: #D4D4D4;" class="d-flex justify-content-center align-items-center vh-100">
    <div class="bg-light p-5 rounded">
        <div class="justify-content-center">
            <div class="">
                <form action="" method="post" class="form-group">
                    <div class="text-center">
                        <h4 class="display-6">LOGIN AGENDA ROAD</h4>
                    </div>
                    <div class="form-group text-center mt-3">
                        <div class="mb-3">
                            <?php if ($creado == "true") { ?>
                                <span class="text-success fw-semibold">¡Se ha registrado correctamente!</span><br>
                                <small>Por favor ingrese al sistema.</small>
                            <?php } else if ($cambio_clave == "true") { ?>
                                <span class="text-success fw-semibold">¡El cambio de clave ha sido exitoso!</span><br>
                                <small>Por favor ingrese al sistema.</small>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="input-group">
                        <div class="input-group-text">
                            <i class="fa-solid fa-user text-secondary"></i>
                        </div>
                        <input type="mail" name="usuario" id="usuario" class="form-control" placeholder="Ingrese correo"
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
                    <?php if ($error == "true") { ?>
                        <span class=" d-flex justify-content-center mt-1">Credenciales incoorrectas.</span>
                    <?php } else if ($error == "vacio") { ?>
                        <span class=" d-flex justify-content-center mt-1">Debe llenar todos los campos.</span>
                    <?php } else if ($error == "noexiste") { ?>
                        <span class=" d-flex justify-content-center mt-1">Usuario No Existe.</span>
                    <?php } ?>
                </form>
                <div class="d-flex gap-1 justify-content-center mt-1">
                    <div style="margin-right:5px">¿No tiene una cuenta?</div>
                    <a href="registro.php" class="text-info text-decoration-none fw-semibold">Registrese</a>
                </div>
                <!-- <a href="recupera.php" class="text-decoration-none">
                    <p class="text-center text-success">¿Olvidó su contraseña?</p>
                </a> -->
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
                    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
                    crossorigin="anonymous">
                </script>
                <script>
                    function verpass() {
                        var pass = document.getElementById('contrasenia');
                        pass.type = pass.type == "password" ? "text" : "password"
                    }
                </script>