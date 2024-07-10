<?php
include "registra_usuario.php";
include "../partials/head.php";
?>

<body class="bg-primary d-flex justify-content-center align-items-center vh-100">
    <div class="bg-white p-5 rounded">
        <div class="justify-content-center">
            <form action="" method="post" class="form-group">
                <div class="text-center">
                    <h4>REGISTRO DE USUARIOS</h4>
                </div>
                <div class="input-group">
                    <div class="input-group-text bg-primary text-light">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <input type="text" name="usuario" id="usuario" class="form-control" placeholder="Ingrese un nombre de usuario" ">
                </div>
                <div class=" input-group mt-2">
                    <div class="input-group-text bg-primary text-light">
                        <i class="fa-solid fa-envelope"></i>
                    </div>
                    <input type="mail" name="correo" id="correo" class="form-control" placeholder="Ingrese un correo">
                </div>
                <div class="input-group mt-2">
                    <div class="input-group-text bg-primary text-light">
                        <i class="fa-solid fa-key"></i>
                    </div>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Ingrese una clave">
                    <div class="input-group-text bg-light">
                        <a href="#" class="pe-auto text-primary">
                            <i class="fa-solid fa-eye" onclick="verpass(1)"></i>
                        </a>
                    </div>
                </div>
                <div class="input-group mt-2">
                    <div class="input-group-text bg-primary text-light">
                        <i class="fa-solid fa-key"></i>
                    </div>
                    <input type="password" name="password2" id="password2" class="form-control" placeholder="Ingrese otra vez">
                    <div class="input-group-text bg-light">
                        <a href="#" class="pe-auto text-primary">
                            <i class="fa-solid fa-eye" onclick="verpass(2)"></i>
                        </a>
                    </div>
                </div>
                <div class="form-group mt-3">
                    <input type="submit" value="Registrar" class="btn btn-primary w-100">
                </div>
                <div class="mt-3 text-center">
                    <?php echo $error ?>
                </div>
                <div>
                    <a href="index.php">Ir al inicio</a>
                </div>
            </form>
        </div>
    </div>

</body>

<script>
    function verpass(param) {
        var pass1 = document.getElementById('password');
        var pass2 = document.getElementById('password2');
        if (param == 1) {
            pass1.type = pass1.type == "password" ? "text" : "password"
        } else {
            pass2.type = pass2.type == "password" ? "text" : "password"
        }
    }

    <?php if ($error == "correo") { ?>
        document.getElementById('correo').focus();
    <?php } else if ($error == "vacio") { ?>
        document.getElementById('usuario').focus();
    <?php } ?>
</script>

</html>