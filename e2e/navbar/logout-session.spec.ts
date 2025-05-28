import { test, expect } from "@playwright/test";
import dotenv from "dotenv";
dotenv.config();

const LOGIN_URL = process.env.E2E_LOGIN_URL!;
const USER = process.env.E2E_USER!;
const PASS = process.env.E2E_PASS!;

// Ajusta la URL de destino tras logout según tu app
const LOGOUT_REDIRECT_URL = "http://localhost/agenda/";

// El id del botón/logout link debe ser 'logout' según tu navbar

test.describe("Logout elimina la sesión", () => {
  test("Al hacer logout se elimina la sesión y se redirige", async ({ page, context }) => {
    // Login
    await page.goto(LOGIN_URL);
    await page.fill("#usuario", USER);
    await page.fill("#contrasenia", PASS);
    await Promise.all([page.waitForURL("**/user_admin/index.php"), page.click('input[type="submit"]')]);

    // Simula navegación para que se guarde algo en sessionStorage
    await page.evaluate(() => sessionStorage.setItem("lastPage", "clientes"));
    expect(await page.evaluate(() => sessionStorage.getItem("lastPage"))).toBe("clientes");

    await page.waitForSelector('[data-bs-toggle="offcanvas"]', { state: "visible" });
    await page.click('[data-bs-toggle="offcanvas"]');

    // Espera a que el logout esté visible dentro del offcanvas
    await page.waitForSelector("#logout", { state: "visible" });

    // Haz clic y espera redirección
    await Promise.all([page.waitForURL(LOGOUT_REDIRECT_URL), page.click("#logout")]);

    // Verifica que la sesión se haya eliminado (sessionStorage vacío o sin lastPage)
    const lastPage = await page.evaluate(() => sessionStorage.getItem("lastPage"));
    expect(lastPage).toBeNull();

    // Verifica que el usuario esté en la página de inicio tras logout
    expect(page.url()).toBe(LOGOUT_REDIRECT_URL);
  });
});
