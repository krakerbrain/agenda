let tempServiceCounter = 1;

export function initServicios() {
  const form = document.getElementById("servicesForm");
  const tableBody = document.getElementById("servicesTableBody");

  async function loadServices() {
    try {
      const response = await fetch(`${baseUrl}user_admin/controllers/services.php`, {
        method: "GET",
      });
      const data = await response.json();

      if (data.success) {
        // Limpiar el cuerpo de la tabla
        tableBody.innerHTML = "";

        const { services, schedules } = data.data; // Extraer services y schedules

        // Procesar los días habilitados/deshabilitados a partir de schedules
        const daysStatus = processSchedules(schedules);
        if (services.length > 0) {
          // Llenar la página con los datos obtenidos
          services.forEach((service) => {
            addService(service, schedules, daysStatus); // Pasar schedules a addService
          });
        }
        // Añadir una fila en blanco para un nuevo servicio
        addEmptyServiceRow();
      }
    } catch (error) {
      console.error("Error:", error);
      alert("Hubo un error al cargar los servicios.");
    }
  }

  loadServices();

  function validateAndCleanForm() {
    const tableBody = document.getElementById("servicesTableBody");
    let hasValidData = false;

    tableBody.querySelectorAll(".service-row").forEach((serviceRow) => {
      const serviceName = serviceRow.querySelector("input[name^='service_name']").value.trim();
      const serviceDuration = serviceRow.querySelector("input[name^='service_duration']").value.trim();

      if (!serviceName || !serviceDuration) {
        serviceRow.remove();
        let nextSibling = serviceRow.nextElementSibling;
        while (nextSibling && nextSibling.classList.contains("category-item")) {
          const categoryRow = nextSibling;
          nextSibling = categoryRow.nextElementSibling;
          categoryRow.remove();
        }
      } else {
        hasValidData = true;
      }
    });

    if (!hasValidData) {
      alert("Debe ingresar al menos un servicio válido con su nombre y duración.");
    }

    return hasValidData;
  }

  form.addEventListener("submit", async function (event) {
    event.preventDefault();

    const isValid = validateAndCleanForm();

    if (!isValid) {
      return; // No enviar la solicitud si no hay datos válidos
    }

    const formData = new FormData(form);
    try {
      const response = await fetch(`${baseUrl}user_admin/controllers/services.php`, {
        method: "POST",
        body: formData,
      });
      const data = await response.json();

      if (data.success) {
        const modal = new bootstrap.Modal(document.getElementById("saveServices"));
        modal.show();
        loadServices(); // Recargar los datos después de guardar
      }
    } catch (error) {
      console.error("Error en la respuesta del servidor:", error);
      alert("Hubo un error al guardar la configuración.");
    }
  });

  // Función para añadir una fila de servicio en blanco
  function addEmptyServiceRow() {
    const emptyServiceRow = document.createElement("tr");
    emptyServiceRow.classList.add("service-row");
    const tempServiceId = `new-service-${tempServiceCounter}`;
    emptyServiceRow.innerHTML = `
      <td data-cell="Habilitado" class="data">
        <div class="form-check form-switch">
            <input type="checkbox" class="form-check-input" name="service_enabled[${tempServiceId}]">
        </div>
      </td>
      <td data-cell="nombre servicio" class="data"><input type="text" class="form-control" name="service_name[${tempServiceId}]" value=""></td>
      <td data-cell="horas duración" class="data"><input type="number" class="form-control" name="service_duration[${tempServiceId}]" value=""></td>
      <td data-cell="observaciones" class="data"><textarea class="form-control" name="service_observations[${tempServiceId}]"></textarea></td>
      <td data-cell="agrega categorías" class="data">
        <button type="button" class="btn btn-outline-primary btn-sm add-category" data-service-id="${tempServiceId}">+Categoría</button>
      </td>
      <td data-cell="días disponibles" class="data">
        <div class="days-container d-flex gap-1">
          ${generateDaysCheckboxes([], tempServiceId)}
        </div>
      </td>
      <td>
        <button type="button" class="btn btn-danger btn-sm delete-service" disabled>Eliminar</button>
      </td>
    `;
    tableBody.appendChild(emptyServiceRow);
    tempServiceCounter++;

    // Registrar el evento de agregar categoría
    emptyServiceRow.querySelector(".add-category").addEventListener("click", function () {
      addCategory(this);
    });
  }

  // Llamar a la función para agregar una fila de servicio en blanco al cargar la página
  // addEmptyServiceRow();

  document.getElementById("addServiceButton").addEventListener("click", addService);

  document.querySelectorAll(".remove-category").forEach((button) => {
    button.addEventListener("click", function () {
      removeCategory(this);
    });
  });

  const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
  const popoverList = [...popoverTriggerList].map((popoverTriggerEl) => new bootstrap.Popover(popoverTriggerEl));
}

