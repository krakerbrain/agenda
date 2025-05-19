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
            <div id="alert-container" class="mb-6"></div>

            <form action="procesar_contacto.php" method="POST" class="space-y-4" id="contactForm">
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
                    class="relative bg-[#249373] hover:bg-[#1B637F] text-white px-6 py-3 rounded-lg font-semibold shadow-md transition-all flex items-center justify-center"
                    id="submitBtn">
                    <span id="btnText">Enviar mensaje</span>
                    <svg id="spinner" class="hidden animate-spin ml-2 h-5 w-5 text-white"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                </button>
            </form>
        </div>
    </section>
    <?php
    include_once dirname(__DIR__, 2) . '/landing/partials/footer.php';
    ?>
    <script src="<?php echo $baseUrl; ?>assets/js/landing/index.js?v=<?php echo time(); ?>"></script>
    <script src="<?php echo $baseUrl; ?>assets/js/landing/contacto.js?v=<?php echo time(); ?>"></script>