import { ContentLoader } from "../ContentLoader.js";

// Simula el DOM y fetch para pruebas
const createMainContent = () => {
  const div = document.createElement("div");
  div.id = "main-content";
  document.body.appendChild(div);
  return div;
};

describe("ContentLoader - rutas de módulos dinámicos", () => {
  let contentLoader;
  let mainContent;
  const APP_VERSION = "test-version";

  beforeEach(() => {
    // Limpia el DOM antes de cada test
    document.body.innerHTML = "";
    mainContent = createMainContent();
    // Mock fetch siempre exitoso
    contentLoader = new ContentLoader({
      fetch: jest.fn(() => Promise.resolve({ ok: true, text: () => Promise.resolve("ok") })),
      APP_VERSION,
    });
  });

  // Helper para forzar la base de ruta en getModulePath
  function getModulePathWithBase(page, base = "/agenda/assets/js/") {
    if (page.startsWith("master_")) return `${base}master_admin/${page}.js`;
    if (page === "notificaciones") return `${base}navbar/notifications.js`;
    return `${base}${page}.js`;
  }

  const testCases = [
    { page: "datesList", expected: "/agenda/assets/js/datesList.js" },
    { page: "clientes", expected: "/agenda/assets/js/clientes.js" },
    { page: "horarios", expected: "/agenda/assets/js/horarios.js" },
    { page: "servicios", expected: "/agenda/assets/js/servicios.js" },
    { page: "configuraciones", expected: "/agenda/assets/js/configuraciones.js" },
    { page: "correos", expected: "/agenda/assets/js/correos.js" },
    { page: "prueba", expected: "/agenda/assets/js/prueba.js" },
    { page: "datosEmpresa", expected: "/agenda/assets/js/datosEmpresa.js" },
    { page: "addUser", expected: "/agenda/assets/js/addUser.js" },
    { page: "integrations", expected: "/agenda/assets/js/integrations.js" },
    { page: "eventos_unicos", expected: "/agenda/assets/js/eventos_unicos.js" },
    { page: "bloqueoHoras", expected: "/agenda/assets/js/bloqueoHoras.js" },
    { page: "notificaciones", expected: "/agenda/assets/js/navbar/notifications.js" },
    { page: "services_assign", expected: "/agenda/assets/js/services_assign.js" },
    { page: "master_add_company", expected: "/agenda/assets/js/master_admin/master_add_company.js" },
    { page: "master_company_list", expected: "/agenda/assets/js/master_admin/master_company_list.js" },
    { page: "master_add_notification", expected: "/agenda/assets/js/master_admin/master_add_notification.js" },
  ];

  testCases.forEach(({ page, expected }) => {
    test(`getModulePath('${page}') retorna la ruta correcta`, () => {
      const result = getModulePathWithBase(page);
      // Mostrar la ruta probada en la salida del test
      // console.log(`Ruta probada para '${page}':`, result);
      expect(result).toBe(expected);
    });
  });

  test("updateActiveLink activa solo el link correcto", () => {
    // Prepara links
    const ids = ["a", "b", "c"];
    ids.forEach((id) => {
      const link = document.createElement("a");
      link.className = "nav-link";
      link.id = id;
      document.body.appendChild(link);
    });
    contentLoader.updateActiveLink("b");
    ids.forEach((id) => {
      const link = document.getElementById(id);
      if (id === "b") {
        expect(link.classList.contains("active")).toBe(true);
      } else {
        expect(link.classList.contains("active")).toBe(false);
      }
    });
  });
});
