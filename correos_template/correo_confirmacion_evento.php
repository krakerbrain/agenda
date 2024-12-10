<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cita confirmada</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            color: #333333;
        }

        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            padding: 20px;
            text-align: center;
            position: relative;
        }

        .header img {
            position: absolute;
            top: 20px;
            right: 20px;
            height: 50px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: normal;
        }

        .content {
            padding: 30px;
        }

        .content p {
            margin: 15px 0;
            line-height: 1.6;
        }

        .content ul {
            list-style-type: none;
            padding: 0;
        }

        .content ul li {
            padding: 10px 0;
            border-bottom: 1px solid #dddddd;
        }

        .content ul li:last-child {
            border-bottom: none;
        }

        .footer {
            background-color: #f8f9fa;
            text-align: center;
            padding: 20px;
            color: #777777;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="{ruta_logo}" alt="Logo de la empresa">
        </div>
        <div class="content">
            <p>Hola <strong>{nombre_cliente}</strong>,</p>
            <p>¡Buenas noticias! Tu reserva para el <strong>{fecha_reserva}</strong> a las
                <strong>{hora_reserva}</strong> ha sido confirmada. Aquí tienes los detalles finales de tu cita:
            </p>
            <ul>
                <li><strong>Fecha:</strong> {fecha_reserva}</li>
                <li><strong>Hora:</strong> {hora_reserva}</li>
                <li><strong>Evento:</strong> {evento}</li>
                <li><strong>Notas adicionales:</strong> {notas}</li>
            </ul>
            <p>Esperamos verte pronto. Si tienes alguna pregunta o necesitas hacer algún cambio, no dudes en
                contactarnos.</p>

            <p>Gracias por confiar en nosotros.</p>
            <p>Atentamente,<br>{nombre_empresa}</p>
        </div>
        <div class="footer">
            &copy; 2024 {nombre_empresa}. Todos los derechos reservados.
        </div>
    </div>
</body>

</html>