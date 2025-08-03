// Clase para gestionar spinners de acción en botones o íconos
export class SpinnerManager {
  /**
   * Muestra un spinner en el contenedor indicado y oculta el contenido original.
   * @param {string} containerId - El id del contenedor (div) donde está el botón o ícono.
   * @param {Object} [options]
   * @param {string} [options.spinnerColor] - Color opcional del spinner.
   * @param {string} [options.size] - Tamaño opcional del spinner (ej: '1.5rem').
   */
  static show(containerId, options = {}) {
    const container = document.getElementById(containerId);
    if (!container) return;
    // Oculta todos los hijos (botón, ícono, texto, etc)
    Array.from(container.children).forEach((child) => (child.style.display = "none"));
    // Si ya hay un spinner, no lo agregues de nuevo
    if (container.querySelector(".spinner-manager")) return;
    // Crea el spinner
    const spinner = document.createElement("span");
    spinner.className = "spinner-manager";
    spinner.innerHTML = `
      <svg class="animate-spin" style="width:${options.size || "1.5rem"};height:${options.size || "1.5rem"};color:${
      options.spinnerColor || "#2563eb"
    }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
    `;
    spinner.style.display = "inline-block";
    spinner.style.verticalAlign = "middle";
    container.appendChild(spinner);
  }

  /**
   * Oculta el spinner y muestra el contenido original del contenedor.
   * @param {string} containerId - El id del contenedor (div) donde está el botón o ícono.
   */
  static hide(containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;
    // Elimina el spinner
    const spinner = container.querySelector(".spinner-manager");
    if (spinner) spinner.remove();
    // Muestra los hijos originales
    Array.from(container.children).forEach((child) => (child.style.display = ""));
  }
}
