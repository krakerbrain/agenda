<table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, sans-serif;">
    <tr>
        <td align="center" style="padding: 40px 0;">
            <img src="{{logo_url}}" alt="Agendarium" width="150">
        </td>
    </tr>
    <tr>
        <td align="center" style="padding: 20px; background-color: #f9f9f9;">
            <table width="600" style="max-width: 600px; margin: auto; text-align: center;">
                <tr>
                    <td>
                        <h2 style="color: #1B637F;">¡Bienvenido, {{nombre}}!</h2>
                        <p style="line-height: 1.5; font-size: 16px; color: #333;">
                            Para activar tu cuenta, haz clic en el botón de abajo:
                        </p>
                        <div style="margin: 30px 0;">
                            <a href="{{activation_url}}"
                                style="background-color: #1B637F; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block;">
                                Activar mi cuenta
                            </a>
                        </div>
                        <p style="line-height: 1.5; font-size: 14px; color: #555;">
                            Este enlace expira en 24 horas.<br>
                            Gracias por unirte a Agendarium.
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td style="text-align: center; font-size: 12px; color: #888; padding: 20px;">
            &copy; {{current_year}} Agendarium. Todos los derechos reservados.
        </td>
    </tr>
</table>