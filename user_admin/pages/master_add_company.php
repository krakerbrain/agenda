<?php
require_once dirname(__DIR__, 2) . '/configs/init.php';
require_once dirname(__DIR__, 2) . '/access-token/seguridad/JWTAuth.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';

$baseUrl = ConfigUrl::get();
$auth = new JWTAuth();
$auth->validarTokenUsuario();
?>
<div class="container mt-5">
    <!-- Formulario Agregar Empresa -->
    <div class="row mb-4">
        <div class="col-md-6 offset-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title mb-4 text-center">Agregar Empresa</h4>
                    <form id="addCompanyForm">
                        <div class="mb-3">
                            <label for="business_name" class="form-label">Nombre de la Empresa:</label>
                            <input type="text" class="form-control" id="business_name" name="business_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="logo" class="form-label">Logo (opcional):</label>
                            <input type="file" class="form-control" id="logo" name="logo">
                        </div>
                        <div>
                            <label for="phone" class="form-label">Teléfono:</label>
                            <input type="tel" class="form-control" id="phone" name="phone" required>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Dirección:</label>
                            <input type="text" class="form-control" id="address" name="address" required>
                        </div>
                        <!-- Descripción de la Empresa -->
                        <div class="mb-3">
                            <div class="">
                                <label for="description" class="form-label">Descripción</label>
                                <textarea class="form-control" id="description" name="description" rows="2"
                                    maxlength="120"
                                    placeholder="Descripción breve de la empresa (máximo 120 caracteres)">Empresa dedicada a...</textarea>
                                <div class="form-text">Máximo 120 caracteres.</div>
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary" id="addCompany">
                                <span class="spinner-border spinner-border-sm d-none" aria-hidden="true"></span>
                                <span class="button-text">Agregar Empresa</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario Agregar Usuario Inicial -->
    <div class="row mb-4">
        <div class="col-md-6 offset-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title mb-4 text-center">Agregar Usuario Inicial</h4>
                    <form id="addUserForm">
                        <div class="mb-3">
                            <input type="hidden" class="form-control" id="role_id" name="role_id" value="2">
                        </div>
                        <div class="mb-3">
                            <label for="username" class="form-label">Nombre de Usuario:</label>
                            <input type="text" class="form-control" id="username" name="usuario" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo Electrónico:</label>
                            <input type="email" class="form-control" id="email" name="correo" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña:</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="password2" class="form-label">Repetir Contraseña:</label>
                            <input type="password" class="form-control" id="password2" name="password2" required>
                        </div>
                        <div class="mb-3">
                            <label for="company_id" class="form-label">ID de la Empresa:</label>
                            <input type="text" class="form-control" id="company_id" name="company_id" required>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary" id="addUser">
                                <span class="spinner-border spinner-border-sm d-none" aria-hidden="true"></span>
                                <span class="button-text">Agregar Usuario</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
</script>
</body>

</html>