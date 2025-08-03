// Pagination global reutilizable
export class Pagination {
  /**
   * @param {string} prevBtnId - ID del botón anterior
   * @param {string} nextBtnId - ID del botón siguiente
   * @param {string} currentPageId - ID del span/elemento de página actual
   * @param {function} onPageChange - Callback al cambiar de página (pageNumber)
   */
  constructor(prevBtnId, nextBtnId, currentPageId, onPageChange) {
    this.prevBtn = document.getElementById(prevBtnId);
    this.nextBtn = document.getElementById(nextBtnId);
    this.currentPageEl = document.getElementById(currentPageId);
    this.onPageChange = onPageChange;
    this.currentPage = 1;
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
    if (this.currentPageEl) {
      this.currentPageEl.innerText = `Página ${this.currentPage}`;
    }
    if (typeof this.onPageChange === "function") {
      this.onPageChange(this.currentPage);
    }
  }

  updateControls(hasMoreData) {
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
