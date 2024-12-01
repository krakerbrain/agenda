<?php
require_once dirname(__DIR__, 2) . '/access-token/seguridad/JWTAuth.php';
require_once dirname(__DIR__, 2) . '/classes/CompanyModel.php';
require_once dirname(__DIR__, 2) . '/classes/IntegrationManager.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';

$baseUrl = ConfigUrl::get();
$company = new CompanyModel();
$integration = new IntegrationManager();
$auth = new JWTAuth();
$datosUsuario = $auth->validarTokenUsuario();

$company_id = $datosUsuario['company_id'];

// Consultar las integraciones de la empresa
$integrations = $integration->getCompanyIntegrations($company_id);
?>

<div class="container mt-5">
    <form id="integrationsForm">
        <div class="d-flex align-items-baseline">
            <h3 class="mb-3">Servicios Integrados</h3>
            <a tabindex="0" role="button" data-bs-trigger="focus" class="btn help" data-bs-toggle="popover"
                data-bs-title="Servicios Integrados"
                data-bs-content="Si tienes desbloqueados estos servicios puedes habilitarlos o deshabilitarlos. Si los tienes bloqueados, comunícate con el administrador para activarlos."></a>
        </div>

        <!-- Listado de Integraciones -->
        <table class="table table-borderless">
            <thead>
                <tr>
                    <th>Servicio</th>
                    <th>Estado</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($integrations as $integration): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($integration['name']); ?></td>
                        <td><?php echo $integration['company_enabled'] ? 'Habilitado' : 'Deshabilitado'; ?></td>
                        <td>
                            <button type="button"
                                class="btn btn-sm <?php echo $integration['company_enabled'] ? 'btn-danger' : 'btn-success'; ?>"
                                data-integration-id="<?php echo $integration['integration_id']; ?>"
                                data-company-enabled="<?php echo !$integration['company_enabled']; ?>">
                                <?php echo $integration['company_enabled'] ? 'Deshabilitar' : 'Habilitar'; ?>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </form>
</div>