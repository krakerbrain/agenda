<?php
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';
$baseUrl = ConfigUrl::get();
$section = 'home';
require_once dirname(__DIR__, 2) . '/landing/partials/head.php';
?>

<body class="bg-gray-50 text-gray-800">
    <?php require_once dirname(__DIR__, 2) . '/landing/partials/navbar.php'; ?>
    <header class="pt-24 text-center">
        <h1 class="text-2xl font-bold text-[#1C4175] mb-6">Términos y Condiciones</h1>
    </header>

    <main class="max-w-3xl mx-auto px-6 py-12 bg-white rounded-lg shadow-md mt-4">
        <!-- Introducción -->
        <section class="mb-8 bg-indigo-50 p-6 rounded-lg">
            <p class="text-indigo-800">Bienvenido a Agendarium, una plataforma diseñada para facilitar la gestión de
                citas entre empresas prestadoras de servicios y sus clientes. Al acceder o utilizar nuestra aplicación,
                ya sea como empresa administradora o como usuario que reserva una cita, aceptas estos Términos y
                Condiciones en su totalidad.</p>
        </section>

        <!-- Sección 1 -->
        <section class="mb-8">
            <h2 class="text-2xl font-semibold text-indigo-600 mb-4 flex items-center">
                <span
                    class="bg-indigo-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">1</span>
                Definiciones
            </h2>
            <div class="ml-11">
                <p class="mb-3"><span class="font-semibold text-indigo-700">"Agendarium":</span> Se refiere a la
                    aplicación web de gestión de citas desarrollada y administrada por [Nombre de tu empresa o tu
                    nombre].</p>

                <p class="mb-3"><span class="font-semibold text-indigo-700">"Empresa" o "Administrador":</span> Persona
                    o entidad que configura y administra una cuenta en Agendarium para gestionar sus servicios y recibir
                    reservas.</p>

                <p class="mb-3"><span class="font-semibold text-indigo-700">"Usuario" o "Cliente":</span> Persona que
                    accede al enlace generado por la empresa para reservar una cita, sin necesidad de registrarse en el
                    sistema.</p>

                <p class="mb-3"><span class="font-semibold text-indigo-700">"Servicios":</span> Funcionalidades
                    proporcionadas por Agendarium, incluyendo la reserva de citas, integración con calendarios, envío de
                    correos electrónicos, y personalización visual.</p>
            </div>
        </section>

        <!-- Sección 2 -->
        <section class="mb-8">
            <h2 class="text-2xl font-semibold text-indigo-600 mb-4 flex items-center">
                <span
                    class="bg-indigo-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">2</span>
                Uso del Servicio
            </h2>

            <div class="ml-11">
                <h3 class="text-xl font-medium text-indigo-700 mb-3 mt-4">2.1 Para Empresas</h3>
                <ul class="list-disc pl-6 space-y-2 mb-4">
                    <li>Las empresas son responsables de la veracidad y exactitud de la información que configuran en su
                        perfil (horarios, tipos de servicios, condiciones de atención, etc.).</li>
                    <li>Las empresas son responsables de gestionar adecuadamente las reservas recibidas a través de
                        Agendarium, así como de cumplir con los compromisos adquiridos con sus clientes.</li>
                    <li>Agendarium permite la personalización de la interfaz, subida de logos, y configuración de
                        formularios, pero no garantiza resultados específicos en cuanto a ventas o volumen de reservas.
                    </li>
                </ul>

                <h3 class="text-xl font-medium text-indigo-700 mb-3 mt-6">2.2 Para Usuarios</h3>
                <ul class="list-disc pl-6 space-y-2">
                    <li>Los usuarios pueden acceder a los formularios de reserva mediante un enlace proporcionado por la
                        empresa. No se requiere registro.</li>
                    <li>La información introducida por los usuarios (nombre, correo, teléfono, etc.) será compartida
                        exclusivamente con la empresa administradora de la cita y no con terceros.</li>
                    <li>Agendarium no es responsable por la calidad del servicio recibido por parte de la empresa, ni
                        por cambios, cancelaciones o incumplimientos por parte de la misma.</li>
                </ul>
            </div>
        </section>

        <!-- Sección 3 -->
        <section class="mb-8">
            <h2 class="text-2xl font-semibold text-indigo-600 mb-4 flex items-center">
                <span
                    class="bg-indigo-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">3</span>
                Privacidad y Datos
            </h2>
            <div class="ml-11">
                <ul class="list-disc pl-6 space-y-2">
                    <li>Agendarium almacena información únicamente con el propósito de facilitar la reserva y
                        organización de citas.</li>
                    <li>Los datos proporcionados por los usuarios son tratados con confidencialidad y no se venderán ni
                        compartirán con terceros ajenos al servicio.</li>
                    <li>Para más información, por favor revisa nuestra <a href="#"
                            class="text-indigo-600 hover:underline font-medium">Política de Privacidad</a>.</li>
                </ul>
            </div>
        </section>

        <!-- Sección 4 -->
        <section class="mb-8">
            <h2 class="text-2xl font-semibold text-indigo-600 mb-4 flex items-center">
                <span
                    class="bg-indigo-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">4</span>
                Responsabilidades
            </h2>
            <div class="ml-11">
                <ul class="list-disc pl-6 space-y-2">
                    <li>Agendarium actúa como intermediario tecnológico entre empresas y usuarios, y no se hace
                        responsable de la relación comercial entre ambas partes.</li>
                    <li>No garantizamos la disponibilidad ininterrumpida del servicio, aunque nos comprometemos a
                        realizar esfuerzos razonables para mantener la operatividad del sistema.</li>
                </ul>
            </div>
        </section>

        <!-- Sección 5 -->
        <section class="mb-8">
            <h2 class="text-2xl font-semibold text-indigo-600 mb-4 flex items-center">
                <span
                    class="bg-indigo-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">5</span>
                Integración con Servicios Externos
            </h2>
            <div class="ml-11">
                <ul class="list-disc pl-6 space-y-2">
                    <li>Las empresas pueden optar por integrar su cuenta de Google Calendar para sincronizar
                        automáticamente las citas.</li>
                    <li>Esta integración requiere autorización explícita por parte del administrador de la empresa y
                        puede revocarse en cualquier momento desde su cuenta de Google.</li>
                </ul>
            </div>
        </section>

        <!-- Sección 6 -->
        <section class="mb-8">
            <h2 class="text-2xl font-semibold text-indigo-600 mb-4 flex items-center">
                <span
                    class="bg-indigo-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">6</span>
                Propiedad Intelectual
            </h2>
            <div class="ml-11">
                <ul class="list-disc pl-6 space-y-2">
                    <li>El código, diseño, marcas, logotipos y demás elementos que forman parte de Agendarium son
                        propiedad de [Tu nombre o empresa] y están protegidos por leyes de propiedad intelectual.</li>
                    <li>No se permite la reproducción, distribución o modificación sin autorización expresa.</li>
                </ul>
            </div>
        </section>

        <!-- Sección 7 -->
        <section class="mb-8">
            <h2 class="text-2xl font-semibold text-indigo-600 mb-4 flex items-center">
                <span
                    class="bg-indigo-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">7</span>
                Modificaciones
            </h2>
            <div class="ml-11">
                <ul class="list-disc pl-6 space-y-2">
                    <li>Nos reservamos el derecho de modificar estos Términos y Condiciones en cualquier momento. Los
                        cambios serán notificados a través de la plataforma o vía correo electrónico a los
                        administradores registrados.</li>
                </ul>
            </div>
        </section>

        <!-- Sección 8 -->
        <section class="mb-8">
            <h2 class="text-2xl font-semibold text-indigo-600 mb-4 flex items-center">
                <span
                    class="bg-indigo-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">8</span>
                Terminación del Servicio
            </h2>
            <div class="ml-11">
                <ul class="list-disc pl-6 space-y-2">
                    <li>Las empresas pueden solicitar la eliminación de su cuenta en cualquier momento.</li>
                    <li>Nos reservamos el derecho de suspender o eliminar cuentas que hagan uso indebido del sistema,
                        incurran en fraude o violen estos términos.</li>
                </ul>
            </div>
        </section>

        <!-- Sección 9 -->
        <section class="mb-8">
            <h2 class="text-2xl font-semibold text-indigo-600 mb-4 flex items-center">
                <span
                    class="bg-indigo-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">9</span>
                Ley Aplicable
            </h2>
            <div class="ml-11">
                <ul class="list-disc pl-6 space-y-2">
                    <li>Este acuerdo se rige por las leyes del [país donde operas]. Cualquier conflicto que surja será
                        resuelto ante los tribunales competentes del mismo.</li>
                </ul>
            </div>
        </section>

        <!-- Footer -->
        <footer class="mt-12 pt-6 border-t border-gray-200 text-sm text-gray-500">
            <p>© <span id="current-year"></span> Agendarium. Todos los derechos reservados.</p>
        </footer>
        </div>
    </main>
    <?php include_once dirname(__DIR__, 2) . '/landing/partials/footer.php'; ?>
    <script src="<?php echo $baseUrl; ?>assets/js/landing/index.js?v=<?php echo time(); ?>"></script>
</body>