// Clase para la lógica de paginación
export class DatesPagination {
  constructor(prevSelector, nextSelector, currentSelector, onPageChange) {
    this.prevBtn = document.getElementById(prevSelector);
    this.nextBtn = document.getElementById(nextSelector);
    this.currentPageEl = document.getElementById(currentSelector);
    this.currentPage = 1;
    this.onPageChange = onPageChange;
    this.init();
  }

  init() {
    if (this.prevBtn) {
      this.prevBtn.addEventListener("click", () => {
        if (this.currentPage > 1) {
          this.setPage(this.currentPage - 1);
        }
      });
    }
    if (this.nextBtn) {
      this.nextBtn.addEventListener("click", () => {
        this.setPage(this.currentPage + 1);
      });
    }
  }

  setPage(page) {
    this.currentPage = page;
    if (typeof this.onPageChange === "function") {
      this.onPageChange(page);
    }
    this.updateControls();
  }

  updateControls(hasMoreData = true) {
    if (this.currentPageEl) {
      this.currentPageEl.innerText = `Página ${this.currentPage}`;
    }
    if (this.prevBtn) {
      this.prevBtn.disabled = this.currentPage === 1;
    }
    if (this.nextBtn) {
      this.nextBtn.disabled = !hasMoreData;
    }
  }
}
