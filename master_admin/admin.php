<?php
require_once dirname(__DIR__) . '/classes/ConfigUrl.php';
$baseUrl = ConfigUrl::get();
$title = "Agregar Empresa";

include dirname(__DIR__) . '/partials/head.php';

?>

<body>
    <div class="container mt-5">
        <!-- Formulario Agregar Empresa -->
        <div class="row mb-4">
            <div class="col-md-6 offset-md-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="card-title mb-4 text-center">Agregar Empresa</h4>
                        <form id="addCompanyForm">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nombre de la Empresa:</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="logo" class="form-label">Logo (opcional):</label>
                                <input type="file" class="form-control" id="logo" name="logo">
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
                                <input type="hidden" class="form-control" id="role_id" name="role_id" value="1">
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
    <script>
    const baseUrl = '<?php echo $baseUrl; ?>';

    document.getElementById('addCompanyForm').addEventListener('submit', async function(event) {
        event.preventDefault();
        // Mostrar spinner y deshabilitar botón
        displaySpinner('addCompany', true);
        const formData = new FormData(this);
        try {
            const response = await fetch(`${baseUrl}master_admin/add_company.php`, {
                method: 'POST',
                body: formData
            });
            const {
                success,
                company_id,
                error
            } = await response.json();

            if (success) {
                document.getElementById('company_id').value = company_id;
                //limpiar formulario
                this.reset();
                alert('Empresa agregada exitosamente');
            } else {
                alert(error);
            }
        } catch (error) {
            console.error('Error:', error);
        } finally {
            // Ocultar spinner y habilitar botón
            displaySpinner('addCompany', false);
        }
    });
    document.getElementById('addUserForm').addEventListener('submit', async function(event) {
        event.preventDefault();
        // Mostrar spinner y deshabilitar botón
        displaySpinner('addUser', true);

        const formData = new FormData(this);
        try {
            const response = await fetch(`${baseUrl}login/registra_usuario.php`, {
                method: 'POST',
                body: formData
            });
            const {
                success,
                error
            } = await response.json();

            if (success) {
                //limpiar formulario
                this.reset();
                alert('Usuario agregado exitosamente');
            } else {
                alert(error);
                this.reset();
            }
        } catch (error) {
            console.error('Error:', error);
        } finally {
            // Ocultar spinner y habilitar botón
            displaySpinner('addUser', false);
        }
    });

    function displaySpinner(id, show) {
        const button = document.getElementById(id);
        const spinner = button.querySelector('.spinner-border');
        const buttonText = button.querySelector('.button-text');
        const textBtn = id === 'addCompany' ? 'Agregar Empresa' : 'Agregar Usuario';
        if (!show) {
            spinner.classList.add('d-none');
            buttonText.textContent = textBtn;
            button.disabled = false;
        } else {
            spinner.classList.remove('d-none');
            buttonText.textContent = 'Procesando...';
            button.disabled = true;
        }
    }
    </script>
</body>

</html>