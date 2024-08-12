export function initCorreos() {
  const formReserva = document.getElementById("reservaForm");
  const formConfirmacion = document.getElementById("confirmacionForm");

  async function getEmailTemplates() {
    const response = await fetch(`${baseUrl}user_admin/controllers/correosController.php`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        action: "getTemplates",
      }),
    });

    const { success, data } = await response.json();

    if (success && data.length > 0) {
      // Limpiar los campos del formulario
      formReserva.reset();
      formConfirmacion.reset();

      data.forEach((template) => {
        if (template.template_name === "Reserva") {
          formReserva.querySelector('input[name="subject"]').value = template.subject;
          formReserva.querySelector('textarea[name="body"]').value = template.body;
        } else if (template.template_name === "Confirmación") {
          formConfirmacion.querySelector('input[name="subject"]').value = template.subject;
          formConfirmacion.querySelector('textarea[name="body"]').value = template.body;
        }
      });
    }
  }

  getEmailTemplates();

  formReserva.addEventListener("submit", async (event) => {
    event.preventDefault();

    const subject = formReserva.querySelector('input[name="subject"]').value;
    const body = formReserva.querySelector('textarea[name="body"]').value;

    const response = await fetch(`${baseUrl}user_admin/controllers/correosController.php`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        template_name: "Reserva",
        subject,
        body,
        action: "saveTemplate",
      }),
    });

    const { success } = await response.json();

    if (success) {
      alert("Correo de reserva enviado exitosamente");
    } else {
      alert("Error al enviar el correo de reserva");
    }
  });

  formConfirmacion.addEventListener("submit", async (event) => {
    event.preventDefault();

    const subject = formConfirmacion.querySelector('input[name="subject"]').value;
    const body = formConfirmacion.querySelector('textarea[name="body"]').value;

    const response = await fetch(`${baseUrl}user_admin/controllers/correosController.php`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        template_name: "Confirmación",
        subject,
        body,
        action: "saveTemplate",
      }),
    });

    const { success } = await response.json();

    if (success) {
      alert("Correo de confirmación enviado exitosamente");
    } else {
      alert("Error al enviar el correo de confirmación");
    }
  });
}
