<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alerta de Reserva</title>
</head>

<body style="font-family: Arial, system-ui; background-color: #f8f9fa; margin: 0; padding: 0;">
    <table width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #f8f9fa; padding: 20px 0;">
        <tr>
            <td align="center">
                <table width="600" cellspacing="0" cellpadding="0" border="0"
                    style="background-color: #ffffff; border-radius: 10px; overflow: hidden;">
                    <tr>
                        <td style="padding: 20px; text-align: left;">
                            <strong style="font-size: 24px;">Alerta de Reserva</strong>
                        </td>
                        <td style="padding: 20px; text-align: right;">
                            <img src="{ruta_logo}" alt="Logo de la empresa" style="height: 50px;">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="padding: 20px;">
                            <p>Hola <strong>{nombre_usuario}</strong>,</p>
                            <p>{nombre_cliente} ha hecho una reserva para el <strong>{fecha}</strong> a las
                                <strong>{hora}</strong>. A continuación, te proporcionamos los detalles de la reserva:
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="padding: 20px;">
                            <table width="100%" cellspacing="0" cellpadding="5" border="1"
                                style="border-color: #dddddd; border-collapse: collapse;">
                                <tr>
                                    <th align="left" style="padding: 10px; background-color: #f2f2f2;">Cliente</th>
                                    <td style="padding: 10px;">{nombre_cliente}</td>
                                </tr>
                                <tr>
                                    <th align="left" style="padding: 10px; background-color: #f2f2f2;">Fecha</th>
                                    <td style="padding: 10px;">{fecha}</td>
                                </tr>
                                <tr>
                                    <th align="left" style="padding: 10px; background-color: #f2f2f2;">Hora</th>
                                    <td style="padding: 10px;">{hora}</td>
                                </tr>
                                <tr>
                                    <th align="left" style="padding: 10px; background-color: #f2f2f2;">Servicio</th>
                                    <td style="padding: 10px;">{nombre_servicio}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="padding: 20px;">
                            <p>Recuerda que debes confirmar la cita en tus configuraciones.</p>
                            <p>Atentamente,<br>Agenda Road</p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"
                            style="background-color: #f8f9fa; text-align: center; padding: 20px; font-size: 12px; color: #777777;">
                            &copy; 2024 Agenda Road. Todos los derechos reservados.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>