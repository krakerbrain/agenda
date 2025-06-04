import { ConfigService } from "../config/ConfigService.js"; // Ajusta el path

export class ContentLoader {
  constructor({ fetch = window.fetch.bind(window), APP_VERSION = "1.0.0" } = {}) {
    this.fetch = fetch;
    this.APP_VERSION = APP_VERSION;
    this.moduleCache = {};
  }

  async load(page, mainContent) {
    if (!page || !mainContent) {
      throw new Error("Parámetros inválidos para load");
    }

    try {
      const response = await this.fetch(`${ConfigService.baseUrl}user_admin/pages/${page}.php`, {
        headers: {
          Accept: "application/json, text/html",
        },
      });

      const contentType = response.headers.get("content-type") || "";

      // Manejar respuesta JSON (para errores o redirecciones)
      if (contentType.includes("application/json")) {
        const data = await response.json();
        if (data.redirect) {
          window.location.href = data.redirect;
          return;
        }
        throw new Error(data.error || "Error desconocido");
      }

      // Manejar respuesta HTML
      const html = await response.text();

      // Verificar si es HTML de login
      if (this.isLoginPage(html)) {
        window.location.href = `${ConfigService.baseUrl}login/index.php`;
        return;
      }

      mainContent.innerHTML = html;
      this.updateUI(page);
      await this.loadModule(page);
    } catch (error) {
      console.error(`Error loading ${page}:`, error);
      this.handleError(mainContent, page, error);
    }
  }

  isLoginPage(html) {
    const parser = new DOMParser();
    const doc = parser.parseFromString(html, "text/html");
    return !!doc.getElementById("loginForm");
  }

  handleError(mainContent, page, error) {
    // --- Enviar log al backend ---
    try {
      fetch(`${ConfigService.baseUrl}error-monitor/log_js_error.php`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          page,
          error: error.message,
          stack: error.stack,
          url: window.location.href,
          userAgent: navigator.userAgent,
          time: new Date().toISOString(),
        }),
      });
    } catch (e) {
      // No hacer nada si falla el log
    }

    if (error.message.includes("SESSION_EXPIRED") || error.message.includes("401")) {
      window.location.href = `${ConfigService.baseUrl}login/index.php`;
    } else {
      mainContent.innerHTML = `<div class="error">Error al cargar ${page}: ${error.message}</div>`;
    }
  }

  async loadModule(page) {
    // if (this.moduleCache[page]) return;

    const modulePath = this.getModulePath(page);
    try {
      // Solo usa la versión de la app, no el timestamp
      const module = await import(`${modulePath}?v=${this.APP_VERSION}`);
      this.moduleCache[page] = module;
      if (typeof module.init === "function") await module.init();
    } catch (error) {
      console.error(`Error loading module ${page}:`, error);
      throw error;
    }
  }

  getModulePath(page) {
    const base = ConfigService.baseUrl;

    const paths = {
      master: `${base}assets/js/master_admin/`,
      notificaciones: `${base}assets/js/navbar-notification/notifications.js`,
      default: `${base}assets/js/`,
    };

    if (page.startsWith("master_")) return `${paths.master}${page}.js`;
    if (page === "notificaciones") return paths.notificaciones;
    return `${paths.default}${page}.js`;
  }

  updateUI(page) {
    this.updateActiveLink(page);
    this.updatePageTitle(page);
  }

  updateActiveLink(activePageId) {
    document.querySelectorAll(".nav-link").forEach((link) => {
      link.classList.toggle("active", link.id === activePageId);
    });
  }

  updatePageTitle(page) {
    const titleElement = document.querySelector(".titulo");
    const navLink = document.querySelector(`#${page}`);

    if (titleElement && navLink) {
      titleElement.textContent = navLink.innerHTML;
    } else {
      console.warn(`No se encontró el título o el enlace para la página: ${page}`);
    }
  }
}
