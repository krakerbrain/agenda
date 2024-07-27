import { initAdmin } from "./admin.js";
import { initHorarios } from "./horarios.js";
import { initServicios } from "./servicios.js";
import { initConfiguraciones } from "./configuraciones.js";

document.addEventListener("DOMContentLoaded", function () {
  const links = document.querySelectorAll(".nav-link");
  const mainContent = document.getElementById("main-content");

  links.forEach((link) => {
    link.addEventListener("click", function (event) {
      event.preventDefault();
      const page = this.id;
      loadContent(page);
    });
  });

  // Load the default content when the page loads, after registering all event listeners
  loadContent("admin");

  function loadContent(page) {
    fetch(`pages/${page}.php`)
      .then((response) => {
        if (!response.ok) {
          throw new Error("Error al cargar el contenido.");
        }
        return response.text();
      })
      .then((data) => {
        mainContent.innerHTML = data;
        document.getElementById(page).classList.add("active");
        links.forEach((link) => {
          if (link.id !== page) {
            link.classList.remove("active");
          }
        });

        // Ejecutar el código específico de la página cargada
        switch (page) {
          case "admin":
            initAdmin();
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
          default:
            console.error("No hay un módulo para la página:", page);
        }
      })
      .catch((error) => {
        mainContent.innerHTML = error.message;
      });
  }
});
