<?php
require_once dirname(__DIR__, 2) . '/configs/init.php';
require_once dirname(__DIR__, 2) . '/access-token/seguridad/JWTAuth.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';

$baseUrl = ConfigUrl::get();
$auth = new JWTAuth();
$auth->validarTokenUsuario();
?>

<div class="max-w-4xl mx-auto px-4 py-8">
    <!-- Formulario Agregar Empresa -->
    <div class="mb-8">
        <div class="bg-white rounded-2xl shadow-md p-6">
            <form id="addCompanyForm" class="space-y-4">
                <div>
                    <label for="business_name" class="block text-sm font-medium mb-1">Nombre de la Empresa:</label>
                    <input type="text" id="business_name" name="business_name"
                        class="w-full rounded-md border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm p-1"
                        required>
                </div>

                <div>
                    <label for="logo" class="block text-sm font-medium mb-1">Logo (opcional):</label>
                    <input type="file" id="logo" name="logo"
                        class="w-full text-sm text-gray-700 border border-gray-300 rounded-md cursor-pointer focus:outline-none p-1">
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium mb-1">Teléfono:</label>
                    <input type="tel" id="phone" name="phone"
                        class="w-full rounded-md border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm p-1"
                        required>
                </div>

                <div>
                    <label for="address" class="block text-sm font-medium mb-1">Dirección:</label>
                    <input type="text" id="address" name="address"
                        class="w-full rounded-md border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm p-1"
                        required>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium mb-1">Descripción</label>
                    <textarea id="description" name="description" rows="2" maxlength="120"
                        placeholder="Descripción breve de la empresa (máximo 120 caracteres)"
                        class="w-full rounded-md border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm p-1">Empresa dedicada a...</textarea>
                    <p class="text-xs text-gray-500 mt-1">Máximo 120 caracteres.</p>
                </div>

                <div class="text-center">
                    <button type="submit" id="addCompany"
                        class="inline-flex items-center px-4 py-2 rounded-md border bg-blue-600 text-white hover:bg-blue-700 transition disabled:opacity-50">
                        <span class="hidden spinner-border spinner-border-sm mr-2" aria-hidden="true"></span>
                        <span class="button-text">Agregar Empresa</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Formulario Agregar Usuario Inicial -->
    <div>
        <div class="bg-white rounded-2xl shadow-md p-6">
            <h4 class="text-xl font-semibold text-center mb-6">Agregar Usuario Inicial</h4>
            <form id="addUserForm" autocomplete="off" class="space-y-4">
                <input type="hidden" id="role_id" name="role_id" value="2">

                <div>
                    <label for="company_id" class="block text-sm font-medium mb-1">ID de la Empresa:</label>
                    <input type="text" id="company_id" name="company_id"
                        class="w-full rounded-md border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm p-1"
                        required>
                </div>

                <div>
                    <label for="username" class="block text-sm font-medium mb-1">Nombre de Usuario:</label>
                    <input type="text" id="username" name="usuario"
                        class="w-full rounded-md border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm p-1"
                        required>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium mb-1">Correo Electrónico:</label>
                    <input type="email" id="email" name="correo" autocomplete="off"
                        class="w-full rounded-md border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm p-1"
                        required>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium mb-1">Contraseña:</label>
                    <input type="password" id="password" name="password" autocomplete="new-password"
                        class="w-full rounded-md border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm p-1"
                        required>
                </div>

                <div>
                    <label for="password2" class="block text-sm font-medium mb-1">Repetir Contraseña:</label>
                    <input type="password" id="password2" name="password2" autocomplete="new-password"
                        class="w-full rounded-md border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm p-1"
                        required>
                </div>

                <div class="text-center">
                    <button type="submit" id="addUser"
                        class="inline-flex items-center px-4 py-2 rounded-md border bg-blue-600 text-white hover:bg-blue-700 transition disabled:opacity-50">
                        <span class="hidden spinner-border spinner-border-sm mr-2" aria-hidden="true"></span>
                        <span class="button-text">Agregar Usuario</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>