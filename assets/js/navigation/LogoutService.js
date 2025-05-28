import { ConfigService } from "../config/ConfigService.js";

export class LogoutService {
  constructor({ baseUrl = ConfigService.baseUrl } = {}) {
    this.baseUrl = baseUrl;
  }

  logout() {
    // Elimina la última página guardada en sessionStorage antes de redirigir
    sessionStorage.removeItem("lastPage");
    window.location.href = `${this.baseUrl}login/logout.php`;
  }
}
