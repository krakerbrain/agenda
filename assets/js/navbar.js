import { ConfigService } from "./config/ConfigService.js";
import { NavbarManager } from "./navigation/NavbarManager.js";
import { ContentLoader } from "./navigation/ContentLoader.js";
import { SessionStorage } from "./navigation/SessionStorage.js";
import { LogoutService } from "./navigation/LogoutService.js";
import { OffcanvasManager } from "./config/OffcanvasManager.js";

export function initializeNavbar(options = {}) {
  ConfigService.init();

  const { navLinks = document.querySelectorAll(".nav-element"), mainContent = document.getElementById("main-content"), roleId = parseInt(window.role_id) || 2, dependencies = {} } = options;

  // Configuración con defaults
  const resolvedDependencies = {
    contentLoader:
      dependencies.contentLoader ||
      new ContentLoader({
        fetch: window.fetch.bind(window),
        APP_VERSION: window.APP_VERSION,
      }),
    sessionStorage: dependencies.sessionStorage || SessionStorage,
    logoutService:
      dependencies.logoutService ||
      new LogoutService({
        baseUrl: ConfigService.baseUrl,
      }),
  };

  if (!navLinks || !mainContent) {
    console.warn("Elementos del DOM no encontrados");
    return null;
  }

  try {
    const navbarManager = new NavbarManager(resolvedDependencies);
    navbarManager.init(navLinks, mainContent, roleId);
    return navbarManager;
  } catch (error) {
    console.error("Error inicializando navbar:", error);
    // Fallback seguro
    resolvedDependencies.contentLoader.load("datesList", mainContent).catch(() => {
      mainContent.innerHTML = "<div>Error cargando contenido</div>";
    });
    return null;
  }
}

export function offCanvas() {
  // Offcanvas lateral (menú principal)
  const navOffcanvas = new OffcanvasManager({
    toggleSelector: "#offcanvasToggle",
    menuSelector: "#offcanvasMenu",
    closeSelector: "#offcanvasClose",
    backdropSelector: "#offcanvasBackdrop",
    direction: "left",
    onOpen: () => console.log("Menú principal abierto"),
    onClose: () => console.log("Menú principal cerrado"),
  });

  // Dropdown de notificaciones (manejo separado)
  const notificationDropdown = document.getElementById("notificationDropdown");
  const notificationMenu = document.getElementById("notificationMenu");

  if (notificationDropdown && notificationMenu) {
    notificationDropdown.addEventListener("click", function (e) {
      e.stopPropagation();
      notificationMenu.classList.toggle("hidden");
    });

    document.addEventListener("click", function () {
      notificationMenu.classList.add("hidden");
    });

    notificationMenu.addEventListener("click", function (e) {
      e.stopPropagation();
    });
  }

  // Cerrar offcanvas al hacer clic en cualquier enlace del menú
  const menuLinks = document.querySelectorAll("#offcanvasMenu a");
  menuLinks.forEach((link) => {
    link.addEventListener("click", () => {
      navOffcanvas.closeCanvas();
    });
  });
}

// Inicialización cuando el DOM está listo
document.addEventListener("DOMContentLoaded", () => {
  initializeNavbar();
  offCanvas();
});

// Exportaciones para testing
export const __testing__ = {
  NavbarManager,
  ContentLoader,
  SessionStorage,
  LogoutService,
  initializeNavbar,
};
