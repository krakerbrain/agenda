<?php
require_once dirname(__DIR__) . '/classes/ConfigUrl.php';
$baseUrl = ConfigUrl::get();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Empresa</title>
    <link rel="stylesheet" href="../assets/css/form.css">
</head>

<body>
    <div class="master-admin-container">
        <div class="form-container">
            <h2>Agregar Empresa</h2>
            <form id="addCompanyForm">
                <label for="name">Nombre de la Empresa:</label>
                <input type="text" id="name" name="name" required>

                <label for="logo">Logo (opcional):</label>
                <input type="file" id="logo" name="logo">

                <button type="submit">Agregar Empresa</button>
            </form>
        </div>

        <div class="form-container">
            <h2>Agregar Usuario Inicial</h2>
            <form id="addUserForm">
                <label for="username">Nombre de Usuario:</label>
                <input type="text" id="username" name="usuario" required>

                <label for="email">Correo Electrónico:</label>
                <input type="email" id="email" name="correo" required>

                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>

                <label for="password2">Repetir Contraseña:</label>
                <input type="password" id="password2" name="password2" required>

                <label for="company_id">ID de la Empresa:</label>
                <input type="text" id="company_id" name="company_id" required>

                <button type="submit">Agregar Usuario</button>
            </form>
        </div>
    </div>
    <script>
        const baseUrl = '<?php echo $baseUrl; ?>';

        document.getElementById('addCompanyForm').addEventListener('submit', function(event) {
            event.preventDefault();

            const formData = new FormData(this);
            fetch(`${baseUrl}master_admin/add_company.php`, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Empresa agregada exitosamente');
                    } else {
                        alert('Error al agregar la empresa');
                    }
                })
                .catch(error => console.error('Error:', error));
        });

        document.getElementById('addUserForm').addEventListener('submit', function(event) {
            event.preventDefault();

            const formData = new FormData(this);
            // formData.append('master_admin', 'master_admin');
            fetch(`${baseUrl}login/registra_usuario.php`, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Usuario agregado exitosamente');
                    } else {
                        alert('Error al agregar el usuario');
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    </script>
</body>

</html>