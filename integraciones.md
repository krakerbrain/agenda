CONSULTAS

CREATE TABLE integrations (
id BIGINT PRIMARY KEY AUTO_INCREMENT,
name VARCHAR(50) NOT NULL, -- Nombre del servicio (e.g., 'Google Calendar', 'WhatsApp')
description TEXT, -- Descripción opcional del servicio
enabled BOOLEAN DEFAULT TRUE -- Activa o desactiva el servicio en general
);

CREATE TABLE company_integrations (
company_id INT NOT NULL, -- ID del usuario
integration_id BIGINT NOT NULL, -- ID del servicio
integration_data JSON, -- Información específica del servicio (e.g., tokens, ID de cuenta)
enabled BOOLEAN DEFAULT TRUE, -- Si el usuario ha activado este servicio
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
FOREIGN KEY (company_id) REFERENCES companies(id),
FOREIGN KEY (integration_id) REFERENCES integrations(id),
PRIMARY KEY (company_id, integration_id)
);

INSERT INTO `integrations`(`name`, `description`, `enabled`) VALUES ('Google Calendar','Las citas confirmadas se agregan automaticamente a tu calendario de Google',0);
INSERT INTO `integrations`(`name`, `description`, `enabled`) VALUES ('WhatsApp','Envío de mensaje de aviso cuando se reserva y cuando se confirma una cita',0);

<!-- Cambiar el id de la empresa en produccion -->

INSERT INTO `company_integrations`(`company_id`, `integration_id`, `integration_data`, `enabled`) VALUES (2,1,'[]',0);
INSERT INTO `company_integrations`(`company_id`, `integration_id`, `integration_data`, `enabled`) VALUES (2,2,'[]',1)

Autenticación de Google solo al activar la sincronización con Calendar:

Ejecutar el flujo de autenticación de Google en el momento en que el usuario elige activar Google Calendar es una excelente solución. Esto asegura que la autenticación se hace únicamente cuando el usuario realmente necesita esta funcionalidad, y evita requerir cuentas de Google para usuarios que no las usarán.
Almacenamiento y gestión segura del token de autenticación:

Cuando el usuario autentica su cuenta de Google para Calendar, se genera un token de acceso que podrías almacenar para evitar que el usuario tenga que volver a iniciar sesión cada vez que confirme una reserva. Asegúrate de almacenar este token de manera segura (encriptado en tu base de datos o en un sistema seguro).
Además, ten en cuenta que el token de Google tiene una fecha de expiración. Asegúrate de implementar una verificación para actualizar o solicitar un nuevo token antes de que expire, evitando así que la sincronización falle cuando intente usarse.
Manejo de errores en la integración de Google Calendar:

Puede suceder que, después de cierto tiempo, el token de Google requiera una renovación que no pueda completarse sin la intervención del usuario. Para estos casos, podrías implementar una notificación que avise al usuario que necesita volver a autenticar su cuenta de Google si detectas algún problema en la sincronización con el calendario.
Opción de desactivar Google Calendar:

Permitir que el usuario desactive la sincronización en cualquier momento es importante en caso de que ya no desee utilizar Google Calendar. Además, podrías implementar un mensaje de confirmación para evitar desactivaciones accidentales.
Log de eventos en tu sistema para usuarios sin Google Calendar:

Para aquellos que no quieran usar Google Calendar, considera ofrecer una alternativa interna para registrar los eventos de la reserva, o simplemente un registro que les permita ver su historial de reservas en la plataforma. Esto puede mejorar la experiencia de usuario para quienes prefieren no vincular su cuenta de Google.
Flujo claro en la interfaz de usuario:

En el panel de configuración de la cuenta, es recomendable tener una sección específica para la integración con Google Calendar que explique al usuario de manera clara los beneficios de activarla o mantenerla desactivada.

FLUJO ACTUAL

- Cuando se confirma reserva:
  se abre confirm.php y se ejecuta
  // Crear evento en Google Calendar
  $eventId = createCalendarEvent(
        $client,
        $appointment['name'],
        $appointment['service'],
        $startDateTimeFormatted,
        $endDateTimeFormatted,
        $appointment['id']
    );
en archivo calendar_service.php
se ejecuta createCalendarEvent donde se intenta obtener el token desde
// Obtener el token de integración de Google Calendar
    $tokenData = $integrationManager->getIntegrationToken($companyId, $client);
  en el metodo getIntegrationToken se busca la integracion y el token si no se encuentra se devuelve null si se encuentras se devuelve el token

POR QUE NO SIRVE EL CODIGO QUE HABIA HECHO? PORQUE A GOOGLE AUTH DEBES ENTRAR DESDE EL FRONTEND. hay que recibir una respuiesta y redirigir

1. Verificar la Habilitación de Google Calendar
   Probar cuando Google Calendar está habilitado:
   Asegúrate de que la funcionalidad de Google Calendar solo se activa cuando el usuario ha habilitado la integración.
   Verifica que el sistema maneja correctamente la activación de Google Calendar en los pasos de confirmación y eliminación de reservas.

   Prueba: Al habilitar Google Calendar, realiza una reserva y verifica que la integración funcione correctamente, creando el evento en Google Calendar.

   Probar cuando Google Calendar NO está habilitado:
   Verifica que si el usuario no ha habilitado la integración, las funciones de confirmación o eliminación de eventos en Google Calendar no se ejecuten.

   Prueba: Desactiva Google Calendar y trata de confirmar o eliminar una cita, asegurándote de que no se intente interactuar con Google Calendar.

