<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a Agendarium</title>
    <style>
        body {
            font-family: Arial, system-ui;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0
        }

        table {
            background-color: #f8f9fa;
            padding: 20px 0
        }

        .link {
            text-align: center;
            margin: 10px 0;
        }

        .link a {
            background-color: #00c4f5;
            line-height: 1.1;
            padding: 0.8em 1.25em;
            text-align: center;
            font-size: 17px;
            display: inline-block;
            color: white;
            font-weight: bold;
            border-radius: 6px;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <table width="100%" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td align="center">
                <table width="600" cellspacing="0" cellpadding="0" border="0"
                    style="background-color: #ffffff; border-radius: 10px; overflow: hidden;">
                    <tr>
                        <td style="padding: 20px; text-align: center;">
                            <strong style="font-size: 24px;">Bienvenido a Agendarium</strong>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 20px;">
                            <p>Estimado/a {nombre_cliente},</p>
                            <p>Gracias por probar nuestra agenda de citas <strong>Agendarium</strong>. Nos complace
                                ayudarte a organizar tus reservas y optimizar tu tiempo.</p>
                            <p>Tu empresa ha sido activada.</p>
                            <p>Para comenzar a usar Agendarium, ingresa al siguiente enlace:</p>
                            <p class="link">
                                <a href="{login_link}">Acceder a Agendarium</a>
                            </p>
                            <p>Usa el correo electrónico con el que te registraste (<strong>{email_cliente}</strong>) y
                                la contraseña que elegiste al inscribirte.</p>
                            <p>Si tienes alguna pregunta o necesitas ayuda, no dudes en contactarnos a través de nuestro
                                WhatsApp:</p>
                            <p style="text-align: center; font-size: 18px; color: #007bff;">
                                <strong>https://wa.me/56975325574</strong>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td
                            style="background-color: #f8f9fa; text-align: center; padding: 20px; font-size: 12px; color: #777777;">
                            &copy; 2024 Agendarium. Todos los derechos reservados.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>