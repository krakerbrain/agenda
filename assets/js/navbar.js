document.addEventListener("DOMContentLoaded", function () {
  const links = document.querySelectorAll(".nav-link");
  const mainContent = document.getElementById("main-content");

  // Usar la versión definida en el layout o fallback a timestamp
  const APP_VERSION = window.APP_VERSION;

  // Cargar la última pestaña seleccionada, o usar la predeterminada
  const lastPage = sessionStorage.getItem("lastPage");

  if (lastPage) {
    loadContent(lastPage);
  } else {
    // Buscar el primer link que no sea #logout
    let defaultLink = null;
    for (let link of links) {
      const href = link.id;
      if (href !== "logout") {
        defaultLink = href;
        break;
      }
    }

    // Cargar esa pestaña
    if (defaultLink) {
      loadContent(defaultLink);
    } else if (role_id == 1) {
      loadContent("master_add_company");
    } else {
      loadContent("datesList");
    }
  }

  // Configurar los listeners para cada pestaña
  links.forEach((link) => {
    link.addEventListener("click", function (event) {
      event.preventDefault();
      const page = this.id;
      if (page === "logout") {
        logout();
      } else {
        loadContent(page);
        sessionStorage.setItem("lastPage", page); // Guardar la pestaña actual en sessionStorage
      }
    });
  });

  async function loadContent(page) {
    document.querySelector(".titulo").textContent = document.querySelector(`#${page}`).innerHTML;
    try {
      const response = await fetch(`pages/${page}.php`);
      if (!response.ok) throw new Error("Error al cargar el contenido.");

      const data = await response.text();
      mainContent.innerHTML = data;
      document.getElementById(page).classList.add("active");

      links.forEach((link) => link.classList.toggle("active", link.id === page));

      hideCanvas();

      try {
        // Determinar la ruta del módulo basada en el ID de la página
        let modulePath;
        if (page.startsWith('master_')) {
          // Módulos de administración
          modulePath = `./master_admin/${page}.js`;
        } else if (page === 'notificaciones') {
          // Módulo de notificaciones en carpeta navbar
          modulePath = './navbar/notifications.js';
        } else {
          // El ID coincide con el nombre del archivo
          modulePath = `./${page}.js`;
        }

        // Agregar versión al path
        modulePath = `${modulePath}?v=${APP_VERSION}`;

        // Importar el módulo
        const module = await import(modulePath);
        
        // Usar la función init genérica
        if (typeof module.init === 'function') {
          await module.init();
        } else {
          console.warn(`Función init no encontrada en el módulo ${modulePath}`);
        }
      } catch (moduleError) {
        console.error(`Error al cargar el módulo para ${page}:`, moduleError);
        throw moduleError;
      }
    } catch (error) {
      mainContent.innerHTML = `Error: ${error.message}`;
      console.error('Error en loadContent:', error);
    }
  }

  function logout() {
    sessionStorage.removeItem("lastPage"); // Limpiar la pestaña guardada

    fetch(`${baseUrl}login/logout.php`)
      .then((response) => {
        if (!response.ok) {
          throw new Error("Error al cerrar la sesión.");
        }
        return response.json();
      })
      .then((data) => {
        window.location.href = data.redirect;
      })
      .catch((error) => {
        console.error("Error:", error);
        window.location.href = "index.php"; // Redirigir incluso si hay un error
      });
  }

  function hideCanvas() {
    // Selecciona todos los enlaces dentro del offcanvas
    const offcanvasMenu = document.getElementById("offcanvasMenu");
    const offcanvasLinks = offcanvasMenu.querySelectorAll(".nav-link");

    offcanvasLinks.forEach((link) => {
      link.addEventListener("click", () => {
        // Obtén o crea la instancia del offcanvas
        const bsOffcanvas = bootstrap.Offcanvas.getOrCreateInstance(offcanvasMenu);
        // Cierra el offcanvas
        bsOffcanvas.hide();
      });
    });
  }
});