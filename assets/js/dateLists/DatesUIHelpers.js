// Clase para helpers de UI y modales
export class DatesUIHelpers {
  static getStatusBadge(status) {
    const statusMap = {
      0: { text: "Pendiente", class: "bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs" },
      1: { text: "Confirmada", class: "bg-green-100 text-green-800 px-2 py-1 rounded text-xs" },
    };
    const statusInfo = statusMap[status] || { text: "Desconocido", class: "bg-gray-200 text-gray-700 px-2 py-1 rounded text-xs" };
    return `<span class="${statusInfo.class}">${statusInfo.text}</span>`;
  }

  static showModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
      modal.classList.remove("hidden");
      void modal.offsetWidth;
      modal.classList.remove("opacity-0");
      const backdrop = modal.querySelector(".fixed.inset-0");
      if (backdrop) backdrop.classList.remove("bg-opacity-0");
      const modalContent = modal.querySelector(".transform");
      if (modalContent) {
        modalContent.classList.remove("opacity-0", "translate-y-4", "sm:translate-y-0", "sm:scale-95");
      }
      document.body.classList.add("overflow-hidden");
    }
  }

  static hideModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
      modal.classList.add("opacity-0");
      const backdrop = modal.querySelector(".fixed.inset-0");
      if (backdrop) backdrop.classList.add("bg-opacity-0");
      const modalContent = modal.querySelector(".transform");
      if (modalContent) {
        modalContent.classList.add("opacity-0", "translate-y-4", "sm:translate-y-0", "sm:scale-95");
      }
      setTimeout(() => {
        modal.classList.add("hidden");
        document.body.classList.remove("overflow-hidden");
      }, 300);
    }
  }
}
