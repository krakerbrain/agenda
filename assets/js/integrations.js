export function initIntegrations() {
  // Selecciona todos los botones de habilitar/deshabilitar en la tabla
  const buttons = document.querySelectorAll("#integrationsForm button");

  buttons.forEach((button) => {
    button.addEventListener("click", async function () {
      const companyEnabled = this.dataset.companyEnabled;

      if (!companyEnabled) {
        // Si `data-company-enabled` está vacío, realiza la solicitud async para deshabilitar
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
            // Recarga la página después de la validación
            window.location.reload();
            console.log("Integración deshabilitada correctamente.");
          } else {
            console.error("Error al deshabilitar la integración.");
          }
        } catch (error) {
          console.error("Error en la solicitud:", error);
        }
      } else {
        // Redirige al usuario después de la validación
        window.location.href = `${baseUrl}google_services/google_auth.php`;
      }
    });
  });
}
