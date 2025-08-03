import { ModalManager } from "./config/ModalManager.js";

export function init() {
  const buttons = document.querySelectorAll("#integrationsForm button");

  ModalManager.setupCloseListeners();
  buttons.forEach((button) => {
    button.addEventListener("click", async function (e) {
      e.preventDefault();
      const integrationId = this.dataset.integrationId;
      const enable = this.dataset.companyEnabled === "1";
      const integrationName = this.dataset.integrationName || "esta integración";

      // Si se está intentando deshabilitar, mostrar modal de confirmación con mensaje específico
      if (!enable) {
        let consequences = "";

        if (integrationId === "2") {
          // WhatsApp
          consequences = "Los clientes NO recibirán notificaciones de sus reservas.";
        } else if (integrationId === "1") {
          // Google Calendar
          consequences = "Las citas NO se sincronizarán con Google Calendar.";
        }

        ModalManager.show("acceptCancelModal", {
          title: "Confirmar acción",
          message: `
            ¿Estás seguro de que deseas deshabilitar ${integrationName}?
            ${consequences}
            Esta acción se puede revertir volviendo a activar la integración.
          `,
        });

        // Configurar el evento de confirmación
        const confirmBtn = document.getElementById("confirmActionButton");
        confirmBtn.onclick = null; // Limpiar listener previo
        confirmBtn.onclick = async () => {
          ModalManager.hide("acceptCancelModal");
          await handleIntegrationAction(integrationId, enable, integrationName);
        };
      } else {
        // Si es habilitar, proceder directamente
        await handleIntegrationAction(integrationId, enable, integrationName);
      }
    });
  });
}

async function handleIntegrationAction(integrationId, enable, integrationName) {
  try {
    let response, result;

    if (integrationId === "2") {
      // WhatsApp
      response = await fetch(`${baseUrl}user_admin/controllers/handleWhatsAppIntegration.php`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ enable: enable }),
      });

      result = await response.json();
    } else if (integrationId === "1") {
      // Google Calendar
      if (!enable) {
        response = await fetch(`${baseUrl}user_admin/controllers/disableGoogleCalendarIntegration.php`, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({ action: "disableIntegration" }),
        });
        result = await response.json();
      } else {
        // Habilitar Google Calendar (redirigir a OAuth)
        window.location.href = `${baseUrl}google_services/google_auth.php`;
        return;
      }
    }

    if (result && result.success) {
      window.location.reload();
    } else {
      console.error(result?.message || "Error desconocido");
      ModalManager.show("acceptCancelModal", {
        title: "Error",
        message: result?.message || `Ocurrió un error al modificar ${integrationName}`,
      });
    }
  } catch (error) {
    console.error("Error en la solicitud:", error);
    ModalManager.show("acceptCancelModal", {
      title: "Error",
      message: `Error al comunicarse con el servidor: ${error.message}`,
    });
  }
}
