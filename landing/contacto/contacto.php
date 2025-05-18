<?php
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';
$baseUrl = ConfigUrl::get();
$section = 'contacto';
include_once dirname(__DIR__, 2) . '/landing/partials/head.php';

?>

<body class="bg-gray-50 text-gray-800">
    <?php
    include_once dirname(__DIR__, 2) . '/landing/partials/navbar.php';
    ?>
    <input type="hidden" name="is_contact" id="is_contact" value="true">
    <section class="min-h-screen flex items-center justify-center px-4 py-20">
        <div class="max-w-xl w-full bg-white rounded-2xl shadow-lg p-8">
            <h1 class="text-2xl font-bold text-[#1B637F] mb-4">¿Tienes dudas o necesitas ayuda?</h1>

            <p class="mb-6">
                Puedes escribirnos directamente a
                <a href="mailto:soporte@agendarium.com"
                    class="text-[#249373] font-medium underline">soporte@agendarium.com</a>
                o llenar el siguiente formulario:
            </p>
            <?php if (isset($_GET['success'])): ?>
                <div id="success-message"
                    class="p-3 rounded mb-6 text-green-800 bg-green-200 opacity-0 transition-opacity transition-colors duration-1000">
                    ¡Tu mensaje fue enviado correctamente!
                </div>
            <?php elseif (isset($_GET['error'])): ?>
                <div class="bg-red-100 text-red-800 p-3 rounded mb-6">
                    Ocurrió un error al enviar el mensaje. Inténtalo de nuevo.
                </div>
            <?php endif; ?>

            <form action="procesar_contacto.php" method="POST" class="space-y-4">
                <div>
                    <label for="nombre" class="block text-sm font-semibold mb-1">Nombre completo</label>
                    <input type="text" name="nombre" id="nombre" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[#249373]" />
                </div>

                <div>
                    <label for="email" class="block text-sm font-semibold mb-1">Correo electrónico</label>
                    <input type="email" name="email" id="email" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[#249373]" />
                </div>

                <div>
                    <label for="mensaje" class="block text-sm font-semibold mb-1">Mensaje</label>
                    <textarea name="mensaje" id="mensaje" rows="5" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[#249373]"></textarea>
                </div>

                <button type="submit"
                    class="bg-[#249373] hover:bg-[#1B637F] text-white px-6 py-3 rounded-lg font-semibold shadow-md transition-all">
                    Enviar mensaje
                </button>
            </form>
        </div>
    </section>
    <?php
    include_once dirname(__DIR__, 2) . '/landing/partials/footer.php';
    ?>
    <script src="<?php echo $baseUrl; ?>assets/js/landing/index.js?v=<?php echo time(); ?>"></script>