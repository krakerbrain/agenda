/**
 * Centraliza configuraciones de la aplicación
 */
export class ConfigService {
  static #config = {
    baseUrl: null,
  };

  static init() {
    // Obtiene la URL del data-attribute del body
    this.#config.baseUrl = document.body?.dataset?.baseUrl || "/";

    // Validación y sanitización
    this.#config.baseUrl = this.#sanitizeUrl(this.#config.baseUrl);
  }

  static get baseUrl() {
    if (!this.#config.baseUrl) {
      throw new Error("ConfigService no inicializado. Llama a ConfigService.init() primero");
    }
    return this.#config.baseUrl;
  }

  static #sanitizeUrl(url) {
    if (!url.endsWith("/")) {
      return `${url}/`;
    }
    return url;
  }
}
