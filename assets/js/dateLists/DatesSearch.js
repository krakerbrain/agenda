// Clase para lógica de búsqueda y autocompletado
export class DatesSearch {
  constructor(formSelector, onSearch, onAutocomplete) {
    this.form = document.querySelector(formSelector);
    this.onSearch = onSearch;
    this.onAutocomplete = onAutocomplete;
    this.init();
  }

  init() {
    if (!this.form) return;
    this.form.addEventListener("submit", (e) => {
      e.preventDefault();
      const formData = new FormData(this.form);
      if (typeof this.onSearch === "function") {
        this.onSearch(formData);
      }
    });
    // Autocompletado en los campos
    ["service", "name", "phone", "mail", "date", "hour", "status"].forEach((id) => {
      const input = this.form.querySelector(`#${id}`);
      if (input) {
        input.addEventListener("input", (e) => {
          if (typeof this.onAutocomplete === "function") {
            this.onAutocomplete(e);
          }
        });
      }
    });
  }
}
