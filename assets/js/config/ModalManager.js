// Clase global para manejo de modales tipo Tailwind/JS
export class ModalManager {
  static show(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
      modal.classList.remove("hidden", "opacity-0", "pointer-events-none");
      modal.classList.add("opacity-100");
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
}
