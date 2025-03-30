<?php
require_once dirname(__DIR__) . '/configs/init.php';
require_once dirname(__DIR__) . '/classes/Users.php';
require_once dirname(__DIR__) . '/classes/ConfigUrl.php';

$baseUrl = ConfigUrl::get();
$title = "Restablecer Contraseña";
include '../partials/head.php';

$token = $_GET['token'] ?? '';
$validToken = false;

if ($token) {
    $user = new Users();
    $tokenData = $user->validate_reset_token($token);

    if ($tokenData && strtotime($tokenData['expires_at']) > time()) {
        $validToken = true;
    }
}
?>

<style>
    .login-container {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 15px;
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        color: #f5f5f5;
    }
</style>

<body style="background-color: #1a1728;" class="d-flex justify-content-center align-items-center vh-100">
    <div class="login-container p-5">
        <?php if ($validToken): ?>
            <div class="text-center mb-4">
                <h4 class="display-6">RESTABLECER CONTRASEÑA</h4>
            </div>
            <form id="resetPasswordForm">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                <div class="input-group mb-3">
                    <div class="input-group-text">
                        <i class="fa-solid fa-lock text-secondary"></i>
                    </div>
                    <input type="password" class="form-control" id="new-password" name="new_password"
                        placeholder="Nueva contraseña" required>
                </div>

                <div class="input-group mb-4">
                    <div class="input-group-text">
                        <i class="fa-solid fa-lock text-secondary"></i>
                    </div>
                    <input type="password" class="form-control" id="confirm-password" name="confirm_password"
                        placeholder="Confirmar contraseña" required>
                </div>

                <button type="submit" class="btn btn-info w-100">
                    <span class="spinner-border spinner-border-sm d-none" id="reset-spinner"></span>
                    <span id="reset-text">Actualizar Contraseña</span>
                </button>
            </form>
            <div id="reset-message" class="mt-3 text-center"></div>
        <?php else: ?>
            <div class="alert alert-danger text-center">
                <i class="fas fa-exclamation-circle me-2"></i>
                El enlace de recuperación no es válido o ha expirado.
            </div>
            <a href="<?= $baseUrl ?>/login" class="btn btn-info w-100">
                <i class="fas fa-arrow-left me-2"></i>Volver al login
            </a>
        <?php endif; ?>
    </div>

    <script>
        document.getElementById('resetPasswordForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const newPassword = formData.get('new_password');
            const confirmPassword = formData.get('confirm_password');

            if (newPassword !== confirmPassword) {
                document.getElementById('reset-message').innerText = "Las contraseñas no coinciden.";
                return;
            }

            try {
                const response = await fetch('controllers/reset_password_controller.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                document.getElementById('reset-message').innerText = result.message;
                if (result.success) {
                    setTimeout(() => {
                        window.location.href = '<?= $baseUrl ?>/login';
                    }, 2000);
                }
            } catch (error) {
                document.getElementById('reset-message').innerText = "Error en la conexión.";
            }
        });
    </script>
</body>