function processSchedules(schedules) {
  let daysStatus = {}; // Objeto para almacenar el estado de los días

  schedules.forEach((schedule) => {
    daysStatus[schedule.day_id] = schedule.is_enabled === 1;
  });

  return daysStatus; // Retorna un objeto donde el key es el día (1-7) y el valor es true o false
}

function addService(service = null, schedules = {}, daysStatus = {}) {
  const tableBody = document.getElementById("servicesTableBody");

  // Valores por defecto
  let serviceId,
    serviceName = "",
    serviceDuration = "",
    serviceObservations = "",
    serviceDays = [];

  if (service && typeof service === "object" && !service.isTrusted) {
    serviceId = service.service_id;
    serviceName = service.service_name;
    serviceDuration = service.duration;
    serviceObservations = service.observations;
    serviceDays = service.available_days || []; // Array de días disponibles
  } else {
    serviceId = `new-service-${tempServiceCounter}`;
    tempServiceCounter++;
  }

  // Crear una fila para el servicio
  const serviceRow = createServiceRow(serviceId, serviceName, serviceDuration, serviceObservations, serviceDays, schedules, daysStatus, service?.is_enabled);

  // Añadir la fila a la tabla
  tableBody.appendChild(serviceRow);

  // Añadir eventos para eliminar servicio y agregar categoría
  serviceRow.querySelector(".delete-service").addEventListener("click", function () {
    const modal = new bootstrap.Modal(document.getElementById("deleteServiceModal"));

    // Agrega la clase 'ondelete-service' al serviceRow que se va a eliminar
    const serviceRow = this.closest(".service-row");
    serviceRow.classList.add("ondelete-service");

    modal.show();
  });

  serviceRow.querySelector(".add-category").addEventListener("click", function () {
    addCategory(this);
  });

  // Manejar el clic en el botón de confirmar
  document.getElementById("confirmServiceDelete").addEventListener("click", function () {
    // Obtén el elemento con la clase 'ondelete-service'
    const serviceRow = document.querySelector(".ondelete-service");

    if (serviceRow) {
      // Llama a deleteService pasando el serviceRow con la clase 'ondelete-service'
      deleteService(serviceRow);

      // Quita la clase 'ondelete-service' del elemento
      serviceRow.classList.remove("ondelete-service");
    }

    // Cierra el modal
    bootstrap.Modal.getInstance(document.getElementById("deleteServiceModal")).hide();
  });

  // Añadir las categorías si el servicio tiene alguna
  if (service && service.categories) {
    service.categories.forEach((category) => {
      addCategoryToService(serviceId, category);
    });
  }

  // Inicializar tooltips para los días deshabilitados
  const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
  [...tooltipTriggerList].map((tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl));
}

