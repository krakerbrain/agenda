<?php
require_once dirname(__DIR__, 2) . '/access-token/seguridad/JWTAuth.php';
require_once dirname(__DIR__, 2) . '/classes/DatabaseSessionManager.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';

$baseUrl = ConfigUrl::get();
$manager = new DatabaseSessionManager();
$conn = $manager->getDB();
$auth = new JWTAuth();
$datosUsuario = $auth->validarTokenUsuario();

$company_id = $datosUsuario['company_id'];

$media = $conn->prepare("SELECT id, name FROM social_networks ORDER BY name");
$media->execute();

?>
<div class="container mt-5">
    <form id="datosEmpresaForm" action="" method="POST">
        <input type="hidden" name="logo_url" id="logoUrl" value="">
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
                <tr>
                    <th>Red Social</th>
                    <th>URL</th>
                    <th>Red preferida
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