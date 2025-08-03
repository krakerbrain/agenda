/**
 * Carga un icono SVG desde la carpeta de iconos
 * @param {string} name - Nombre del archivo SVG (sin extensión)
 * @param {string} [className] - Clases adicionales para el SVG
 * @returns {Promise<string>} SVG como string
 */
export async function loadIcon(name, className = "w-4 h-4") {
  try {
    const response = await fetch(`../assets/icons/${name}.svg`);
    if (!response.ok) throw new Error("Icon not found");
    const svgText = await response.text();
    const svgElement = new DOMParser().parseFromString(svgText, "image/svg+xml").querySelector("svg");

    if (className) {
      svgElement.classList.add(...className.split(" "));
    }

    return svgElement.outerHTML;
  } catch (error) {
    console.error(`Error loading icon ${name}:`, error);
    return "";
  }
}

// Iconos predefinidos para notificaciones
export const NOTIFICATION_ICONS = {
  feature: "star",
  bugfix: "bug",
  announcement: "megaphone",
  default: "bell",
};

// Iconos para toasts
export const TOAST_ICONS = {
  success: "check-circle",
  error: "x-circle",
  close: "x",
};