// Crear la fila del servicio
function createServiceRow(serviceId, serviceName, serviceDuration, serviceObservations, serviceDays, schedules, dayStatus, isEnabled) {
  const daysStatus = Object.keys(schedules).length === 0 ? "" : getAvailableDays(serviceDays, schedules);
  const serviceRow = document.createElement("tr");
  serviceRow.classList.add("service-row");

  const isChecked = isEnabled ? "checked" : "";
  serviceRow.innerHTML = `
    <td data-cell="Habilitado" class="data">
      <div class="form-check form-switch">
        <input type="checkbox" class="form-check-input" name="service_enabled[${serviceId}]" ${isChecked}>
      </div>
    </td>
    <td data-cell="nombre servicio" class="data"><input type="text" class="form-control" name="service_name[${serviceId}]" value="${serviceName}"></td>
    <td data-cell="horas duración" class="data"><input type="number" class="form-control" name="service_duration[${serviceId}]" value="${serviceDuration}"></td>
    <td data-cell="observaciones" class="data"><textarea class="form-control" name="service_observations[${serviceId}]">${serviceObservations}</textarea></td>
    <td data-cell="agrega categorías" class="data">
      <button type="button" class="btn btn-outline-primary btn-sm add-category" data-service-id="${serviceId}">+Categoría</button>
    </td>
    <td data-cell="días disponibles" class="data">
      <div class="days-container d-flex gap-1">
      ${generateDaysCheckboxes(daysStatus, serviceId)}
      </div>
    </td>
    <td>
      <button type="button" class="btn btn-danger btn-sm delete-service" ${serviceName == "" ? "disabled" : ""}>Eliminar</button>
    </td>
  `;

  return serviceRow;
}

// Generar los checkboxes para los días (1 al 7 siempre)
function generateDaysCheckboxes(daysStatus, serviceId) {
  const daysOfWeek = ["L", "M", "M", "J", "V", "S", "D"];

  return daysOfWeek
    .map((day, index) => {
      const dayId = index + 1; // Los días empiezan desde 1
      const { enabled = true, checked = false } = daysStatus[dayId] || {}; // Si no hay datos en daysStatus, habilitado por defecto
      const disabledClass = !enabled ? "disabled-day" : ""; // Clase para días deshabilitados
      const tooltipAttributes = !enabled ? `tabindex="0" data-bs-toggle="tooltip" title="Día no disponible. Habilitarlo en Servicios"` : "";

      return `
      <div class="day align-items-center d-flex flex-column text-center ${disabledClass}" ${tooltipAttributes}>
       <input type="checkbox" class="form-check-input" name="available_service_day[${serviceId}][]" value="${dayId}" ${checked ? "checked" : ""} ${!enabled ? "disabled" : ""}>
        <label class="mt-1">${day}</label>
      </div>`;
    })
    .join("");
}

function getAvailableDays(serviceDays, schedules) {
  // Crea un objeto para almacenar el estado de los días (habilitado y marcado)
  const daysStatus = {};

  // Recorre los días de la semana (1-7) y aplica la lógica
  for (let i = 1; i <= 7; i++) {
    const isDayEnabled = schedules.some((schedule) => schedule.day_id === i && schedule.is_enabled);
    const isChecked = serviceDays.includes(i) && isDayEnabled; // Solo checked si está habilitado y marcado

    daysStatus[i] = {
      day: i,
      enabled: isDayEnabled,
      checked: isChecked,
    };
  }

  return daysStatus;
}

function addCategoryToService(serviceId, category) {
  const tableBody = document.getElementById("servicesTableBody");
  const categoryRow = document.createElement("tr");
  const categoryId = category.category_id;
  categoryRow.classList.add("category-item");
  categoryRow.innerHTML = `
    <td></td>
    <td class="text-center">CATEGORÍA</td>
    <td data-cell="nombre categoría" class="data"><input type="text" class="form-control mb-1" name="category_name[${serviceId}][${categoryId}]" value="${category.category_name}" placeholder="Nombre de la Categoría"></td>
    <td data-cell="descripción categoría" class="data"><textarea class="form-control mb-1" name="category_description[${serviceId}][${categoryId}]" placeholder="Descripción de la Categoría">${category.category_description}</textarea></td>
    <td><button type="button" class="btn btn-outline-danger btn-sm remove-category">Eliminar</button></td>
  `;
  tableBody.appendChild(categoryRow);

  // Registrar el evento de eliminación de la categoría
  categoryRow.querySelector(".remove-category").addEventListener("click", function () {
    // categoryRow.remove();
    removeCategory(this);
  });
}

