<?php
require_once dirname(__DIR__) . '/classes/DatabaseSessionManager.php';
require_once dirname(__DIR__) . '/access-token/seguridad/jwt.php';
require_once dirname(__DIR__) . '/classes/ConfigUrl.php';

$baseUrl = ConfigUrl::get();
$datosUsuario = validarTokenSuperUser();
if (!$datosUsuario) {
    header("Location: " . $baseUrl . "login/index.php");
    exit;
}

$title = "Lista de Empresas";

include dirname(__DIR__) . '/master_admin/navbar.php';
?>
<script>
    const baseUrl = "<?php echo $baseUrl; ?>";
</script>
<div class="container mt-4">
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Estado</th>
                    <th scope="col">Logo</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">URL</th>
                    <th scope="col">Acción</th>
                </tr>
            </thead>
            <tbody>
                <!-- El contenido se llenará dinámicamente con JavaScript -->
            </tbody>
        </table>
    </div>
</div>

<script src="<?php echo $baseUrl ?>assets/js/master_admin/company_list.js?v=" <?php echo time(); ?>"></script>