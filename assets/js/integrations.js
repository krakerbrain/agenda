export function init() {
  const buttons = document.querySelectorAll("#integrationsForm button");

  buttons.forEach((button) => {
    button.addEventListener("click", async function () {
      const integrationId = this.dataset.integrationId;
      const enable = this.dataset.companyEnabled === "1"; // Cambiado a comparar con '1' que es el formato correcto

      if (integrationId === "2") {
        // WhatsApp
        try {
          const response = await fetch(`${baseUrl}user_admin/controllers/handleWhatsAppIntegration.php`, {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
            },
            body: JSON.stringify({ enable: enable }),
          });

          const result = await response.json();

          if (result.success) {
            window.location.reload();
          } else {
            console.error(result.message);
            alert(result.message); // Mostrar error al usuario
          }
        } catch (error) {
          console.error("Error en la solicitud:", error);
          alert("Error al comunicarse con el servidor");
        }
      } else if (integrationId === "1") {
        // Google Calendar
        if (!enable) {
          // Deshabilitar Google Calendar
          try {
            const response = await fetch(`${baseUrl}user_admin/controllers/disableGoogleCalendarIntegration.php`, {
              method: "POST",
              headers: {
                "Content-Type": "application/json",
              },
              body: JSON.stringify({ action: "disableIntegration" }),
            });

            const result = await response.json();

            if (result.success) {
              window.location.reload();
            } else {
              console.error(result.message);
              alert(result.message);
            }
          } catch (error) {
            console.error("Error en la solicitud:", error);
            alert("Error al comunicarse con el servidor");
          }
        } else {
          // Habilitar Google Calendar (redirigir a OAuth)
          window.location.href = `${baseUrl}google_services/google_auth.php`;
        }
      }
    });
  });
}
