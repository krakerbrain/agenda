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
<div class="container mt-5">
    <form id="datosEmpresaForm" action="" method="POST">
        <input type="hidden" name="logo_url" id="logoUrl" value="">
        <input type="hidden" name="company_id" id="companyId" value="<?php echo $company_id ?>">
        <input type="hidden" name="company_name" id="companyName" value="">
        <h4 class="companyName"></h4>
        <div class="mb-3">
            <div class="">
                <img src="" alt="Logo de la Empresa" class="img-fluid w-25 logoEmpresa">
            </div>
            <div class="">
                <label for="logo" class="form-label">Cambiar Logo</label>
                <input type="file" class="form-control" id="logo" name="logo">
            </div>
        </div>
        <!-- Campo para subir el banner -->
        <div class="mb-3">
            <label for="banner" class="form-label">Subir Banner</label>
            <input type="file" class="form-control" id="banner" name="banner" accept="image/*">
            <small class="form-text text-muted">Sube una imagen y ajústala al área del banner.</small>
        </div>

        <!-- Contenedor para la imagen subida -->
        <div class="mb-3">
            <div id="image-container" style="display: none; max-width: 600px; margin: 0 auto;">
                <img id="image-to-crop" src="#" alt="Imagen subida" style="max-width: 100%;">
            </div>
        </div>

        <!-- Área de previsualización del banner -->
        <div class="mb-3">
            <label class="form-label">Previsualización del Banner</label>
            <div id="banner-preview"
                style="max-width: 600px; width: 100%; height: 150px; overflow: hidden; background-color: #f0f0f0;">
                <img id="cropped-image" src="#" alt="Banner recortado"
                    style="width: 100%; height: 100%; object-fit: cover; display: none;">
            </div>
        </div>

        <!-- Botón para guardar el banner -->
        <button type="button" id="save-banner" class="btn btn-primary" style="display: none;">Guardar Banner</button>

        <!-- Selección de imágenes -->
        <div class="mb-3">
            <label class="form-label">Seleccionar un banner predeterminado</label>

            <div class="row" id="default-banners-container">
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <img id="saved-cropped-image"
                            src="<?php echo $baseUrl . 'assets/img/banners/banner_vacio.png'; ?>" class="card-img-top"
                            alt="Banner Personalizado">
                        <div class="card-body text-center">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="selected-banner" id="banner-custom"
                                    value="custom">
                                <label class="form-check-label" for="banner-custom">
                                    Seleccionar
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                // Array de imágenes predeterminadas
                $defaultBanners = [
                    'default_belleza.png',
                    'default_salud.png',
                    'default_deportes.png'
                ];
                foreach ($defaultBanners as $banner) : ?>
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <img src="<?php echo $baseUrl . 'assets/img/banners/' . $banner; ?>" class="card-img-top"
                                alt="Banner Predeterminado">
                            <div class="card-body text-center">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="selected-banner"
                                        id="banner-<?php echo $banner; ?>" value="<?php echo $banner; ?>">
                                    <label class="form-check-label" for="banner-<?php echo $banner; ?>">
                                        Seleccionar
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <!-- Teléfono y Dirección -->
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="phone" class="form-label">Teléfono</label>
                <input type="tel" class="form-control" id="phone" name="phone" placeholder="Número de teléfono"
                    value="+56912345678" title="El formato usado es +56912345678">
            </div>
            <div class="col-md-6">
                <label for="address" class="form-label">Dirección</label>
                <input type="text" class="form-control" id="address" name="address"
                    placeholder="Dirección de la empresa" value="">
            </div>
        </div>

        <!-- Descripción de la Empresa -->
        <div class="row mb-3">
            <div class="col-md-12">
                <label for="description" class="form-label">Descripción</label>
                <textarea class="form-control" id="description" name="description" rows="2" maxlength="150"
                    placeholder="Descripción breve de la empresa (máximo 150 caracteres)"></textarea>
                <div class="form-text">Máximo 150 caracteres.</div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
    </form>
    <div class="container mt-4">
        <form id="social-form">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="social-network" class="form-label">Red Social</label>
                    <select id="social-network" name="social_network" class="form-select">
                        <?php foreach ($media as $red) { ?>
                            <option value="<?php echo $red['id'] ?>"><?php echo $red['name'] ?></option>
                        <?php } ?>
                    </select>

                </div>
                <div class="col-md-4">
                    <label for="social-url" class="form-label">URL</label>
                    <input type="text" id="social-url" name="social_url" class="form-control"
                        placeholder="Ingrese la URL">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Agregar</button>
                </div>
            </div>
        </form>
    </div>
    <div class="container mt-4">
        <table class="table">
            <thead>
                <tr class="head-table">
                    <th>Red Social</th>
                    <th>URL</th>
                    <th class="text-center">Red preferida
                        <button id="edit-preferred" class="btn btn-link p-0" title="Editar Red Preferida">
                            <i class="fas fa-pencil-alt"></i>
                        </button>
                        <!-- <a tabindex="0" role="button" data-bs-trigger="focus" class="btn help" data-bs-toggle="popover"
                            data-bs-title="Red preferida"
                            data-bs-content="Presiona en el ícono del lápiz para que puedas seleccionar tu red social preferida o página web. La función de ésto es que en el mensaje de Whatsapp de confirmación hay un botón que lleva al usuario a ver tu red social o página web"><i
                                class="fa fa-circle-question text-primary"></i></a> -->
                    </th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="social-networks">
                <!-- Aquí se cargarán las redes sociales -->
            </tbody>
        </table>
    </div>
    <div class="modal fade" id="preferedSocial" tabindex="-1" aria-labelledby="preferedSocialLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="preferedSocialLabel">Red Social Favorita</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-siguiente" id="acceptButton"
                        data-bs-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>

</div>
<script>
    const baseUrl = '<?php echo $baseUrl ?>';
</script>