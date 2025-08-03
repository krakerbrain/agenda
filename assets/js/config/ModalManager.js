// Clase global para manejo de modales tipo Tailwind/JS
export class ModalManager {
  static show(modalId, options = null) {
    const modal = document.getElementById(modalId);
    if (modal) {
      if (options) {
        const { title, message } = options;
        if (title) {
          const titleElement = modal.querySelector("#modalLabel");
          if (titleElement) {
            titleElement.textContent = title;
          }
        }
        if (message) {
          const messageElement = modal.querySelector("#modalMessage");
          if (messageElement) {
            messageElement.textContent = message;
          }
        }
      }
      modal.classList.remove("hidden", "opacity-0", "pointer-events-none");
      modal.classList.add("flex", "opacity-100");
      const modalContent = modal.querySelector(".bg-white");
      if (modalContent) {
        modalContent.classList.remove("scale-95", "translate-y-4", "opacity-0");
        modalContent.classList.add("scale-100", "translate-y-0", "opacity-100");
      }
      document.body.classList.add("overflow-hidden");
    }
  }

  static hide(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
      modal.classList.remove("opacity-100");
      modal.classList.add("opacity-0", "pointer-events-none");
      const modalContent = modal.querySelector(".bg-white");
      if (modalContent) {
        modalContent.classList.remove("scale-100", "translate-y-0", "opacity-100");
        modalContent.classList.add("scale-95", "translate-y-4", "opacity-0");
      }
      setTimeout(() => {
        modal.classList.add("hidden");
        document.body.classList.remove("overflow-hidden");
      }, 300);
    }
  }

  static setupCloseListeners() {
    // Cerrar con botones
    document.querySelectorAll(".close-modal, #cancelAutoOpen").forEach((button) => {
      button.addEventListener("click", function () {
        const modal = this.closest(".fixed.inset-0");
        if (modal) ModalManager.hide(modal.id);
      });
    });

    // Cerrar haciendo clic fuera
    document.querySelectorAll(".fixed.inset-0").forEach((modal) => {
      modal.addEventListener("click", function (e) {
        if (e.target === modal || e.target.classList.contains("bg-opacity-75")) {
          ModalManager.hide(modal.id);
        }
      });
    });

    // Cerrar con Escape
    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape") {
        const openModal = document.querySelector(".fixed.inset-0:not(.hidden)");
        if (openModal) ModalManager.hide(openModal.id);
      }
    });
  }
}
