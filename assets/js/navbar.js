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
      loadContent("dateList");
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
      switch (page) {
        case "dateList":
          const { initDateList } = await import(`./datesList.js?v=${APP_VERSION}`);
          initDateList();
          break;
        case "clientes":
          const { initClientes } = await import(`./clientes.js?v=${APP_VERSION}`);
          initClientes();
          break;
        case "horarios":
          const { initHorarios } = await import(`./horarios.js?v=${APP_VERSION}`);
          initHorarios();
          break;
        case "servicios":
          const { initServicios } = await import(`./servicios.js?v=${APP_VERSION}`);
          initServicios();
          break;
        case "configuraciones":
          const { initConfiguraciones } = await import(`./configuraciones.js?v=${APP_VERSION}`);
          initConfiguraciones();
          break;
        case "correos":
          const { initCorreos } = await import(`./correos.js?v=${APP_VERSION}`);
          initCorreos();
          break;
        case "datos_empresa":
          const { initDatosEmpresa } = await import(`./datosEmpresa.js?v=${APP_VERSION}`);
          initDatosEmpresa();
          break;
        case "add_user":
          const { initAddUser } = await import(`./addUser.js?v=${APP_VERSION}`);
          initAddUser();
          break;
        case "master_add_company":
          const { initAddCompany } = await import(`./master_admin/master_add_company.js?v=${APP_VERSION}`);
          initAddCompany();
          break;
        case "master_company_list":
          const { initCompanyList } = await import(`./master_admin/master_company_list.js?v=${APP_VERSION}`);
          initCompanyList();
          break;
        case "master_add_notification":
          const { initAddNotification } = await import(`./master_admin/master_add_notification.js?v=${APP_VERSION}`);
          initAddNotification();
          break;
        case "notificaciones":
          const { initNotificaciones } = await import(`./navbar/notifications.js?v=${APP_VERSION}`);
          initNotificaciones();
          break;
        case "integrations":
          const { initIntegrations } = await import(`./integrations.js?v=${APP_VERSION}`);
          initIntegrations();
          break;
        case "eventos_unicos":
          const { initEventosUnicos } = await import(`./eventos_unicos.js?v=${APP_VERSION}`);
          initEventosUnicos();
          break;
        case "block_hour":
          const { initBloqueoHoras } = await import(`./bloqueoHoras.js?v=${APP_VERSION}`);
          initBloqueoHoras();
          break;
        case "services_assign":
          const { initServicesAssign } = await import(`./services_assign.js?v=${APP_VERSION}`);
          initServicesAssign();
          break;
        default:
          console.error("No hay un módulo para la página:", page);
      }
    } catch (error) {
      mainContent.innerHTML = error.message;
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
