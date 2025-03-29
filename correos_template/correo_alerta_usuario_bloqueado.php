<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aviso: Cliente Bloqueado Intentó Hacer una Cita</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
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
            background-color: #ff4d4d;
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }

        .content {
            padding: 20px;
            color: #333333;
        }

        .content h2 {
            margin-top: 0;
        }

        .content p {
            line-height: 1.6;
        }

        .user-details {
            background-color: #f9f9f9;
            border-radius: 5px;
            margin-top: 20px;
        }

        .user-details table {
            width: 100%;
            border-collapse: collapse;
        }

        .user-details table td {
            padding: 8px;
            border: 1px solid #dddddd;
        }

        .footer {
            text-align: center;
            padding: 10px;
            background-color: #f4f4f4;
            color: #777777;
            font-size: 12px;
        }

        @media only screen and (max-width: 600px) {
            h2 {
                font-size: 1rem;
            }

            table,
            p {
                font-size: 0.8rem;
            }


        }
    </style>
</head>

<body>
    <div class="email-container">
        <!-- Encabezado -->
        <div class="header">
            <h2>Aviso: Cliente bloqueado intentó hacer una cita</h2>
        </div>

        <!-- Contenido -->
        <div class="content">
            <h2>Hola {empresa},</h2>
            <p>Un cliente bloqueado ha intentado realizar una cita. A continuación, te proporcionamos los detalles del
                cliente:</p>

            <!-- Detalles del usuario -->
            <div class="user-details">
                <table>
                    <tr>
                        <td><strong>Nombre:</strong></td>
                        <td>{nombre_cliente}</td>
                    </tr>
                    <tr>
                        <td><strong>Teléfono:</strong></td>
                        <td>{telefono_cliente}</td>
                    </tr>
                    <tr>
                        <td><strong>Correo Electrónico:</strong></td>
                        <td>{email_cliente}</td>
                    </tr>
                    <tr>
                        <td><strong>Fecha del Intento:</strong></td>
                        <td>{fecha_intento}</td>
                    </tr>
                </table>
            </div>

            <p>Por favor, ponte en contacto con el cliente si es necesario o toma las medidas correspondientes.</p>
            <p>Gracias,</p>
            <p><strong>Equipo de Soporte</strong></p>
        </div>

        <!-- Pie de página -->
        <div class="footer">
            <p>Este es un correo automático, por favor no responder directamente a este mensaje.</p>
        </div>
    </div>
</body>

</html>