function addCategory(button) {
  const serviceId = button.getAttribute("data-service-id");
  const row = button.closest(".service-row");
  const categoryId = `new-category-${tempServiceCounter}`;
  const categoryRow = document.createElement("tr");
  categoryRow.classList.add("category-item");
  categoryRow.innerHTML = `
      <td></td>
      <td class="text-center">Agregar categoría</td>
      <td data-cell="nombre categoría" class="data"><input type="text" class="form-control mb-1" name="category_name[${serviceId}][${categoryId}]" value="" placeholder="Nombre de la Categoría"></td>
      <td data-cell="descripción categoría" class="data"><textarea class="form-control mb-1" name="category_description[${serviceId}][${categoryId}]" placeholder="Descripción de la Categoría"></textarea></td>
      <td><button type="button" class="btn btn-outline-danger btn-sm remove-category">Eliminar</button></td>
  `;
  row.insertAdjacentElement("afterend", categoryRow);

  // Registrar el evento de eliminación de la nueva categoría
  categoryRow.querySelector(".remove-category").addEventListener("click", function () {
    removeCategory(this);
  });
  tempServiceCounter++;
}

async function deleteService(button) {
  // Encuentra la fila del servicio a eliminar
  // const serviceRow = button.closest(".service-row");

  // Encuentra el id del servicio
  const serviceId = button
    // .closest(".service-row")
    .querySelector("input[name^='service_name']")
    .name.match(/\[(.*?)\]/)[1];

  // Enviar solicitud al backend para verificar si el servicio tiene citas agendadas
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
      // Encuentra todas las filas de categorías asociadas a este servicio
      const categories = document.querySelectorAll(`input[name^='category_name[${serviceId}]']`);
      categories.forEach((categoryInput) => {
        const categoryRow = categoryInput.closest(".category-item");
        if (categoryRow) {
          categoryRow.remove();
        }
      });

      // Elimina la fila del servicio
      // serviceRow.remove();
      const modal = new bootstrap.Modal(document.getElementById("deletedServiceModal"));
      modal.show();
      button.remove();
    } else {
      alert(result.message);
    }
  } catch (error) {
    console.error("Error eliminando el servicio:", error);
    alert("Hubo un error al intentar eliminar el servicio. Por favor, inténtalo de nuevo.");
  }
}

async function removeCategory(button) {
  const categoryRow = button.closest(".category-item");

  // Encuentra el id de la categoría usando una expresión regular para capturar ambos valores dentro de los corchetes
  const nameAttribute = categoryRow.querySelector("input[name^='category_name']").name;
  const matches = nameAttribute.match(/\[(\d+)\]\[(\d+)\]/);

  if (matches) {
    const serviceId = matches[1];
    const categoryId = matches[2];

    // Verificar si la categoría es nueva (ID temporal) o existente
    if (categoryId.includes("new-category")) {
      // Solo eliminar del DOM
      categoryRow.remove();
    } else {
      try {
        // Enviar solicitud al backend para eliminar la categoría existente
        const response = await fetch(`${baseUrl}user_admin/controllers/services.php`, {
          method: "DELETE",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: `category_id=${categoryId}`,
        });
        const result = await response.json();

        if (result.success) {
          categoryRow.remove();
        } else {
          alert(result.message);
        }
      } catch (error) {
        console.error("Error eliminando la categoría:", error);
        alert("Hubo un error al intentar eliminar la categoría. Por favor, inténtalo de nuevo.");
      }
    }
  } else {
    categoryRow.remove();
  }
}
