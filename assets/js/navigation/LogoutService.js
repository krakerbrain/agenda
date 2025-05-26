import { ConfigService } from "../config/ConfigService.js";

export class LogoutService {
  constructor({ baseUrl = ConfigService.baseUrl, redirect = () => window.location.replace(`${ConfigService.baseUrl}index.php`), fetch = window.fetch.bind(window) } = {}) {
    this.baseUrl = baseUrl;
    this.redirect = redirect;
    this.fetch = fetch;
  }

  async logout() {
    try {
      const response = await this.fetch(`${this.baseUrl}login/logout.php`, {
        method: "POST",
        credentials: "include",
      });

      if (!response.ok) throw new Error(`Logout failed with status ${response.status}`);

      const data = await response.json();
      sessionStorage.removeItem("lastPage");

      if (data.redirect) {
        window.location.href = data.redirect;
      } else {
        this.redirect();
      }
    } catch (error) {
      console.error("Logout error:", error);
      this.redirect(); // Fallback seguro
      throw error;
    }
  }
}
