<?php
$title = "Login";
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
    <div class="login-container p-5">
        <div class="justify-content-center">
            <form id="loginForm" class="form-group">
                <div class="text-center">
                    <h4 class="display-6">LOGIN AGENDA ROAD</h4>
                </div>
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
                <div id="error-message" class="d-flex justify-content-center mt-1 text-danger"></div>
            </form>
        </div>
    </div>

    <script>
    const baseUrl = '<?= $baseUrl ?>';
    document.getElementById('loginForm').addEventListener('submit', async function(event) {
        event.preventDefault(); // Evitar el envío tradicional del formulario

        // Capturar los datos del formulario
        const formData = new FormData(this);

        try {
            const response = await fetch('controllers/login_controller.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                // Redirigir si la respuesta es exitosa
                window.location.href = `${baseUrl}${result.redirect}`;
            } else {
                // Mostrar el mensaje de error
                document.getElementById('error-message').innerText = result.message;
            }
        } catch (error) {
            document.getElementById('error-message').innerText = "Error en la conexión.";
        }
    });

    function verpass() {
        var pass = document.getElementById('contrasenia');
        pass.type = pass.type === "password" ? "text" : "password";
    }
    </script>
</body>