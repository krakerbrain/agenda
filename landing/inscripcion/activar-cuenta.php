<?php
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';
require_once dirname(__DIR__, 2) . '/access-token/seguridad/ActivationTokenService.php';
$tokenService = new ActivationTokenService();
$baseUrl = ConfigUrl::get();

$token = $_GET['token'] ?? null;
$email = $_GET['email'] ?? null;
$tokenValido = false;
$userId = null;

if ($token) {
    $userId = $tokenService->validateToken($token);
    $tokenValido = $userId !== null;
}
include_once dirname(__DIR__, 2) . '/landing/partials/head.php';
?>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <?php
    include_once dirname(__DIR__, 2) . '/landing/partials/logo_navbar.php';
    ?>
    <div class="bg-white shadow-md rounded p-6 w-full max-w-md">
        <?php if ($tokenValido): ?>
            <h2 class="text-xl font-bold mb-4">Establece tu contraseña</h2>
            <form action="#" method="POST" class="space-y-4">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                <input type="hidden" name="user_id" value="<?php echo $userId; ?>">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Contraseña</label>
                    <input type="password" name="password" class="w-full mt-1 border rounded px-3 py-2" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Confirmar contraseña</label>
                    <input type="password" name="password2" class="w-full mt-1 border rounded px-3 py-2" required>
                </div>

                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                    Activar cuenta
                </button>
            </form>
        <?php else: ?>
            <h2 class="text-xl font-bold text-red-600">Token inválido o expirado</h2>
            <p class="mt-2 text-gray-600">Por favor solicita un nuevo enlace de activación.</p>
        <?php endif; ?>
    </div>
    <!-- Modal de respuesta -->
    <?php include dirname(__DIR__, 2) . '/includes/modal-activar-cuenta.php'; ?>
    <?php include dirname(__DIR__, 2) . '/includes/modal-info.php'; ?>
    <!-- Overlay Loading spinner -->
    <div id="loadingOverlay" class="fixed inset-0 hidden bg-black opacity-50  items-center justify-center  z-50">
        <span class="loader"></span>
    </div>

    <script type="module" src="<?php echo $baseUrl; ?>assets/js/landing/activarCuenta.js?v=<?php echo time(); ?>">
    </script>
</body>

</html>