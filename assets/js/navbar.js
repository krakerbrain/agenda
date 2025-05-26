import { ConfigService } from "./config/ConfigService.js";
import { NavbarManager } from "./navigation/NavbarManager.js";
import { ContentLoader } from "./navigation/ContentLoader.js";
import { SessionStorage } from "./navigation/SessionStorage.js";
import { LogoutService } from "./navigation/LogoutService.js";

export function initializeNavbar(options = {}) {
  ConfigService.init();

  const { navLinks = document.querySelectorAll(".nav-link"), mainContent = document.getElementById("main-content"), roleId = parseInt(window.role_id) || 2, dependencies = {} } = options;

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

// Inicialización cuando el DOM está listo
document.addEventListener("DOMContentLoaded", () => {
  initializeNavbar();
});

// Exportaciones para testing
export const __testing__ = {
  NavbarManager,
  ContentLoader,
  SessionStorage,
  LogoutService,
  initializeNavbar,
};
