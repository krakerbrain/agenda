# Agenda Application

## Descripción

Esta aplicación permite a cualquier negocio agendar citas, guardando el evento en el calendario de la empresa y enviando un correo de confirmación a los usuarios. La aplicación utiliza Google Calendar API y OAuth para la autenticación y la gestión de eventos. Está desarrollada en PHP y utiliza MySQL como base de datos.

## Características

- Agendar citas para cualquier negocio
- Guardar eventos en el calendario de la empresa
- Enviar correos de confirmación a los usuarios
- Utiliza Google Calendar API y OAuth para la autenticación y gestión de eventos
- Desarrollada en PHP con MySQL como base de datos

## Requisitos

- PHP 7.4 o superior
- MySQL 5.7 o superior
- Composer
- Credenciales de Google API (archivo JSON)
- Servidor web (Apache, Nginx, etc.)

## Instalación

1. Clonar el repositorio:

   git clone https://github.com/tuusuario/tu-repositorio.git
   cd tu-repositorio

2. Instalar las dependencias usando Composer:

composer install

3. Configurar las variables de entorno:

Copiar el archivo .env.example a .env y configurar las variables de entorno necesarias, incluyendo la ruta al archivo de credenciales de Google API.

cp .env.example .env
Edita el archivo .env y actualiza las siguientes variables:
GOOGLE_APPLICATION_CREDENTIALS_PATH=/ruta/a/tus/credenciales.json
DB_HOST=localhost
DB_NAME=nombre_de_tu_base_de_datos
DB_USER=tu_usuario
DB_PASSWORD=tu_contraseña

## Uso

Iniciar sesión:

Al acceder a la aplicación, se redirigirá a los usuarios para que inicien sesión con su cuenta de Google. Esto es necesario para gestionar los eventos en el calendario.

Agendar una cita:

Los usuarios pueden agendar citas seleccionando una fecha y hora disponibles. La cita se guardará en el calendario del negocio y se enviará un correo de confirmación al usuario.

## Desarrollo

Para contribuir al desarrollo de esta aplicación, sigue estos pasos:

Hacer un fork del repositorio
Crear una rama para tu nueva característica (git checkout -b feature/nueva-caracteristica)
Realizar los cambios necesarios y confirmar los cambios (git commit -m 'Agregar nueva característica')
Subir los cambios a tu repositorio (git push origin feature/nueva-caracteristica)
Crear un Pull Request

## Créditos

Esta aplicación utiliza las siguientes tecnologías y librerías:

Google API Client
PHPMailer
Dotenv
