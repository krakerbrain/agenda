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
      const response = await this.fetch(`pages/${page}.php`);
      if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

      mainContent.innerHTML = await response.text();
      this.updateUI(page);
      await this.loadModule(page);
    } catch (error) {
      console.error(`Error loading ${page}:`, error);
      mainContent.innerHTML = `<div class="error">Error al cargar la página: ${page}</div>`;
      throw error; // Propagar el error para manejo externo
    }
  }

  async loadModule(page) {
    if (this.moduleCache[page]) return;

    const modulePath = this.getModulePath(page);
    try {
      const module = await import(`${modulePath}?v=${this.APP_VERSION}`);
      this.moduleCache[page] = module;
      if (typeof module.init === "function") await module.init();
    } catch (error) {
      console.error(`Error loading module ${page}:`, error);
      throw error;
    }
  }

  getModulePath(page) {
    const paths = {
      master: "/agenda/assets/js/master_admin/",
      notificaciones: "/agenda/assets/js/navbar/notifications.js",
      default: "/agenda/assets/js/",
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
