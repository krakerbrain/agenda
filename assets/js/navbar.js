import { initDateList } from "./datesList.js";
import { initHorarios } from "./horarios.js";
import { initServicios } from "./servicios.js";
import { initConfiguraciones } from "./configuraciones.js";
import { initCorreos } from "./correos.js";
import { initDatosEmpresa } from "./datosEmpresa.js";
import { initAddUser } from "./addUser.js";
import { initAddCompany } from "./master_admin/master_add_company.js";
import { initCompanyList } from "./master_admin/master_company_list.js";

document.addEventListener("DOMContentLoaded", function () {
  const links = document.querySelectorAll(".nav-link");
  const mainContent = document.getElementById("main-content");

  links.forEach((link) => {
    link.addEventListener("click", function (event) {
      event.preventDefault();
      const page = this.id;
      if (page === "logout") {
        logout();
      } else {
        loadContent(page);
      }
    });
  });
  if (role_id == 1) {
    loadContent("master_add_company");
  } else {
    loadContent("dateList");
  }
  // Load the default content when the page loads, after registering all event listeners

  function loadContent(page) {
    document.querySelector(".titulo").textContent = document.querySelector("#" + page).innerHTML;
    fetch(`pages/${page}.php`)
      .then((response) => {
        if (!response.ok) {
          throw new Error("Error al cargar el contenido.");
        }
        return response.text();
      })
      .then((data) => {
        hideCanvas();
        mainContent.innerHTML = data;
        document.getElementById(page).classList.add("active");
        links.forEach((link) => {
          if (link.id !== page) {
            link.classList.remove("active");
          }
        });

        // Ejecutar el código específico de la página cargada
        switch (page) {
          case "dateList":
            initDateList();
            break;
          case "horarios":
            initHorarios();
            break;
          case "servicios":
            initServicios();
            break;
          case "configuraciones":
            initConfiguraciones();
            break;
          case "correos":
            initCorreos();
            break;
          case "datos_empresa":
            initDatosEmpresa();
            break;
          case "add_user":
            initAddUser();
            break;
          case "master_add_company":
            initAddCompany();
            break;
          case "master_company_list":
            initCompanyList();
            break;
          default:
            console.error("No hay un módulo para la página:", page);
        }
      })
      .catch((error) => {
        mainContent.innerHTML = error.message;
      });
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
