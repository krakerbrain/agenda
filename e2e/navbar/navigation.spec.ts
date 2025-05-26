import { test, expect } from "@playwright/test";
import dotenv from "dotenv";
dotenv.config();

// Configuración base
const LOGIN_URL = process.env.E2E_LOGIN_URL!;
const JS_BASE_URL = process.env.E2E_JS_BASE_URL!;
const USER = process.env.E2E_USER!;
const PASS = process.env.E2E_PASS!;

// Lista de módulos JS y sus IDs (links) en el menú
const PAGES = ["datesList", "clientes", "horarios", "servicios", "notificaciones"];

test.describe("Login y verificación global de módulos JS en navegación", () => {
  test.beforeEach(async ({ page }) => {
    await page.goto(LOGIN_URL);

    // Login (ajusta los selectores y credenciales según tu app)
    await page.fill("#usuario", USER);
    await page.fill("#contrasenia", PASS);
    await Promise.all([page.waitForURL("**/user_admin/index.php"), page.click('input[type="submit"]')]);

    await page.waitForSelector('[data-bs-toggle="offcanvas"]', { state: "visible" });
    await page.click('[data-bs-toggle="offcanvas"]');
    await page.waitForSelector(".nav-link", { state: "visible" });
  });

  test("Todos los archivos JS deben existir o mostrar cuáles faltan", async ({ page, request }) => {
    const missingFiles: string[] = [];

    for (const pageId of PAGES) {
      // Verificar que el link exista para ese pageId
      const link = await page.locator(`#${pageId}`);
      if ((await link.count()) === 0) {
        // Si no existe el link, saltar (o puedes opcionalmente reportarlo)
        continue;
      }

      let jsUrl;
      if (pageId === "notificaciones") {
        jsUrl = `${JS_BASE_URL}navbar-notification/notifications.js`;
      } else {
        jsUrl = `${JS_BASE_URL}${pageId}.js`;
      }

      const response = await request.get(jsUrl);
      if (response.status() !== 200) {
        missingFiles.push(jsUrl);
      }
    }

    if (missingFiles.length > 0) {
      console.error("❌ Los siguientes archivos JS no fueron encontrados:");
      missingFiles.forEach((file) => console.error(file));
    }
    expect(missingFiles.length, "Faltan archivos JS").toBe(0);
  });
});
