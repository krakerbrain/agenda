<?php
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';
$baseUrl = ConfigUrl::get();
$section = 'inscripcion';
include_once dirname(__DIR__, 2) . '/landing/partials/head.php';
?>

<body class="bg-custom-light font-sans text-gray-800">
    <?php
    include_once dirname(__DIR__, 2) . '/landing/partials/navbar.php';
    ?>
    <div class="hidden md:block bg-white py-8"></div>
    <section class="bg-gradient-to-r from-[#1B637F] to-[#249373] text-white py-12">
        <div class="container mx-auto px-4 text-center">
            <div class="lg:hidden flex justify-around mb-6">
                <div class="flex items-center">
                    <img src="<?php echo $baseUrl; ?>assets/img/landing/logo/Isotipo-Agendarium.svg"
                        alt="Logo Agendarium" class="h-12 w-auto">
                    <span class="text-2xl font-bold text-[#1B637F] ml-1 mt-6">Agendarium</span>
                </div>
            </div>
            <h1 class="text-2xl md:text-4xl font-bold mb-4 lg:pt-6">Comienza tu prueba gratis</h1>
            <p class="text-xl opacity-90 max-w-2xl mx-auto">30 días sin costo - Sin tarjeta requerida</p>
        </div>
    </section>
    <div class="container mx-auto px-4 py-12 max-w-5xl">
        <div class="grid md:grid-cols-2 gap-8 items-start">
            <!-- Columna izquierda: Formulario -->
            <div class="bg-white rounded-xl shadow-lg p-6 md:p-8 border border-gray-100">
                <h2 class="text-2xl font-bold text-[#1B637F] mb-6">Información básica</h2>

                <form id="companyForm" action="<?php echo $baseUrl; ?>inscripcion/procesar" method="POST"
                    enctype="multipart/form-data" class="space-y-6">
                    <!-- Nombre del Negocio -->
                    <div>
                        <label for="business_name" class="block text-sm font-medium text-gray-700 mb-1">Nombre del
                            Negocio*</label>
                        <input type="text" id="business_name" name="business_name" required
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-[#1B637F] focus:ring-2 focus:ring-[#1B637F]/50">
                    </div>

                    <!-- Nombre del Dueño -->
                    <div>
                        <label for="owner_name" class="block text-sm font-medium text-gray-700 mb-1">Nombre del
                            Dueño/Usuario Principal*</label>
                        <input type="text" id="owner_name" name="owner_name" required
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-[#1B637F] focus:ring-2 focus:ring-[#1B637F]/50">
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo
                            Electrónico*</label>
                        <input type="email" id="email" name="email" required
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-[#1B637F] focus:ring-2 focus:ring-[#1B637F]/50">
                    </div>
                    <!-- Botón -->
                    <button type="submit"
                        class="mt-6 w-full bg-[#1B637F] hover:bg-[#2B819F] text-white font-bold py-3 px-4 rounded-lg transition-colors">
                        Crear cuenta de empresa
                    </button>
                </form>

            </div>

            <!-- Columna derecha: Beneficios -->
            <div class="bg-[#F8FAFC] rounded-xl p-6 md:p-8 border border-gray-200">
                <h3 class="text-xl font-bold text-[#1B637F] mb-4">Tu prueba incluye:</h3>
                <ul class="space-y-4">
                    <li class="flex items-start">
                        <span class="material-icons text-[#249373] mr-2">check_circle</span>
                        <span>Acceso completo a todas las funciones</span>
                    </li>
                    <li class="flex items-start">
                        <span class="material-icons text-[#249373] mr-2">check_circle</span>
                        <span>Soporte prioritario durante el trial</span>
                    </li>
                    <li class="flex items-start">
                        <span class="material-icons text-[#249373] mr-2">check_circle</span>
                        <span>Hasta 50 citas mensuales</span>
                    </li>
                    <li class="flex items-start">
                        <span class="material-icons text-[#249373] mr-2">check_circle</span>
                        <span>Recordatorios automáticos</span>
                    </li>
                    <li class="flex items-start">
                        <span class="material-icons text-[#249373] mr-2">check_circle</span>
                        <span>Sin compromiso de permanencia</span>
                    </li>
                </ul>

                <div class="mt-8 p-4 bg-white rounded-lg border border-[#249373]/20">
                    <div class="flex items-start">
                        <span class="material-icons text-[#FFBF2F] mr-2">star</span>
                        <div>
                            <h4 class="font-bold text-[#1B637F]">Recomendación profesional</h4>
                            <p class="text-sm text-gray-600 mt-1">"Agendarium redujo mis citas perdidas en un 70%. La
                                mejor inversión para mi consulta."</p>
                            <p class="text-xs text-gray-500 mt-2">- Dra. Valeria M., Dermatóloga</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal de respuesta -->
    <div id="responseModal"
        class="fixed inset-0 bg-black opacity-50 flex items-center justify-center z-50 opacity-0 pointer-events-none transition-opacity duration-300 ease-out">
        <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6 text-center">
            <h2 class="text-xl font-bold text-[#1B637F] mb-4">Registro exitoso</h2>
            <p id="responseMessage" class="text-gray-700 mb-6">Tu empresa fue creada correctamente.</p>
            <button onclick="closeModal()"
                class="bg-[#1B637F] hover:bg-[#2B819F] text-white font-semibold px-4 py-2 rounded">
                Cerrar
            </button>
        </div>
    </div>
    <?php
    include_once dirname(__DIR__, 2) . '/landing/partials/footer.php';
    ?>
    <script src="<?php echo $baseUrl; ?>assets/js/landing/index.js?v=<?php echo time(); ?>"></script>
    <script src="<?php echo $baseUrl; ?>assets/js/landing/inscripcion.js?v=<?php echo time(); ?>"></script>