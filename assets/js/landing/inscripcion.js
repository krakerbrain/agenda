import { ModalManager } from "../config/ModalManager.js";

const overlay = document.querySelector("#loadingOverlay");

document.querySelector("#companyForm").addEventListener("submit", async function (e) {
  e.preventDefault();

  // Mostrar overlay
  overlay.classList.remove("hidden");
  overlay.classList.add("flex");

  const formData = new FormData(this);

  try {
    const response = await fetch(`${baseUrl}inscripcion/controller/procesar_inscripcion.php`, {
      method: "POST",
      body: formData,
    });

    const { success, message } = await response.json();

    // Ocultar overlay
    overlay.classList.add("hidden");
    overlay.classList.remove("flex");

    // Mostrar modal usando ModalManager
    ModalManager.show("infoModal", {
      title: success ? "Éxito" : "Error",
      message: message || (success ? "Te hemos enviado un correo de activación." : "Hubo un error en el proceso."),
    });
  } catch (error) {
    overlay.classList.add("hidden");
    overlay.classList.remove("flex");

    ModalManager.show("infoModal", {
      title: "Error",
      message: "Error inesperado. Intenta nuevamente.",
    });
  }
});

// Configuración de cierre de modales
ModalManager.setupCloseListeners();
