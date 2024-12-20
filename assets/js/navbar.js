document.addEventListener("DOMContentLoaded", function () {
  const links = document.querySelectorAll(".nav-link");
  const mainContent = document.getElementById("main-content");
  // Cargar la última pestaña seleccionada, o usar la predeterminada
  const lastPage = sessionStorage.getItem("lastPage");
  if (lastPage) {
    loadContent(lastPage);
  } else if (role_id == 1) {
    loadContent("master_add_company");
  } else {
    loadContent("dateList");
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
          const { initDateList } = await import("./datesList.js?v=2.0.8");
          initDateList();
          break;
        case "horarios":
          const { initHorarios } = await import("./horarios.js?v=1.0.6");
          initHorarios();
          break;
        case "servicios":
          const { initServicios } = await import("./servicios.js?v=1.0.6");
          initServicios();
          break;
        case "configuraciones":
          const { initConfiguraciones } = await import("./configuraciones.js?v=1.0.6");
          initConfiguraciones();
          break;
        case "correos":
          const { initCorreos } = await import("./correos.js?v=1.0.6");
          initCorreos();
          break;
        case "datos_empresa":
          const { initDatosEmpresa } = await import("./datosEmpresa.js?v=1.0.6");
          initDatosEmpresa();
          break;
        case "add_user":
          const { initAddUser } = await import("./addUser.js?v=1.0.6");
          initAddUser();
          break;
        case "master_add_company":
          const { initAddCompany } = await import("./master_admin/master_add_company.js?v=1.0.6");
          initAddCompany();
          break;
        case "master_company_list":
          const { initCompanyList } = await import("./master_admin/master_company_list.js?v=1.0.6");
          initCompanyList();
          break;
        case "integrations":
          const { initIntegrations } = await import("./integrations.js?v=1.0.6");
          initIntegrations();
          break;
        case "eventos_unicos":
          const { initEventosUnicos } = await import("./eventos_unicos.js?v=1.0.6");
          initEventosUnicos();
          break;
        default:
          console.error("No hay un módulo para la página:", page);
      }
    } catch (error) {
      mainContent.innerHTML = error.message;
    }
  }

  function logout() {
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
