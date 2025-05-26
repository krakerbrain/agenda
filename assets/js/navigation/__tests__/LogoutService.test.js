import { LogoutService } from "../LogoutService.js";
import { ConfigService } from "../../config/ConfigService.js";

describe("LogoutService", () => {
  let logoutService;
  let mockFetch;
  let mockRedirect;
  let originalLocation;

  beforeAll(() => {
    ConfigService.init({
      baseUrl: "http://localhost/agenda", // O la URL que uses en test
      // otras configuraciones necesarias
    });
  });

  beforeEach(() => {
    // Mock de fetch
    mockFetch = jest.fn();

    // Mock de redirect (fallback)
    mockRedirect = jest.fn();

    // Guardar original window.location para restaurar luego
    originalLocation = window.location;

    // Mock de window.location.href (define propiedad)
    delete window.location;
    window.location = { href: "" };

    // Instancia de LogoutService con mocks
    logoutService = new LogoutService({
      fetch: mockFetch,
      redirect: mockRedirect,
      baseUrl: "https://fakeurl.com/",
    });

    // Limpiar sessionStorage
    sessionStorage.clear();
  });

  afterEach(() => {
    // Restaurar window.location original
    window.location = originalLocation;
  });

  // Aquí irán los tests...
  describe("LogoutService.logout", () => {
    beforeEach(() => {
      // Limpio sessionStorage antes de cada test
      sessionStorage.clear();
      // Mockeo window.location.href y redirect
      delete window.location;
      window.location = { href: "" };
    });

    it("debería eliminar 'lastPage' y redirigir con URL recibida", async () => {
      // Preparo sessionStorage con 'lastPage'
      sessionStorage.setItem("lastPage", "algún valor");

      // Mock de fetch exitoso
      global.fetch = jest.fn(() =>
        Promise.resolve({
          ok: true,
          json: () => Promise.resolve({ redirect: "https://redirect.com" }),
        })
      );

      const logoutService = new LogoutService();

      await logoutService.logout();

      expect(sessionStorage.getItem("lastPage")).toBeNull();
      expect(window.location.href).toBe("https://redirect.com");
    });

    it("debería llamar redirect fallback si no hay redirect en respuesta", async () => {
      sessionStorage.setItem("lastPage", "algún valor");

      global.fetch = jest.fn(() =>
        Promise.resolve({
          ok: true,
          json: () => Promise.resolve({}),
        })
      );

      const mockRedirect = jest.fn();
      const logoutService = new LogoutService({ redirect: mockRedirect });

      await logoutService.logout();

      expect(sessionStorage.getItem("lastPage")).toBeNull();
      expect(mockRedirect).toHaveBeenCalled();
    });

    it("debería lanzar error y llamar redirect fallback si fetch responde con error HTTP", async () => {
      sessionStorage.setItem("lastPage", "algún valor");

      global.fetch = jest.fn(() =>
        Promise.resolve({
          ok: false,
          status: 500,
        })
      );

      const mockRedirect = jest.fn();
      const logoutService = new LogoutService({ redirect: mockRedirect });

      await expect(logoutService.logout()).rejects.toThrow("Logout failed with status 500");
      expect(mockRedirect).toHaveBeenCalled();
    });

    it("debería lanzar error y llamar redirect fallback si fetch lanza excepción", async () => {
      sessionStorage.setItem("lastPage", "algún valor");

      global.fetch = jest.fn(() => Promise.reject(new Error("Network error")));

      const mockRedirect = jest.fn();
      const logoutService = new LogoutService({ redirect: mockRedirect });

      await expect(logoutService.logout()).rejects.toThrow("Network error");
      expect(mockRedirect).toHaveBeenCalled();
    });
  });
});
