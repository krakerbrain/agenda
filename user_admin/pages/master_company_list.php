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
        <table class="min-w-full divide-y divide-gray-200 bg-white">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col"
                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th scope="col"
                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado
                    </th>
                    <th scope="col"
                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Logo</th>
                    <th scope="col"
                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre
                    </th>
                    <th scope="col"
                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">URL</th>
                    <th scope="col"
                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acción
                    </th>
                </tr>
            </thead>
            <tbody>
                <!-- El contenido se llenará dinámicamente con JavaScript -->
            </tbody>
        </table>
    </div>
</div>
</body>

</html>