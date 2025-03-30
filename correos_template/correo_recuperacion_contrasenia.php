<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperación de Contraseña</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            color: #333333;
        }

        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border: 1px solid #dddddd;
            border-radius: 8px;
            overflow: hidden;
        }

        .header {
            background-color: #1a1728;
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }

        .logo {
            max-width: 150px;
            height: auto;
        }

        .content {
            padding: 20px;
        }

        .content h2 {
            margin-top: 0;
            color: #1a1728;
        }

        .content p {
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .button-container {
            text-align: center;
            margin: 25px 0;
        }

        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #1a1728;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
        }

        .details {
            background-color: #f9f9f9;
            border-radius: 5px;
            padding: 15px;
            margin-top: 20px;
        }

        .details table {
            width: 100%;
            border-collapse: collapse;
        }

        .details table td {
            padding: 8px;
            border: 1px solid #dddddd;
        }

        .footer {
            text-align: center;
            padding: 15px;
            background-color: #f4f4f4;
            color: #777777;
            font-size: 12px;
        }

        @media only screen and (max-width: 600px) {
            .email-container {
                margin: 0;
                border-radius: 0;
                border: none;
            }

            .content,
            .header,
            .footer {
                padding: 15px;
            }

            .button {
                display: block;
                padding: 10px;
            }
        }
    </style>
</head>

<body>
    <div class="email-container">
        <!-- Encabezado -->
        <div class="header">
            <h4>Agendarium.com</h4>
            <h2>Recuperación de Contraseña</h2>
        </div>

        <!-- Contenido -->
        <div class="content">
            <h2>Hola {nombre_usuario},</h2>

            <p>Hemos recibido una solicitud para restablecer la contraseña de tu cuenta en {nombre_empresa}.</p>

            <p>Por favor, haz clic en el siguiente botón para crear una nueva contraseña:</p>

            <div class="button-container">
                <a href="{enlace_recuperacion}" class="button">Restablecer Contraseña</a>
            </div>

            <p>Si no puedes hacer clic en el botón, copia y pega la siguiente URL en tu navegador:</p>
            <p><small>{enlace_recuperacion}</small></p>

            <div class="details">
                <table>
                    <tr>
                        <td><strong>Enlace válido hasta:</strong></td>
                        <td>{tiempo_expiracion}</td>
                    </tr>
                    <tr>
                        <td><strong>Solicitado por:</strong></td>
                        <td>{nombre_usuario}</td>
                    </tr>
                    <tr>
                        <td><strong>Correo electrónico:</strong></td>
                        <td>{user_email}</td>
                    </tr>
                </table>
            </div>

            <p>Si no solicitaste este cambio, puedes ignorar este mensaje. Tu contraseña permanecerá igual.</p>

            <p>Atentamente,</p>
            <p><strong>El equipo de {nombre_empresa}</strong></p>
        </div>

        <!-- Pie de página -->
        <div class="footer">
            <p>Este es un correo automático, por favor no responder directamente a este mensaje.</p>
            <p>&copy; {year} {nombre_empresa}. Todos los derechos reservados.</p>
        </div>
    </div>
</body>

</html>