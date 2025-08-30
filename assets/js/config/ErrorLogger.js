import { ConfigService } from "../config/ConfigService.js"; // Ajusta el path

export class ErrorLogger {
  constructor(context) {
    this.context = `${context}_log`; // le agregamos el sufijo automáticamente
  }

  log(message, error = null, extra = {}) {
    const payload = {
      context: this.context,
      message,
      stack: error?.stack || null,
      userAgent: navigator.userAgent,
      url: window.location.href,
      time: new Date().toISOString(),
      ...extra,
    };

    try {
      fetch(`${ConfigService.baseUrl}error-monitor/log_js_error.php`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
      });
    } catch (_) {
      // Falla en silencio, no molesta al usuario
    }
  }
}
