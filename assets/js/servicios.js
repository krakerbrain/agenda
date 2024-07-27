export function initServicios() {
  const form = document.getElementById("servicesForm");
  const tableBody = document.getElementById("servicesTableBody");

  form.addEventListener("submit", function (event) {
    event.preventDefault();
    const formData = new FormData(form);

    fetch(`${baseUrl}user_admin/controllers/services.php`, {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          // Limpiar el cuerpo de la tabla
          tableBody.innerHTML = "";

          const servicesData = JSON.parse(data.data);
          if (servicesData.length > 0) {
            // Llenar la página con los datos obtenidos
            servicesData.forEach((service) => {
              const serviceRow = document.createElement("tr");
              serviceRow.classList.add("service-row");
              serviceRow.innerHTML = `
                <td><input type="text" class="form-control" name="service_name[${service.id}]" value="${service.service_name}"></td>
                <td><input type="number" class="form-control" name="service_duration[${service.id}]" value="${service.duration}"></td>
                <td><textarea class="form-control" name="service_observations[${service.id}]">${service.observations}</textarea></td>
                <td>
                  <button type="button" class="btn btn-outline-primary btn-sm add-category">+Categoría</button>
                </td>
                <td>
                  <button type="button" class="btn btn-danger btn-sm delete-service">Eliminar</button>
                </td>
              `;
              tableBody.appendChild(serviceRow);

              if (service.categories && service.categories.length > 0) {
                service.categories.forEach((category) => {
                  const categoryRow = document.createElement("tr");
                  categoryRow.classList.add("category-item");
                  categoryRow.innerHTML = `
                    <td class="text-center">Agregar categoría</td>
                    <td><input type="text" class="form-control mb-1" name="category_name[${category.id}]" value="${category.category_name}" placeholder="Nombre de la Categoría"></td>
                    <td><textarea class="form-control mb-1" name="category_description[${category.id}]" placeholder="Descripción de la Categoría">${category.category_description}</textarea></td>
                    <td><button type="button" class="btn btn-outline-danger btn-sm remove-category">Eliminar</button></td>
                  `;
                  tableBody.appendChild(categoryRow);
                });
              }
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
  });

  let tempServiceCounter = 1;
  // Función para añadir una fila de servicio en blanco
  function addEmptyServiceRow() {
    document.getElementById("tempId").value = tempServiceCounter;
    const emptyServiceRow = document.createElement("tr");
    emptyServiceRow.classList.add("service-row");
    emptyServiceRow.innerHTML = `
    <td><input type="text" class="form-control" name="service_name[new-service-${tempServiceCounter}]" value=""></td>
    <td><input type="number" class="form-control" name="service_duration[new-service-${tempServiceCounter}]" value=""></td>
    <td><textarea class="form-control" name="service_observations[new-service-${tempServiceCounter}]"></textarea></td>
    <td>
    <button type="button" class="btn btn-outline-primary btn-sm add-category">+Categoría</button>
    </td>
    <td>
    <button type="button" class="btn btn-danger btn-sm delete-service" disabled>Eliminar</button>
    </td>
    `;
    tableBody.appendChild(emptyServiceRow);
    tempServiceCounter++;

    // Registrar el evento de agregar categoría
    emptyServiceRow.querySelector(".add-category").addEventListener("click", function () {
      addCategory(this, tempServiceCounter);
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

function addService() {
  const tableBody = document.getElementById("servicesTableBody");
  const tempId = document.getElementById("tempId").value;
  const emptyServiceRow = document.createElement("tr");
  emptyServiceRow.classList.add("service-row");
  emptyServiceRow.innerHTML = `
    <td><input type="text" class="form-control" name="service_name[new-service-${tempId}]" value=""></td>
    <td><input type="number" class="form-control" name="duration[new-service-${tempId}]" value=""></td>
    <td><textarea class="form-control" name="observations[new-service-${tempId}]"></textarea></td>
    <td>
      <button type="button" class="btn btn-outline-primary btn-sm add-category">+Categoría</button>
    </td>
    <td>
      <button type="button" class="btn btn-danger btn-sm delete-service">Eliminar</button>
    </td>
  `;
  tableBody.appendChild(emptyServiceRow);

  // Registrar el evento de eliminación del nuevo servicio
  emptyServiceRow.querySelector(".delete-service").addEventListener("click", function () {
    deleteService(this);
  });

  // Registrar el evento de adición de categoría al nuevo servicio
  emptyServiceRow.querySelector(".add-category").addEventListener("click", function () {
    addCategory(this, tempId);
  });
}

function addCategory(button, serviceId) {
  const row = button.closest(".service-row");
  const categoryRow = document.createElement("tr");
  categoryRow.classList.add("category-item");
  categoryRow.innerHTML = `
      <td class="text-center">Agregar categoría</td>
      <td><input type="text" class="form-control mb-1" name="category_name[new-service-${serviceId}][]" value="" placeholder="Nombre de la Categoría"></td>
      <td><textarea class="form-control mb-1" name="category_description[new-service-${serviceId}][]" placeholder="Descripción de la Categoría"></textarea></td>
      <td><button type="button" class="btn btn-outline-danger btn-sm remove-category">Eliminar</button></td>
  `;
  row.insertAdjacentElement("afterend", categoryRow);

  // Registrar el evento de eliminación de la nueva categoría
  categoryRow.querySelector(".remove-category").addEventListener("click", function () {
    removeCategory(this);
  });
}

function deleteService(button) {
  // Implementa la lógica para eliminar un servicio
  const serviceRow = button.closest(".service-row");
  serviceRow.remove();
}

function removeCategory(button) {
  const categoryRow = button.closest(".category-item");
  categoryRow.remove();
}
