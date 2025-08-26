<?php
require_once dirname(__DIR__, 2) . '/configs/init.php';
require_once dirname(__DIR__, 2) . '/access-token/seguridad/JWTAuth.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';

$baseUrl = ConfigUrl::get();
$auth = new JWTAuth();
$auth->validarTokenUsuario();
?>
<div class="max-w-6xl mx-auto mt-8 px-4">
    <div class="overflow-x-auto rounded-lg shadow">
        <div class="max-w-6xl mx-auto mt-4 px-4">
            <h2 class="text-xl font-semibold mb-4">Agregar nuevo Feature Flag</h2>
            <form id="addFeatureFlagForm" class="flex flex-col md:flex-row gap-2 mb-6">
                <input type="text" id="featureName" placeholder="Nombre del flag"
                    class="border rounded px-2 py-1 flex-1">
                <select id="companySelect" class="border rounded px-2 py-1 flex-1">
                    <option value="">Selecciona compañía</option>
                </select>
                <button type="submit" class="bg-blue-600 text-white px-4 py-1 rounded">Agregar</button>
            </form>
        </div>
        <table class="min-w-full divide-y divide-gray-200 bg-white">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col"
                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th scope="col"
                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre de
                        Flag</th>
                    <th scope="col"
                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Compañía
                    </th>
                    <th scope="col"
                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Activo
                    </th>
                    <th scope="col"
                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acción
                    </th>
                </tr>
            </thead>
            <tbody id="featureFlagsList">
                <!-- Se llenará con JS -->
            </tbody>
        </table>
    </div>
</div>
</body>

</html>