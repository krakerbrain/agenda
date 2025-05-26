export class NavbarManager {
  constructor({ contentLoader, sessionStorage, logoutService, offcanvasId = "offcanvasMenu" }) {
    this.contentLoader = contentLoader || console.error("ContentLoader es requerido");
    this.sessionStorage = sessionStorage || console.error("SessionStorage es requerido");
    this.logoutService = logoutService || console.error("LogoutService es requerido");
    this.offcanvasId = offcanvasId;
    this.bsOffcanvas = null;
  }

  init(navLinks, mainContent, roleId = 2) {
    if (!navLinks || !mainContent) {
      throw new Error("Elementos del DOM requeridos no encontrados");
    }
    // Inicializar offcanvas si existe
    this.initializeOffcanvas();

    const lastPage = this.sessionStorage.getItem("lastPage");
    this.setupNavLinks(navLinks, mainContent, roleId, lastPage);
  }

  initializeOffcanvas() {
    if (typeof bootstrap !== "undefined" && this.offcanvasId) {
      const offcanvasElement = document.getElementById(this.offcanvasId);
      if (offcanvasElement) {
        this.bsOffcanvas = bootstrap.Offcanvas.getOrCreateInstance(offcanvasElement);
      }
    }
  }

  setupNavLinks(navLinks, mainContent, roleId, lastPage) {
    const initialPage = lastPage || this.getDefaultLink(navLinks, roleId);
    this.contentLoader.load(initialPage, mainContent);

    navLinks.forEach((link) => {
      link.addEventListener("click", (event) => this.handleNavClick(event, link, mainContent));
    });
  }

  getDefaultLink(navLinks, roleId) {
    const defaultLinks = {
      1: "master_add_company",
      2: "datesList",
    };
    return Array.from(navLinks).find((link) => link.id !== "logout")?.id || defaultLinks[roleId];
  }

  handleNavClick(event, link, mainContent) {
    event.preventDefault();
    const page = link.id;

    this.hideOffcanvas();

    if (page === "logout") {
      this.logoutService.logout();
    } else {
      this.contentLoader.load(page, mainContent);
      this.sessionStorage.setItem("lastPage", page);
    }
  }

  hideOffcanvas() {
    if (this.bsOffcanvas) {
      this.bsOffcanvas.hide();
    }
  }
}
