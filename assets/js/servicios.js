import { ServiceRender } from "./servicios/ServiceRender.js";
import { ModalManager } from "./config/ModalManager.js";

let serviceRender;

export function init() {
  // Inicializar el renderizador de tablas
  serviceRender = new ServiceRender("servicesContainer");
  const form = document.getElementById("servicesForm");

  // Manejador de eventos delegado para eliminar servicios
  document.addEventListener("click", async (e) => {
    if (e.target.closest(".delete-service")) {
      e.preventDefault();
      const card = e.target.closest("[data-service-id]");
      const serviceId = card.dataset.serviceId;
      await deleteService(serviceId, card);
    }

    if (e.target.closest(".remove-category")) {
      e.preventDefault();
      const categoryElement = e.target.closest(".category-item");
      await removeCategory(categoryElement);
    }
  });

  setupAddServiceButton();
  ModalManager.setupCloseListeners();

  async function loadServices() {
    try {
      const response = await fetch(`${baseUrl}user_admin/controllers/services.php`, {
        method: "GET",
      });
      const data = await response.json();

      if (data.success) {
        // Limpiar el contenedor ANTES de renderizar
        const container = document.getElementById("servicesContainer");
        container.innerHTML = ""; // Limpiar todo el contenido

        const { services, schedules } = data.data;
        const daysStatus = processSchedules(schedules);

        if (services.length > 0) {
          services.forEach((service) => {
            serviceRender.renderService(service, schedules, daysStatus);
          });
        }
      }
    } catch (error) {
      handleError(error, "Error al cargar los servicios.");
    }
  }

  loadServices();

  form.addEventListener("submit", async function (event) {
    event.preventDefault();

    const formData = new FormData(form);
    try {
      const response = await fetch(`${baseUrl}user_admin/controllers/services.php`, {
        method: "POST",
        body: formData,
      });
      const data = await response.json();

      if (data.success) {
        ModalManager.show("saveServices");
        loadServices(); // Recargar los datos después de guardar
      }
    } catch (error) {
      console.error("Error en la respuesta del servidor:", error);
      handleError(error, "Error al guardar los servicios. Por favor, inténtelo de nuevo.");
    }
  });
}

function processSchedules(schedules) {
  let daysStatus = {}; // Objeto para almacenar el estado de los días

  schedules.forEach((schedule) => {
    daysStatus[schedule.day_id] = schedule.is_enabled === 1;
  });

  return daysStatus; // Retorna un objeto donde el key es el día (1-7) y el valor es true o false
}

function setupAddServiceButton() {
  let addServiceBtn = document.getElementById("addServiceButton");

  addServiceBtn.addEventListener("click", createNewService);
}

export function createNewService() {
  if (!serviceRender) {
    serviceRender = new ServiceRender("servicesContainer");
  }

  const newService = {
    // Datos vacíos para nuevo servicio
    service_id: `new-service-${Date.now()}`,
    service_name: "",
    duration_formatted: { hours: 0, minutes: 0 },
    observations: "",
    available_days: [],
    is_enabled: true,
    categories: [],
  };

  serviceRender.renderService(newService);

  // Hacer scroll al nuevo servicio
  const container = document.getElementById("servicesContainer");
  const lastService = container.lastElementChild;
  lastService.scrollIntoView({ behavior: "smooth", block: "nearest" });
}

// Funciones de eliminación
async function deleteService(serviceId, cardElement) {
  try {
    const response = await fetch(`${baseUrl}user_admin/controllers/services.php`, {
      method: "DELETE",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `service_id=${serviceId}`,
    });
    const result = await response.json();

    if (result.success) {
      cardElement.remove();
      ModalManager.show("deletedServiceModal");
    } else {
      handleError(error, "Error al eliminar el servicio. Por favor, inténtelo de nuevo.");
    }
  } catch (error) {
    console.error("Error eliminando el servicio:", error);
    handleError(error, "Error al eliminar el servicio. Por favor, inténtelo de nuevo.");
  }
}

async function removeCategory(categoryElement) {
  const nameAttribute = categoryElement.querySelector("input[name^='category_name']").name;
  const matches = nameAttribute.match(/\[(\d+)\]\[(\d+)\]/);

  if (matches && !matches[2].includes("new")) {
    try {
      const response = await fetch(`${baseUrl}user_admin/controllers/services.php`, {
        method: "DELETE",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: `category_id=${matches[2]}`,
      });
      const result = await response.json();

      if (!result.success) {
        handleError(error, "Error al eliminar la categoría. Por favor, inténtelo de nuevo.");
        return;
      }
    } catch (error) {
      console.error("Error eliminando la categoría:", error);
      handleError(error, "Error al eliminar la categoría. Por favor, inténtelo de nuevo.");
      return;
    }
  }
  categoryElement.remove();
}

function handleError(error, customMessage = null) {
  console.error("Error:", error);
  const errorMessage = document.getElementById("errorMessage");
  errorMessage.textContent = customMessage || error.message || "Ha ocurrido un error inesperado";
  ModalManager.show("errorModal");
}
