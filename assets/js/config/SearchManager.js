// Clase global para lógica de búsqueda y autocompletado reutilizable
export class SearchManager {
  /**
   * @param {string} formSelector - Selector del formulario de búsqueda
   * @param {function} onSearch - Callback al hacer submit
   * @param {function} onAutocomplete - Callback al autocompletar
   * @param {Array<string>} autocompleteFields - IDs de los campos a escuchar para autocompletar
   */
  constructor(formSelector, onSearch, onAutocomplete, autocompleteFields = []) {
    this.form = document.querySelector(formSelector);
    this.onSearch = onSearch;
    this.onAutocomplete = onAutocomplete;
    this.autocompleteFields = autocompleteFields;
    this.init();
  }

  init() {
    if (!this.form) return;

    // Submit
    this.form.addEventListener("submit", (e) => {
      e.preventDefault();
      const formData = new FormData(this.form);
      if (typeof this.onSearch === "function") {
        this.onSearch(formData);
      }
    });

    // Autocompletado
    this.autocompleteFields.forEach((id) => {
      const input = this.form.querySelector(`#${id}`);
      if (input) {
        input.addEventListener("input", (e) => {
          // Validar si hay más de un input lleno
          const filledInputs = Array.from(this.form.elements)
            .filter((el) => ["input", "select"].includes(el.tagName.toLowerCase()))
            .filter((el) => el.value);

          if (filledInputs.length > 2) {
            return; // No hacer nada si hay más de un input lleno
          }

          if (typeof this.onAutocomplete === "function") {
            this.onAutocomplete(e);
          }
        });
      }
    });
  }
}