2. Probar el Token de Google
   Verificar si el token se corrompe o expira:
   Verifica si el token de acceso se corrompe o se invalida después de varias interacciones (por ejemplo, si el token expira y no se refresca correctamente).

   Prueba: Cambia la hora del sistema o simula que el token ha expirado y verifica que el flujo de actualización del token se ejecute correctamente.
   Prueba: Verifica si el sistema detecta la expiración del token y lo refresca correctamente cuando el usuario realiza una acción que requiere el token.

3. Verificar el Refresco del Token
   Probar si el token se refresca correctamente:
   Verifica que el proceso de actualización del token (refresh) funcione bien cuando el token expira.

   Prueba: Asegúrate de que el token se actualice automáticamente en la base de datos después de que el sistema lo refresque (cuando sea necesario).

   Verifica que después de la actualización, el token siga funcionando como se espera (la integración con Google Calendar debe continuar sin problemas).

4. Probar la Columna integration_data en la Base de Datos
   Verificar si la columna integration_data se actualiza correctamente:
   Verifica que los datos en integration_data se mantengan actualizados después de una integración exitosa con Google Calendar.

   Prueba: Actualiza o refresca el token en el sistema y asegúrate de que los datos en la columna integration_data se actualicen correctamente.
   Verificar si integration_data se borra correctamente:
   Si el usuario desactiva Google Calendar o se elimina la integración, verifica que los datos en integration_data se borren correctamente y no queden datos antiguos o corruptos.
   Prueba: Desactiva Google Calendar y asegúrate de que la columna integration_data se vacíe correctamente (quede como null o en su estado predeterminado).

5. Verificar Comportamiento en la Confirmación de Reservas
   Verificar la creación del evento en Google Calendar:
   Si la integración está habilitada, asegúrate de que el evento se cree correctamente en Google Calendar cuando se confirma una cita.
   Prueba: Confirmar una reserva y verificar que el evento se haya creado correctamente en Google Calendar.
   Prueba: Verifica que los datos del evento en Google Calendar coincidan con los de la base de datos (por ejemplo, que la fecha, hora y detalles de la cita sean correctos).
   Verificar si no hay interacción con Google Calendar cuando no está habilitado:
   Si la integración no está habilitada, asegúrate de que no se intente crear ni eliminar eventos en Google Calendar al confirmar o eliminar la reserva.
   Prueba: Realiza una reserva sin Google Calendar habilitado y verifica que no se haga ninguna solicitud a la API de Google Calendar.
6. Verificar la Eliminación de Reservas
   Probar la eliminación de un evento en Google Calendar:
   Asegúrate de que al eliminar una reserva, el evento correspondiente también se elimine correctamente en Google Calendar.
   Prueba: Elimina una cita y verifica que el evento se haya borrado en Google Calendar correctamente.
   Verificar la eliminación cuando la integración no está habilitada:
   Si Google Calendar no está habilitado, asegúrate de que no se haga ninguna solicitud para eliminar eventos de Google Calendar.
   Prueba: Intenta eliminar una cita sin Google Calendar habilitado y asegúrate de que no se intente eliminar el evento en Google Calendar.
7. Verificación de Logs y Errores
   Comprobar logs y mensajes de error:
   Verifica que los logs de acción proporcionen suficiente información cuando se habilita/deshabilita Google Calendar, se crea/elimina un evento, o se actualiza el token.
   Prueba: Revisa los logs generados durante el proceso de habilitación de la integración, confirmación de reservas, eliminación de citas, etc.
   Probar manejo de errores:
   Asegúrate de que cualquier error relacionado con la integración (por ejemplo, fallos en la API de Google Calendar, problemas con el token, etc.) se maneje adecuadamente y se notifique al usuario.
   Prueba: Simula errores de la API de Google Calendar y verifica que se manejen correctamente, sin afectar la experiencia del usuario.
8. Pruebas de Seguridad
   Verificar acceso no autorizado:
   Si el usuario deshabilita la integración, asegúrate de que no haya intentos de acceso o modificación de los eventos de Google Calendar sin la debida autorización.
   Prueba: Realiza acciones de reserva y eliminación sin un token válido o cuando la integración está deshabilitada, y verifica que no haya interacción con la API de Google Calendar.
   Resúmen de pruebas a realizar:

Verificar habilitación/deshabilitación de la integración de Google Calendar.
Confirmar que el token se refresca correctamente y no se corrompe.
Asegurar que integration_data se actualiza y borra correctamente.
Verificar que las interacciones con Google Calendar solo suceden cuando está habilitado.
Probar la creación y eliminación de eventos en Google Calendar.
Comprobar que se manejen adecuadamente los errores y logs.
Si cubres estos puntos, podrás asegurarte de que todo el flujo de integración con Google Calendar funcione correctamente en todos los casos.
