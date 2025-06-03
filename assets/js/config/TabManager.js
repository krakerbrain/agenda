export class TabManager {
  /**
   * @param {string} tabContainerSelector - Selector del contenedor de tabs
   * @param {function} onTabChange - Callback que se ejecuta al cambiar de tab
   * @param {string} storageKey - Clave para guardar en sessionStorage
   * @param {string} defaultTab - Tab por defecto si no hay uno guardado
   */
  constructor({ tabContainerSelector, onTabChange = null, storageKey = "tabStatus", defaultTab = "", resetFormSelector = null }) {
    this.tabContainerSelector = tabContainerSelector;
    this.onTabChange = onTabChange;
    this.storageKey = storageKey;
    this.defaultTab = defaultTab;
    this.resetFormSelector = resetFormSelector;
    this.init();
  }

  init() {
    // Permitir selectores flexibles: botones pueden tener data-bs-target o data-tab-target
    const tabButtons = document.querySelectorAll(`${this.tabContainerSelector} [data-tab-target], ${this.tabContainerSelector} [data-bs-target]`);
    const savedTab = sessionStorage.getItem(this.storageKey) || this.defaultTab;

    if (tabButtons.length) {
      tabButtons.forEach((button) => {
        // Soporta ambos atributos para compatibilidad
        const tabTarget = button.dataset.tabTarget || (button.dataset.bsTarget ? button.dataset.bsTarget.replace("#", "") : "");

        // Activar tab guardado
        if (tabTarget === savedTab) {
          this.setActiveTab(tabTarget, false);
        }

        // Manejar clicks
        button.addEventListener("click", (event) => {
          event.preventDefault();
          this.handleTabClick(event, tabTarget);
        });
      });
    }
  }

  handleTabClick(event, tabTarget) {
    if (this.resetFormSelector) {
      document.querySelector(this.resetFormSelector)?.reset();
    }

    this.setActiveTab(tabTarget);

    if (typeof this.onTabChange === "function") {
      this.onTabChange(tabTarget);
    }
  }

  setActiveTab(tabTarget, saveToStorage = true) {
    const tabButtons = document.querySelectorAll(`${this.tabContainerSelector} [data-tab-target], ${this.tabContainerSelector} [data-bs-target]`);
    tabButtons.forEach((button) => {
      // Soporta ambos atributos
      const btnTabTarget = button.dataset.tabTarget || (button.dataset.bsTarget ? button.dataset.bsTarget.replace("#", "") : "");
      const parentItem = button.closest("li, .tab-item");
      if (parentItem) {
        if (btnTabTarget === tabTarget) {
          parentItem.classList.add("active", "border-b-2", "border-blue-500");
          button.setAttribute("aria-selected", "true");
        } else {
          parentItem.classList.remove("active", "border-b-2", "border-blue-500");
          button.setAttribute("aria-selected", "false");
        }
      }
    });
    if (saveToStorage) {
      sessionStorage.setItem(this.storageKey, tabTarget);
    }
  }
}
