let tempServiceCounter = 1;

export function initServicios() {
  const form = document.getElementById("servicesForm");
  const tableBody = document.getElementById("servicesTableBody");

  function loadServices() {
    fetch(`${baseUrl}user_admin/controllers/services.php`, {
      method: "GET",
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          // Limpiar el cuerpo de la tabla
          tableBody.innerHTML = "";

          const servicesData = data.data;

          if (servicesData.length > 0) {
            // Llenar la página con los datos obtenidos
            servicesData.forEach((service) => {
              addService(service);
            });
          }
          // Añadir una fila en blanco para un nuevo servicio
          addEmptyServiceRow();
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        alert("Hubo un error al guardar la configuración.");
      });
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

  form.addEventListener("submit", function (event) {
    event.preventDefault();

    const isValid = validateAndCleanForm();

    if (!isValid) {
      return; // No enviar la solicitud si no hay datos válidos
    }

    const formData = new FormData(form);

    fetch(`${baseUrl}user_admin/controllers/services.php`, {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          alert("Configuración guardada exitosamente.");
          loadServices(); // Recargar los datos después de guardar
        } else {
          console.error("Error en la respuesta del servidor:", data);
        }
      })
      .catch((error) => {
        console.error("Error en la petición:", error);
        alert("Hubo un error al guardar la configuración.");
      });
  });

  // Función para añadir una fila de servicio en blanco
  function addEmptyServiceRow() {
    const emptyServiceRow = document.createElement("tr");
    emptyServiceRow.classList.add("service-row");
    const tempServiceId = `new-service-${tempServiceCounter}`;
    emptyServiceRow.innerHTML = `
      <td>
        <div class="form-check form-switch">
            <input type="checkbox" class="form-check-input" name="service_enabled[${tempServiceId}]">
        </div>
      </td>
      <td><input type="text" class="form-control" name="service_name[${tempServiceId}]" value=""></td>
      <td><input type="number" class="form-control" name="service_duration[${tempServiceId}]" value=""></td>
      <td><textarea class="form-control" name="service_observations[${tempServiceId}]"></textarea></td>
      <td>
        <button type="button" class="btn btn-outline-primary btn-sm add-category" data-service-id="${tempServiceId}">+Categoría</button>
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
  addEmptyServiceRow();

  document.getElementById("addServiceButton").addEventListener("click", addService);

  document.querySelectorAll(".delete-service").forEach((button) => {
    button.addEventListener("click", function () {
      deleteService(this);
    });
  });

  document.querySelectorAll(".remove-category").forEach((button) => {
    button.addEventListener("click", function () {
      removeCategory(this);
    });
  });
}
function addService(service = null) {
  const tableBody = document.getElementById("servicesTableBody");

  let serviceId;
  let serviceName = "";
  let serviceDuration = "";
  let serviceObservations = "";

  if (service) {
    serviceId = service.service_id;
    serviceName = service.service_name;
    serviceDuration = service.duration;
    serviceObservations = service.observations;
  } else {
    serviceId = `new-service-${tempServiceCounter}`;
    tempServiceCounter++;
  }

  const serviceRow = document.createElement("tr");
  serviceRow.classList.add("service-row");
  const isChecked = service.is_enabled ? "checked" : "";
  serviceRow.innerHTML = `
    <td>
      <div class="form-check form-switch">
          <input type="checkbox" class="form-check-input" name="service_enabled[${serviceId}]" ${isChecked}>
      </div>
    </td>
    <td><input type="text" class="form-control" name="service_name[${serviceId}]" value="${serviceName}"></td>
    <td><input type="number" class="form-control" name="service_duration[${serviceId}]" value="${serviceDuration}"></td>
    <td><textarea class="form-control" name="service_observations[${serviceId}]">${serviceObservations}</textarea></td>
    <td>
      <button type="button" class="btn btn-outline-primary btn-sm add-category" data-service-id="${serviceId}">+Categoría</button>
    </td>
    <td>
      <button type="button" class="btn btn-danger btn-sm delete-service">Eliminar</button>
    </td>
  `;
  tableBody.appendChild(serviceRow);

  // Registrar el evento de eliminación del servicio
  serviceRow.querySelector(".delete-service").addEventListener("click", function () {
    deleteService(this);
  });

  // Registrar el evento de adición de categoría al servicio
  serviceRow.querySelector(".add-category").addEventListener("click", function () {
    addCategory(this);
  });

  // Agregar categorías si es un servicio existente
  if (service && service.categories) {
    service.categories.forEach((category) => {
      addCategoryToService(serviceId, category);
    });
  }
}

function addCategoryToService(serviceId, category) {
  const tableBody = document.getElementById("servicesTableBody");
  const categoryRow = document.createElement("tr");
  categoryRow.classList.add("category-item");
  categoryRow.innerHTML = `
    <td></td>
    <td class="text-center">CATEGORÍA</td>
    <td><input type="text" class="form-control mb-1" name="category_name[${serviceId}][]" value="${category.category_name}" placeholder="Nombre de la Categoría"></td>
    <td><textarea class="form-control mb-1" name="category_description[${serviceId}][]" placeholder="Descripción de la Categoría">${category.category_description}</textarea></td>
    <td><button type="button" class="btn btn-outline-danger btn-sm remove-category">Eliminar</button></td>
  `;
  tableBody.appendChild(categoryRow);

  // Registrar el evento de eliminación de la categoría
  categoryRow.querySelector(".remove-category").addEventListener("click", function () {
    categoryRow.remove();
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
      <td><input type="text" class="form-control mb-1" name="category_name[${serviceId}][${categoryId}]" value="" placeholder="Nombre de la Categoría"></td>
      <td><textarea class="form-control mb-1" name="category_description[${serviceId}][${categoryId}]" placeholder="Descripción de la Categoría"></textarea></td>
      <td><button type="button" class="btn btn-outline-danger btn-sm remove-category">Eliminar</button></td>
  `;
  row.insertAdjacentElement("afterend", categoryRow);

  // Registrar el evento de eliminación de la nueva categoría
  categoryRow.querySelector(".remove-category").addEventListener("click", function () {
    removeCategory(this);
  });
}

function deleteService(button) {
  // Encuentra la fila del servicio a eliminar
  const serviceRow = button.closest(".service-row");

  // Encuentra el id del servicio
  const serviceId = button
    .closest(".service-row")
    .querySelector("input[name^='service_name']")
    .name.match(/\[(.*?)\]/)[1];

  // Encuentra todas las filas de categorías asociadas a este servicio
  const categories = document.querySelectorAll(`input[name^='category_name[${serviceId}]']`);
  categories.forEach((categoryInput) => {
    const categoryRow = categoryInput.closest(".category-item");
    if (categoryRow) {
      categoryRow.remove();
    }
  });

  // Elimina la fila del servicio
  serviceRow.remove();
}

function removeCategory(button) {
  const categoryRow = button.closest(".category-item");
  categoryRow.remove();
}
