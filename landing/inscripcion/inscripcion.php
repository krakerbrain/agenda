<?php
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';
$baseUrl = ConfigUrl::get();

include_once dirname(__DIR__, 2) . '/landing/partials/head.php';
?>

<body class="bg-custom-light font-sans text-gray-800">
    <?php
    include_once dirname(__DIR__, 2) . '/landing/partials/navbar.php';
    ?>
    <section class="bg-gradient-to-r from-[#1B637F] to-[#249373] text-white py-12">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-3xl md:text-4xl font-bold mb-4">Comienza tu prueba gratis</h1>
            <p class="text-xl opacity-90 max-w-2xl mx-auto">14 días sin costo - Sin tarjeta requerida</p>
        </div>
    </section>
    <div class="container mx-auto px-4 py-12 max-w-5xl">
        <div class="grid md:grid-cols-2 gap-8 items-start">
            <!-- Columna izquierda: Formulario -->
            <div class="bg-white rounded-xl shadow-lg p-6 md:p-8 border border-gray-100">
                <h2 class="text-2xl font-bold text-[#1B637F] mb-6">Información básica</h2>

                <form id="registrationForm" action="<?php echo $baseUrl; ?>inscripcion/procesar" method="POST">
                    <!-- Campos esenciales -->
                    <div class="space-y-4">
                        <div>
                            <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre
                                completo*</label>
                            <input type="text" id="nombre" name="nombre" required
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-[#1B637F] focus:ring-2 focus:ring-[#1B637F]/50">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email*</label>
                                <input type="email" id="email" name="email" required
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-[#1B637F] focus:ring-2 focus:ring-[#1B637F]/50">
                            </div>
                            <div>
                                <label for="telefono"
                                    class="block text-sm font-medium text-gray-700 mb-1">Teléfono*</label>
                                <input type="tel" id="telefono" name="telefono" required
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-[#1B637F] focus:ring-2 focus:ring-[#1B637F]/50">
                            </div>
                        </div>

                        <div>
                            <label for="negocio" class="block text-sm font-medium text-gray-700 mb-1">Tipo de
                                negocio*</label>
                            <select id="negocio" name="negocio" required
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-[#1B637F] focus:ring-2 focus:ring-[#1B637F]/50">
                                <option value="">Seleccione...</option>
                                <option value="medico">Médico/Consultorio</option>
                                <option value="belleza">Salón de belleza</option>
                                <option value="estetica">Centro de estética</option>
                                <option value="otros">Otros servicios</option>
                            </select>
                        </div>

                        <div class="flex items-start">
                            <input type="checkbox" id="terminos" name="terminos" required
                                class="mt-1 h-4 w-4 text-[#1B637F] rounded border-gray-300 focus:ring-[#1B637F]">
                            <label for="terminos" class="ml-2 block text-sm text-gray-700">
                                Acepto los <a href="<?php echo $baseUrl; ?>terminos"
                                    class="text-[#1B637F] hover:underline">Términos de servicio</a> y
                                <a href="<?php echo $baseUrl; ?>privacidad"
                                    class="text-[#1B637F] hover:underline">Política de privacidad</a>
                            </label>
                        </div>
                    </div>

                    <button type="submit"
                        class="mt-6 w-full bg-[#1B637F] hover:bg-[#2B819F] text-white font-bold py-3 px-4 rounded-lg transition-colors">
                        Crear mi cuenta gratis
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
    <?php
    include_once dirname(__DIR__, 2) . '/landing/partials/footer.php';
    ?>