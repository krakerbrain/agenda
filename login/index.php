<?php
require_once dirname(__DIR__) . '/configs/init.php';
require_once dirname(__DIR__) . '/classes/ConfigUrl.php';
$baseUrl = ConfigUrl::get();
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
                    <h4 class="display-6">LOGIN AGENDARIUM</h4>
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

                <!-- Dentro del formulario de login, después del botón de Ingresar -->
                <div class="form-group mt-3 text-center">
                    <a href="#" class="text-info" id="forgot-password-link">¿Olvidaste tu contraseña?</a>
                </div>



                <!-- Mostrar mensaje de error -->
                <div id="error-message" class="d-flex justify-content-center mt-1 text-danger"></div>
            </form>
        </div>
    </div>
    <!-- Modal para recuperación de contraseña -->
    <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content login-container">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Recuperar Contraseña</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="forgotPasswordForm" novalidate>
                        <div class="mb-3">
                            <label for="recovery-email" class="form-label">Ingresa tu correo electrónico</label>
                            <input type="email" class="form-control" id="recovery-email" name="email" required
                                pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
                            <div class="invalid-feedback">Por favor ingresa un correo válido</div>
                        </div>
                        <button type="submit" class="btn btn-info w-100" id="recovery-submit">
                            <span class="spinner-border spinner-border-sm d-none" id="recovery-spinner"></span>
                            <span id="recovery-text">Enviar enlace</span>
                        </button>
                    </form>
                    <div id="recovery-message" class="mt-3 text-center"></div>
                </div>
            </div>
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

        // Agrega esto al script de tu login.html
        document.getElementById('forgot-password-link').addEventListener('click', function(e) {
            e.preventDefault();
            // Asumiendo que estás usando Bootstrap 5
            const modal = new bootstrap.Modal(document.getElementById('forgotPasswordModal'));
            modal.show();
        });

        document.getElementById('forgotPasswordForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const submitBtn = document.getElementById('recovery-submit');
            const spinner = document.getElementById('recovery-spinner');
            const btnText = document.getElementById('recovery-text');

            // Mostrar spinner y deshabilitar botón
            spinner.classList.remove('d-none');
            btnText.textContent = 'Enviando...';
            submitBtn.disabled = true;
            const email = document.getElementById('recovery-email').value;

            try {
                const response = await fetch(`${baseUrl}login/controllers/password_recovery_controller.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `email=${encodeURIComponent(email)}`
                });
                const result = await response.json();

                const messageElement = document.getElementById('recovery-message');
                messageElement.innerText = result.message;
                messageElement.className = result.success ? 'mt-3 text-center text-success' :
                    'mt-3 text-center text-info';

                if (result.success) {
                    setTimeout(() => {
                        bootstrap.Modal.getInstance(document.getElementById('forgotPasswordModal'))
                            .hide();
                    }, 3000);
                }
            } catch (error) {
                document.getElementById('recovery-message').innerText = "Error en la conexión.";
            } finally {
                // Siempre restaurar el estado del botón
                spinner.classList.add('d-none');
                btnText.textContent = 'Enviar enlace';
                submitBtn.disabled = false;
            }
        });
    </script>
    <script src="<?php echo $baseUrl; ?>assets/vendors/js/bootstrap/bootstrap.bundle.min.js"></script>
    </script>
</body>

</html>