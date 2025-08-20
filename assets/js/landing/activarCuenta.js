import { ModalManager } from "../config/ModalManager.js";

document.querySelector("form").addEventListener("submit", async function (e) {
  e.preventDefault();

  const formData = new FormData(this);

  try {
    const response = await fetch(`${baseUrl}landing/controller/procesar_activacion.php`, {
      method: "POST",
      body: formData,
    });

    const { success, message, email } = await response.json();

    // Actualizar el href del anchor para incluir el email como parámetro
    const loginBtn = document.getElementById("goToLoginBtn");
    if (email) {
      const url = new URL(loginBtn.href);
      url.searchParams.set("email", email);
      loginBtn.href = url.toString();
    }

    // Mostrar modal usando ModalManager
    ModalManager.show(success ? "activationModal" : "infoModals", {
      title: success ? "Cuenta activada" : "Error",
      message: message || "Ocurrió un error al procesar la activación.",
    });
  } catch (error) {
    ModalManager.show("infoModal", {
      title: "Error",
      message: "Ocurrió un error inesperado. Intenta nuevamente.",
    });
  }
});

// Configurar listeners de cierre de modales
ModalManager.setupCloseListeners();
