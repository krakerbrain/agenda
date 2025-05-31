// Clase para la gestión de tabs y navegación
export class DatesTabManager {
  constructor(tabSelector, onTabChange) {
    this.tabSelector = tabSelector;
    this.onTabChange = onTabChange;
    this.init();
  }

  init() {
    const triggerTabList = document.querySelectorAll(this.tabSelector);
    const savedStatus = sessionStorage.getItem("status") || "unconfirmed";
    if (triggerTabList) {
      triggerTabList.forEach((triggerEl) => {
        const status = triggerEl.dataset.bsTarget.substring(1);
        if (status === savedStatus) {
          triggerTabList.forEach((btn) => btn.parentElement.classList.remove("active"));
          triggerEl.parentElement.classList.add("active");
        }
        triggerEl.addEventListener("click", (event) => {
          event.preventDefault();
          document.querySelector("#searchForm").reset();
          const newStatus = triggerEl.dataset.bsTarget.substring(1);
          sessionStorage.setItem("status", newStatus);
          triggerTabList.forEach((btn) => btn.parentElement.classList.remove("active"));
          triggerEl.parentElement.classList.add("active");
          if (typeof this.onTabChange === "function") {
            this.onTabChange(newStatus);
          }
        });
      });
    }
  }

  setActiveTab(status) {
    const triggerTabList = document.querySelectorAll(this.tabSelector);
    triggerTabList.forEach((btn) => btn.parentElement.classList.remove("active"));
    const targetBtn = Array.from(triggerTabList).find((btn) => btn.dataset.bsTarget && btn.dataset.bsTarget.substring(1) === status);
    if (targetBtn) {
      targetBtn.parentElement.classList.add("active");
      sessionStorage.setItem("status", status);
      if (typeof this.onTabChange === "function") {
        this.onTabChange(status);
      }
    }
  }
}
