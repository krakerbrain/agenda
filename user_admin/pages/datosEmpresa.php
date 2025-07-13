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
            <label for="banner" class="block text-base font-semibold mb-1">Subir Banner</label>
            <input type="file"
                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200"
                id="banner" name="banner" accept="image/*">
            <small class="text-gray-400">Sube una imagen y ajústala al área del banner.</small>
        </div>
        <div class="mb-6">
            <div id="image-container" class="hidden max-w-2xl mx-auto">
                <img id="image-to-crop" src="#" alt="Imagen subida" class="w-full">
            </div>
        </div>
        <div class="mb-6">
            <label class="block text-base font-semibold mb-1">Previsualización del Banner</label>
            <div id="banner-preview"
                class="max-w-2xl w-full h-40 overflow-hidden bg-gray-100 flex items-center justify-center">
                <img id="cropped-image" src="#" alt="Banner recortado" class="w-full h-full object-cover hidden">
            </div>
        </div>
        <button type="button" id="save-banner"
            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition hidden">Guardar Banner</button>
        <div class="mb-6">
            <label class="block text-base font-semibold mb-1">Seleccionar un banner predeterminado</label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="default-banners-container">
                <div>
                    <div class="bg-white rounded shadow">
                        <img id="saved-cropped-image"
                            src="<?php echo $baseUrl . 'assets/img/banners/banner_vacio.png'; ?>"
                            class="w-full h-32 object-cover rounded-t" alt="Banner Personalizado">
                        <div class="p-2 text-center">
                            <input class="form-radio text-blue-600" type="radio" name="selected-banner"
                                id="banner-custom" value="custom">
                            <label class="ml-2" for="banner-custom">Seleccionar</label>
                        </div>
                    </div>
                </div>
                <?php
                $defaultBanners = [
                    'default_belleza.png',
                    'default_salud.png',
                    'default_deportes.png'
                ];
                foreach ($defaultBanners as $banner) : ?>
                    <div>
                        <div class="bg-white rounded shadow">
                            <img src="<?php echo $baseUrl . 'assets/img/banners/' . $banner; ?>"
                                class="w-full h-32 object-cover rounded-t" alt="Banner Predeterminado">
                            <div class="p-2 text-center">
                                <input class="form-radio text-blue-600" type="radio" name="selected-banner"
                                    id="banner-<?php echo $banner; ?>" value="<?php echo $banner; ?>">
                                <label class="ml-2" for="banner-<?php echo $banner; ?>">Seleccionar</label>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
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
    <div class="mt-8">
        <table class="min-w-full bg-white rounded shadow overflow-hidden">
            <thead>
                <tr class="bg-gray-100 text-gray-700">
                    <th class="py-2 px-4">Red Social</th>
                    <th class="py-2 px-4">URL</th>
                    <th class="py-2 px-4 text-center">Red preferida
                        <button id="edit-preferred" class="text-blue-600 hover:text-blue-800 ml-2"
                            title="Editar Red Preferida">
                            <i class="fas fa-pencil-alt"></i>
                        </button>
                    </th>
                    <th class="py-2 px-4">Acciones</th>
                </tr>
            </thead>
            <tbody id="social-networks">
                <!-- Aquí se cargarán las redes sociales -->
            </tbody>
        </table>
    </div>
    <div id="preferedSocial"
        class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 transition-opacity duration-300 pointer-events-none">
        <div class="flex items-center justify-center min-h-screen">
            <div
                class="bg-white rounded-lg shadow-lg w-full max-w-md mx-auto overflow-hidden transform transition-all duration-300 scale-95">
                <div class="flex items-center justify-between px-4 py-3 border-b">
                    <h5 class="text-lg font-semibold" id="preferedSocialLabel">Red Social Favorita</h5>
                    <button type="button"
                        class="text-gray-400 hover:text-gray-700 text-2xl leading-none focus:outline-none close-modal"
                        aria-label="Close">&times;</button>
                </div>
                <div class="px-4 py-4 text-gray-700 modal-body"></div>
                <div class="flex justify-end px-4 py-3 border-t">
                    <button type="button"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded close-modal btn-siguiente"
                        id="acceptButton">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    const baseUrl = '<?php echo $baseUrl ?>';
</script>