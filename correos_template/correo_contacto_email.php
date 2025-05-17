<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Mensaje de Contacto</title>
</head>

<body style="font-family: Arial, system-ui; background-color: #f8f9fa; margin: 0; padding: 0;">
    <table width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #f8f9fa; padding: 20px 0;">
        <tr>
            <td align="center">
                <table width="600" cellspacing="0" cellpadding="0" border="0"
                    style="background-color: #ffffff; border-radius: 10px; overflow: hidden;">
                    <tr>
                        <td colspan="2" style="padding: 20px; text-align: center; background-color: #1B637F;">
                            <img src="{{logo_url}}" alt="Logo Agendarium" style="height: 50px;">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="padding: 20px;">
                            <h2 style="color: #1B637F;">Nuevo mensaje de contacto</h2>
                            <p>Has recibido un nuevo mensaje a través del formulario de contacto:</p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="padding: 20px;">
                            <table width="100%" cellspacing="0" cellpadding="5" border="1"
                                style="border-color: #dddddd; border-collapse: collapse;">
                                <tr>
                                    <th align="left" style="padding: 10px; background-color: #f2f2f2; width: 120px;">
                                        Nombre</th>
                                    <td style="padding: 10px;">{{nombre}}</td>
                                </tr>
                                <tr>
                                    <th align="left" style="padding: 10px; background-color: #f2f2f2;">Email</th>
                                    <td style="padding: 10px;"><a href="mailto:{{email}}">{{email}}</a></td>
                                </tr>
                                <tr>
                                    <th align="left" style="padding: 10px; background-color: #f2f2f2;">Mensaje</th>
                                    <td style="padding: 10px;">{{mensaje}}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="padding: 20px;">
                            <p>Puedes responder directamente a este correo para contactar al remitente.</p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"
                            style="background-color: #f8f9fa; text-align: center; padding: 20px; font-size: 12px; color: #777777;">
                            &copy; {{current_year}} Agendarium. Todos los derechos reservados.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>