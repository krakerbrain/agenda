<?php
require_once dirname(__DIR__) . '/configs/init.php';
require_once dirname(__DIR__) . '/classes/ConfigUrl.php';
$baseUrl = ConfigUrl::get();
$title = "Login";
include '../partials/head-login.php';
?>
<!-- Código HTML -->
<style>
    @font-face {
        font-family: 'CarrigPro-Regular';
        src: url("<?php echo $baseUrl; ?>assets/fonts/CarrigPro-Regular.woff2") format('woff2');
        font-display: swap;
    }

    :root {

        font-family: 'CarrigPro-Regular', sans-serif;
    }

    body {
        font-family: 'CarrigPro-Regular', sans-serif;

    }

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

<body class="bg-[#1a1728] min-h-screen">
    <header class="w-full bg-white/80 shadow-sm">
        <nav class="container mx-auto flex items-center min-h-16 py-2">
            <a href="<?php echo $baseUrl; ?>" class="flex items-center gap-3 no-underline ms-3">
                <img src="<?php echo $baseUrl; ?>assets/img/landing/logo/Isotipo-Agendarium.svg" alt="Logo Agendarium"
                    class="h-10 w-auto" />
                <div class="agendarium-logo-text">
                    <span class="font-semibold text-gray-900 text-lg leading-tight">Agendarium</span>
                    <p class="mb-0 text-xs text-gray-500 leading-tight">Gestión de citas simplificada</p>
                </div>
            </a>
        </nav>
    </header>
    <div class="flex justify-center items-center min-h-[80vh] px-2">
        <div
            class="login-container relative p-6 rounded-2xl shadow-lg bg-white/20 backdrop-blur-md max-w-md w-full overflow-hidden">
            <form id="loginForm" class="space-y-4 transition-all duration-300 ease-in-out"
                style="transition-property: opacity, transform;">
                <div class="text-center">
                    <h4 class="text-xl font-bold text-cyan-500 mb-4">Iniciar sesión</h4>
                </div>
                <!-- Inputs del formulario -->
                <div class="flex items-center border rounded-md bg-white/80 w-full overflow-hidden">
                    <span class="px-3 text-gray-400">
                        <i class="fa-solid fa-envelope"></i>
                    </span>
                    <input type="email" name="usuario" id="usuario"
                        class="flex-1 py-2 px-2 outline-none text-gray-800 min-w-0 w-full" placeholder="Ingrese correo"
                        autocomplete="email" required
                        value="<?= isset($_GET['email']) ? htmlspecialchars($_GET['email']) : '' ?>" />

                </div>
                <div class="flex items-center border rounded-md bg-white/80 w-full overflow-hidden">
                    <span class="px-3 text-gray-400">
                        <i class="fa-solid fa-key"></i>
                    </span>
                    <input type="password" name="contrasenia" id="contrasenia"
                        class="flex-1 py-2 px-2  outline-none text-gray-800 min-w-0 w-full"
                        placeholder="Ingrese su contraseña" autocomplete="current-password" required />
                    <span class="px-3 cursor-pointer text-gray-400" onclick="verpass()">
                        <i class="fa-solid fa-eye"></i>
                    </span>
                </div>
                <button type="submit"
                    class="w-full bg-cyan-500 hover:bg-cyan-600 text-white font-semibold py-2 rounded-md transition">
                    Ingresar
                </button>
                <p class="text-sm text-center">
                    <a href="#" id="showRecovery" class="text-cyan-500 hover:underline">¿Olvidaste tu contraseña?</a>
                </p>
            </form>
            <!-- Password Recovery Form (hidden by default) -->
            <form id="recoveryForm"
                class="space-y-4 absolute inset-x-6 top-10 bottom-10 opacity-0 scale-95 pointer-events-none transition-all duration-300 ease-in-out"
                style="transition-property: opacity, transform;">
                <div class="text-center">
                    <h4 class="text-xl font-bold text-cyan-500 mb-4">Recuperar Contraseña</h4>
                </div>
                <div class="flex items-center border rounded-md bg-white/80">
                    <span class="px-3 text-gray-400">
                        <i class="fa-solid fa-envelope"></i>
                    </span>
                    <input type="email" id="recovery-email" name="email" required
                        placeholder="Ingresa tu correo electrónico"
                        class="w-full px-3 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-cyan-500" />
                </div>
                <p class="text-red-500 text-sm mt-1 hidden" id="invalid-feedback">Por favor ingresa un correo válido</p>

                <button type="submit" id="recovery-submit"
                    class="w-full bg-cyan-500 hover:bg-cyan-600 text-white font-semibold py-2 rounded-md transition">
                    <span class="animate-spin hidden" id="recovery-spinner">&#9696;</span>
                    <span id="recovery-text">Enviar enlace</span>
                </button>
                <p class="text-sm text-center">
                    <a href="#" id="showLogin" class="text-cyan-500 hover:underline">← Volver al login</a>
                </p>
            </form>
            <div id="error-message" class="text-center text-red-500 text-sm mt-2 hidden"></div>
            <div id="recovery-message" class="mt-2 text-center text-sm"></div>
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
                    document.getElementById('error-message').classList.remove('hidden');
                    setTimeout(() => {
                        document.getElementById('error-message').classList.add('hidden');
                    }, 3000); // Ocultar el mensaje después de 5 segundos
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

        const loginForm = document.getElementById('loginForm');
        const recoveryForm = document.getElementById('recoveryForm');

        document.getElementById('showRecovery').addEventListener('click', (e) => {
            e.preventDefault();
            // Ocultar login con transición
            loginForm.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
            loginForm.classList.remove('opacity-100', 'scale-100');
            // Mostrar recovery
            recoveryForm.classList.remove('opacity-0', 'scale-95', 'pointer-events-none');
            recoveryForm.classList.add('opacity-100', 'scale-100');
        });

        document.getElementById('showLogin').addEventListener('click', (e) => {
            e.preventDefault();
            // Ocultar recovery
            recoveryForm.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
            recoveryForm.classList.remove('opacity-100', 'scale-100');
            // Mostrar login
            loginForm.classList.remove('opacity-0', 'scale-95', 'pointer-events-none');
            loginForm.classList.add('opacity-100', 'scale-100');
        });

        // Manejo del envío del formulario de recuperación (opcional)
        document.getElementById('recoveryForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const submitBtn = document.getElementById('recovery-submit');
            const spinner = document.getElementById('recovery-spinner');
            const btnText = document.getElementById('recovery-text');
            const email = document.getElementById('recovery-email').value;
            const messageElement = document.getElementById('recovery-message');
            const invalidFeedback = document.getElementById('invalid-feedback');

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                invalidFeedback.classList.remove('hidden');
                return;
            } else {
                invalidFeedback.classList.add('hidden');
            }

            spinner.classList.remove('hidden');
            btnText.textContent = 'Enviando...';
            submitBtn.disabled = true;

            try {
                const response = await fetch(`${baseUrl}login/controllers/password_recovery_controller.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `email=${encodeURIComponent(email)}`
                });
                const result = await response.json();

                messageElement.innerText = result.message;
                messageElement.className =
                    `mt-3 text-center text-sm ${result.success ? 'text-green-600' : 'text-red-600'}`;

                if (result.success) {
                    setTimeout(() => {
                        document.getElementById('showLogin').click();
                    }, 3000);
                }
            } catch (error) {
                messageElement.innerText = "Error en la conexión.";
                messageElement.className = "mt-3 text-center text-sm text-red-600";
            } finally {
                spinner.classList.add('hidden');
                btnText.textContent = 'Enviar enlace';
                submitBtn.disabled = false;
            }
        });
    </script>
</body>

</html>