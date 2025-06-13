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

<div class="max-w-4xl mx-auto px-4 py-8">
    <form id="integrationsForm" class="bg-white rounded-lg shadow-md p-6">
        <!-- Encabezado -->
        <div class="flex items-baseline space-x-2 mb-6">
            <h3 class="text-xl font-semibold text-gray-800">Servicios Integrados</h3>
            <a tabindex="0" role="button" class="help focus:outline-none" data-bs-trigger="focus"
                data-bs-toggle="popover" data-bs-title="Servicios Integrados"
                data-bs-content="Si tienes desbloqueados estos servicios puedes habilitarlos o deshabilitarlos. Si los tienes bloqueados, comunícate con el administrador para activarlos.">
                <i class="fa fa-circle-question text-blue-500 hover:text-blue-600"></i>
            </a>
        </div>

        <!-- Listado de Integraciones -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 hidden sm:table-header-group">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Servicio</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">
                            Estado</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Acción</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($integrations as $integration): ?>
                    <tr class="flex flex-col sm:table-row">
                        <!-- Celda de Servicio con Badge para móvil -->
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 sm:whitespace-nowrap">
                            <div class="flex flex-col">
                                <span><?php echo htmlspecialchars($integration['name']); ?></span>
                                <span
                                    class="sm:hidden mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $integration['company_enabled'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                    <?php echo $integration['company_enabled'] ? 'Habilitado' : 'Deshabilitado'; ?>
                                </span>
                            </div>
                        </td>

                        <!-- Celda de Estado (solo desktop) -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden sm:table-cell">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $integration['company_enabled'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                <?php echo $integration['company_enabled'] ? 'Habilitado' : 'Deshabilitado'; ?>
                            </span>
                        </td>

                        <!-- Celda de Acción -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <button type="button"
                                class="w-full sm:w-auto inline-flex justify-center items-center px-3 py-1 border border-transparent text-xs font-medium rounded shadow-sm text-white <?php echo $integration['company_enabled'] ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700'; ?> focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                data-integration-id="<?php echo $integration['integration_id']; ?>"
                                data-company-enabled="<?php echo !$integration['company_enabled']; ?>"
                                data-integration-name="<?php echo htmlspecialchars($integration['name']); ?>">
                                <?php echo $integration['company_enabled'] ? 'Deshabilitar' : 'Habilitar'; ?>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </form>
</div>

<?php include dirname(__DIR__, 2) . '/includes/modal-accept-cancel.php';