<?php
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';
$baseUrl = ConfigUrl::get();
$section = 'home';
require_once dirname(__DIR__, 2) . '/landing/partials/head.php';
?>

<body class="bg-gray-50 text-gray-800">
    <?php require_once dirname(__DIR__, 2) . '/landing/partials/navbar.php'; ?>

    <header class="pt-24 text-center">
        <h1 class="text-2xl font-bold text-[#1C4175] mb-6">Política de Privacidad</h1>
    </header>

    <main class="max-w-3xl mx-auto px-6 py-12 bg-white rounded-lg shadow-md mt-4">
        <section class="mb-8">
            <h2 class="text-2xl font-semibold text-[#28809C] mb-2">1. Introducción</h2>
            <p class="mb-2">En AGENDARIUM, nos tomamos muy en serio la privacidad de nuestros usuarios. Esta política
                explica cómo recopilamos, usamos, compartimos y protegemos tu información.</p>
            <p class="text-sm text-gray-600">Esta política se aplica en cumplimiento de la normativa aplicable en
                materia de protección de datos, incluyendo el Reglamento General de Protección de Datos (RGPD) si
                corresponde, o las leyes locales del país desde donde operamos.</p>
        </section>

        <section class="mb-8">
            <h2 class="text-2xl font-semibold text-[#28809C] mb-2">2. Datos que recopilamos</h2>
            <p class="mb-2">Recopilamos la siguiente información de los usuarios:</p>
            <ul class="list-disc list-inside space-y-1 text-sm">
                <li><strong>Nombre del usuario:</strong> Para identificar a los usuarios en la plataforma.</li>
                <li><strong>Correo electrónico:</strong> Para conectar tu cuenta con Google Calendar y enviarte
                    notificaciones.</li>
                <li><strong>Teléfono:</strong> Para mostrarlo como referencia en la página de reservas y enviar
                    notificaciones a través de WhatsApp Business.</li>
                <li><strong>Dirección (opcional):</strong> Para fines de referencia y personalización.</li>
                <li><strong>Redes sociales:</strong> Si eliges configurarlas, se utilizan para mejorar la interacción
                    con tus clientes.</li>
            </ul>
        </section>

        <section class="mb-8">
            <h2 class="text-2xl font-semibold text-[#28809C] mb-2">3. Propósito del uso de los datos</h2>
            <ul class="list-disc list-inside space-y-1 text-sm mb-2">
                <li>Conectar tu cuenta con Google Calendar para agendar citas y eventos.</li>
                <li>Mostrar información de contacto (teléfono y redes sociales) a los clientes que reserven citas
                    contigo.</li>
                <li>Enviar notificaciones y recordatorios mediante correo electrónico o WhatsApp Business.</li>
            </ul>
            <p class="text-sm">
                Al integrar tu cuenta con Google Calendar, consientes que Agendarium tenga acceso a tu calendario para
                crear y gestionar eventos. Esta información solo se utiliza con fines operativos y nunca se almacena
                fuera del ecosistema de Google salvo para mostrarla al usuario. Esta integración está sujeta a la <a
                    href="https://policies.google.com/privacy" class="text-[#28809C] underline" target="_blank">Política
                    de Privacidad de Google</a>.
            </p>
        </section>

        <section class="mb-8">
            <h2 class="text-2xl font-semibold text-[#28809C] mb-2">4. Compartición de datos</h2>
            <p class="mb-2">Los datos que proporcionas solo se comparten entre las partes involucradas en el proceso de
                reservas, es decir:</p>
            <ul class="list-disc list-inside text-sm space-y-1">
                <li>El cliente que reserva una cita.</li>
                <li>El prestador de servicios que atenderá la cita.</li>
            </ul>
            <p class="text-sm mt-2">No compartimos tus datos con terceros para fines comerciales o publicitarios.</p>
        </section>

        <section class="mb-8">
            <h2 class="text-2xl font-semibold text-[#28809C] mb-2">5. Derechos del usuario</h2>
            <ul class="list-disc list-inside text-sm space-y-1 mb-2">
                <li>Acceder, corregir o eliminar tus datos personales.</li>
                <li>Solicitar información sobre cómo manejamos tus datos.</li>
                <li>Retirar tu consentimiento para el uso de tus datos en cualquier momento.</li>
            </ul>
            <p class="text-sm">Para ejercer estos derechos, contáctanos a través del correo <a
                    href="mailto:agendaroad@gmail.com" class="text-[#28809C] underline">agendaroad@gmail.com</a>.</p>
        </section>

        <section class="mb-8">
            <h2 class="text-2xl font-semibold text-[#28809C] mb-2">6. Seguridad</h2>
            <p class="text-sm">Nos esforzamos por proteger tus datos utilizando medidas de seguridad técnicas y
                organizativas. Sin embargo, ningún sistema es completamente seguro, por lo que no podemos garantizar la
                seguridad absoluta de tu información.</p>
        </section>

        <section class="mb-8">
            <h2 class="text-2xl font-semibold text-[#28809C] mb-2">7. Contacto</h2>
            <ul class="text-sm list-inside list-disc mb-2">
                <li>Correo electrónico: <a href="mailto:soporte@agendarium.com"
                        class="text-[#28809C] underline">soporte@agendarium.com</a></li>
                <li>Página web: <a href="https://agendarium.com" class="text-[#28809C] underline"
                        target="_blank">https://agendarium.com</a></li>
            </ul>
        </section>

        <section class="mb-8">
            <h2 class="text-2xl font-semibold text-[#28809C] mb-2">8. Cambios en la política</h2>
            <p class="text-sm">Podemos actualizar esta política de privacidad en cualquier momento. Te notificaremos
                sobre cambios importantes mediante un aviso en nuestra plataforma.</p>
        </section>

        <section>
            <h2 class="text-2xl font-semibold text-[#28809C] mb-2">9. Uso de cookies y almacenamiento local</h2>
            <p class="text-sm mb-2">En AGENDARIUM utilizamos tecnologías como <strong>cookies</strong> o
                <strong>almacenamiento local del navegador</strong> únicamente para mejorar tu experiencia en la
                plataforma. Por ejemplo, recordamos la última pestaña o sección que visitaste para que, al volver,
                puedas continuar fácilmente desde donde lo dejaste.
            </p>
            <p class="text-sm">No utilizamos estas tecnologías para realizar seguimiento de tu actividad con fines
                publicitarios ni compartimos esta información con terceros.</p>
        </section>
    </main>
    <?php include_once dirname(__DIR__, 2) . '/landing/partials/footer.php'; ?>
    <script src="<?php echo $baseUrl; ?>assets/js/landing/index.js?v=<?php echo time(); ?>"></script>
</body>