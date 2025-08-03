<?php

require_once dirname(__DIR__, 2) . '/configs/init.php';
require_once dirname(__DIR__, 2) . '/access-token/seguridad/JWTAuth.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';
require_once dirname(__DIR__, 2) . '/classes/RedesSociales.php';
$baseUrl = ConfigUrl::get();
$auth = new JWTAuth();
$datosUsuario = $auth->validarTokenUsuario();
$company_id = $datosUsuario['company_id'];

$redesSociales = new RedesSociales($company_id);
$media = $redesSociales->getSocialForDatosEmpresa();

?>
<div class="max-w-4xl mx-auto mt-8">
    <form id="datosEmpresaForm" action="" method="POST" class="space-y-6">
        <input type="hidden" name="logo_url" id="logoUrl" value="">
        <input type="hidden" name="banner_url" id="bannerUrl" value="">
        <input type="hidden" name="company_id" id="companyId" value="<?php echo $company_id ?>">
        <input type="hidden" name="company_name" id="companyName" value=" ">
        <h4 class="companyName text-2xl font-bold mb-4"></h4>
        <div class="mb-6">
            <div class="mb-2">
                <img src="" alt="Logo de la Empresa" class="w-32 h-32 object-contain rounded mx-auto" id="preview-logo">
            </div>

            <label class="block text-base font-semibold mb-1">Cambiar Logo</label>

            <div id="logoDropzone"
                class="flex items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition">
                <p class="text-sm text-gray-500 text-center">
                    Arrastra aquí el logo o <span class="text-blue-600 underline">haz clic para subir</span>
                </p>
            </div>

            <input type="file" id="logo" name="logo" class="hidden" accept="image/*">
        </div>

        <div class="mb-6">
            <!-- Vista previa del banner actual -->
            <div class="mb-4">
                <label class="block text-base font-semibold mb-2">Banner Actual</label>
                <img src="<?php echo $baseUrl . 'assets/img/banners/banner_vacio.png' ?>" alt="Banner Actual"
                    class="w-full h-40 object-cover rounded-lg border border-gray-200" id="current-banner">
            </div>

            <!-- Selector de banner (personalizado o predeterminado) -->
            <div class="mb-6">
                <label class="block text-base font-semibold mb-2">Cambiar Banner</label>

                <!-- Opción 1: Subir banner personalizado -->
                <div class="mb-4 p-4 border border-gray-200 rounded-lg">
                    <div class="flex items-center mb-2">
                        <input class="form-radio text-blue-600" type="radio" name="selected-banner" id="banner-custom"
                            value="custom">
                        <label class="ml-2 font-medium" for="banner-custom">Banner Personalizado</label>
                    </div>

                    <!-- Dropzone para subir banner -->
                    <div id="bannerDropzone"
                        class="flex items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition mt-2">
                        <p class="text-sm text-gray-500 text-center">
                            Arrastra aquí el banner o <span class="text-blue-600 underline">haz clic para subir</span>
                            <small class="block text-gray-400 mt-1">El banner tiene una medida pero si quieres ajustar
                                la imagen puedes hacerlo con la rueda del mouse</small>
                            <small class="block text-gray-400 mt-1">Recomendado: 1200×300px</small>
                        </p>
                    </div>
                    <input type="file" id="banner" name="banner" class="hidden" accept="image/*">

                    <!-- Editor de banner (oculto inicialmente) -->
                    <div id="banner-editor-container" class="hidden">
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Recortar Banner</label>
                            <div class="border rounded-lg overflow-hidden">
                                <img id="image-to-crop" src="#" alt="Imagen para recortar" class="max-w-full">
                            </div>
                        </div>
                        <button id="save-custom-banner" type="button"
                            class="mt-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                            Guardar Banner Recortado
                        </button>
                    </div>
                </div>

                <!-- Opción 2: Banners predeterminados -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <?php
                    $defaultBanners = [
                        'default_belleza.png',
                        'default_salud.png',
                        'default_deportes.png'
                    ];
                    foreach ($defaultBanners as $banner): ?>
                        <div class="p-4 border border-gray-200 rounded-lg hover:border-blue-300 transition">
                            <div class="flex items-center mb-2">
                                <input class="form-radio text-blue-600" type="radio" name="selected-banner"
                                    id="banner-<?php echo $banner; ?>" value="<?php echo $banner; ?>">
                                <label class="ml-2 font-medium" for="banner-<?php echo $banner; ?>">
                                    <?php echo str_replace(['default_', '.png'], '', $banner); ?>
                                </label>
                            </div>
                            <img src="<?php echo $baseUrl . 'assets/img/banners/' . $banner; ?>"
                                class="w-full h-24 object-cover rounded border border-gray-200 mt-2"
                                alt="Banner <?php echo $banner; ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <label for="phone" class="block text-base font-semibold mb-1">Teléfono</label>
                <input type="tel"
                    class="block w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                    id="phone" name="phone" placeholder="Número de teléfono" value="+56912345678"
                    title="El formato usado es +56912345678">
            </div>
            <div>
                <label for="address" class="block text-base font-semibold mb-1">Dirección</label>
                <input type="text"
                    class="block w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                    id="address" name="address" placeholder="Dirección de la empresa" value="">
            </div>
        </div>
        <div class="mb-6">
            <label for="description" class="block text-base font-semibold mb-1">Descripción</label>
            <textarea
                class="block w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                id="description" name="description" rows="2" maxlength="150"
                placeholder="Descripción breve de la empresa (máximo 150 caracteres)"></textarea>
            <div class="text-gray-400 text-sm">Máximo 150 caracteres.</div>
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Guardar
            Cambios</button>
    </form>
    <div class="mt-8">
        <form id="social-form" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="social-network" class="block text-base font-semibold mb-1">Red Social</label>
                    <select id="social-network" name="social_network"
                        class="block w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <?php foreach ($media as $red) { ?>
                            <option value="<?php echo $red['id'] ?>"><?php echo $red['name'] ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div>
                    <label for="social-url" class="block text-base font-semibold mb-1">URL</label>
                    <input type="text" id="social-url" name="social_url"
                        class="block w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Ingrese la URL">
                </div>
                <div class="flex items-end">
                    <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition w-full">Agregar</button>
                </div>
            </div>
        </form>
    </div>
    <!-- Nueva sección de redes sociales como cards -->
    <div class="mt-8 space-y-4" id="social-networks-container">
        <div class="flex items-center space-x-2 mb-4">
            <span class="text-sm font-medium text-gray-500">Arrastra las cards para reordenar</span>
        </div>

        <!-- Las cards se insertarán aquí dinámicamente -->
        <div id="social-networks" class="space-y-4"></div>
    </div>
</div>
<?php include dirname(__DIR__, 2) . '/includes/modal-info.php'; ?>
<script>
    const baseUrl = '<?php echo $baseUrl ?>';
</script>