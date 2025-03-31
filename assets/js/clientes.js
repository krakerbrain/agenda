export function initClientes() {
  // Obtener el último estado guardado o usar "unconfirmed" por defecto
  const savedStatus = sessionStorage.getItem("customerStatus") || "todos";

  // Cargar citas para la pestaña correspondiente al último estado guardado
  loadCustomers();

  const triggerTabList = document.querySelectorAll("#customerTab button");

  // Seleccionar el tab correspondiente al estado guardado
  triggerTabList.forEach((triggerEl) => {
    const status = triggerEl.dataset.bsTarget.substring(1); // Extraer el estado del atributo data-bs-target
    if (status === savedStatus) {
      const tabTrigger = new bootstrap.Tab(triggerEl);
      tabTrigger.show(); // Mostrar el tab correspondiente al estado guardado
    }

    // Agregar el evento de clic para cambiar de tab y actualizar el estado en sessionStorage
    triggerEl.addEventListener("click", (event) => {
      event.preventDefault();
      document.querySelector("#searchCustomerForm").reset();
      const newStatus = event.target.dataset.bsTarget.substring(1);
      sessionStorage.setItem("customerStatus", newStatus);
      loadCustomers();
    });
  });
}

let currentPage = 1;
const limit = 10;

// Función para cargar cliente de acuerdo al estado de la pestaña
async function loadCustomers(page = 1) {
  const status = sessionStorage.getItem("customerStatus") || "todos";
  try {
    let url = `${baseUrl}user_admin/controllers/customers.php?status=${status}&page=${page}`;

    const response = await fetch(url, { method: "GET" });
    const { success, data, totalPages: total } = await response.json();

    if (success) {
      fillTableCustomers(data);
      currentPage = page;
      // Si el número de cliente recibidas es menor que el límite, no hay más páginas
      const hasMoreData = data.length === limit;
      updatePaginationControls(hasMoreData);
    }
  } catch (error) {
    console.error("Error al obtener cliente:", error);
  }
}

function fillTableCustomers(data) {
  const tableContent = document.getElementById("tableContent");
  let html = "";
  const status = sessionStorage.getItem("customerStatus") || "todos";

  data.forEach((customer) => {
    html += `
          <tr class="body-table">
             <td data-cell="nombre" class="data">
              <a id="customerDetailLink${customer.id}" data-id="${customer.id}" href="#">${customer.name}</a>
            </td>
               <td data-cell="telefono" class="data"><i class="fab fa-whatsapp pe-1" style="font-size:0.85rem"></i><a href="https://wa.me/${customer.phone}" target="_blank">+${customer.phone}</a></td>
              <td data-cell="correo" class="data">${customer.mail}</td>
              <td data-cell="estado" class="data">${getStatusIcon(customer.blocked, customer.has_incidents)}</td>
              <td data-cell="acciones" class="data align-content-around">
              <div class="actionBtns">
                ${getActionIcons(customer.id, customer.blocked)}
              </div>
              </td>
          </tr>
      `;
  });
  tableContent.innerHTML = html;

  // Añadir listeners para abrir el detalle del cliente
  data.forEach((customer) => {
    const customerDetailLink = document.getElementById(`customerDetailLink${customer.id}`);
    customerDetailLink.addEventListener("click", function (event) {
      event.preventDefault();
      openCustomerDetail(customer.id); // Llamar a la función correcta
    });
  });

  data.forEach((customer) => {
    if (status === "todos") {
      document.getElementById(`agendar-${customer.id}`).onclick = () => agendarCliente(customer);
      document.getElementById(`editar-${customer.id}`).onclick = () => editarCliente(customer);
      if (customer.blocked == 1) {
        document.getElementById(`desbloquear-${customer.id}`).onclick = () => toggleBlockSubmit(customer.id);
      } else {
        document.getElementById(`bloquear-${customer.id}`).onclick = () => toggleBlockWithModal(customer);
      }
      document.getElementById(`eliminar-${customer.id}`).onclick = () => modalEliminarCliente(customer.id);
    } else if (status === "incidencias") {
      document.getElementById(`eliminar-incidencia-${customer.id}`).onclick = () => handleIncidentDelete(customer.id);
    } else if (status === "blocked") {
      document.getElementById(`desbloquear-${customer.id}`).onclick = () => toggleBlockSubmit(customer.id);
    }
  });
}

function getActionIcons(customerId, isBlocked) {
  let icons = "";
  const status = sessionStorage.getItem("customerStatus") || "todos";

  if (status === "todos") {
    icons = `
      <i id="agendar-${customerId}" class="fas fa-calendar-plus action-icon text-primary" title="Agendar"></i>
      <i id="editar-${customerId}" class="fas fa-edit action-icon text-warning" title="Editar"></i>
        ${
          isBlocked === 1
            ? `<i id="desbloquear-${customerId}" class="fas fa-unlock action-icon" title="Desbloquear"></i>`
            : `<i id="bloquear-${customerId}" class="fas fa-lock action-icon" title="Bloquear"></i>`
        }
      <i id="eliminar-${customerId}" class="fas fa-trash-alt action-icon text-danger" title="Eliminar"></i>
    `;
  } else if (status === "incidencias") {
    icons = `
      <i id="eliminar-incidencia-${customerId}" class="fas fa-trash-alt action-icon" title="Eliminar incidencia"></i>
    `;
  } else if (status === "blocked") {
    icons = `
      <i id="desbloquear-${customerId}" class="fas fa-unlock action-icon" title="Desbloquear"></i>
    `;
  }

  return icons;
}

async function agendarCliente(customer) {
  try {
    // Hacer un fetch para obtener la custom_url de la empresa
    const response = await fetch(`${baseUrl}user_admin/controllers/customers.php?action=getUrl&company_id=${customer.company_id}`);
    const { success, custom_url } = await response.json();

    if (success) {
      // Redirigir a la página de reservas con la custom_url y el customer_id
      window.location.href = `${baseUrl}reservas/${custom_url}?customer_id=${customer.id}`;
    } else {
      console.error("Error al obtener la URL de la empresa.");
    }
  } catch (error) {
    console.error("Error:", error);
  }
}

// Función para abrir el modal y cargar los datos del cliente
async function editarCliente(customer) {
  try {
    // Obtener los datos del cliente
    const response = await fetch(`${baseUrl}user_admin/controllers/customers.php?action=getCustomerDetail&id=${customer.id}`);
    const data = await response.json();

    if (data.success) {
      const customer = data.data;

      // Llenar el formulario con los datos del cliente
      document.getElementById("editCustomerId").value = customer.id;
      document.getElementById("editCustomerName").value = customer.name;
      document.getElementById("editCustomerPhone").value = customer.phone;
      document.getElementById("editCustomerMail").value = customer.mail;
      document.getElementById("editCustomerBlocked").checked = customer.blocked;
      document.getElementById("editCustomerNotes").value = customer.notes || "";

      // Mostrar el modal
      const editModal = new bootstrap.Modal(document.getElementById("editCustomerModal"));
      editModal.show();
    } else {
      console.error("Error al cargar los datos del cliente:", data.message);
    }
  } catch (error) {
    console.error("Error en la solicitud:", error);
  }
}

// Evento para guardar los cambios
document.getElementById("editCustomerForm").addEventListener("submit", async (e) => {
  e.preventDefault();

  const formData = new FormData(e.target);
  formData.append("action", "updateCustomer"); // Agregar la acción al FormData

  try {
    const response = await fetch(`${baseUrl}user_admin/controllers/customers.php`, {
      method: "POST",
      body: formData,
    });
    const data = await response.json();

    if (data.success) {
      console.log("Cliente actualizado:", data.message);
      // Cerrar el modal
      const editModal = bootstrap.Modal.getInstance(document.getElementById("editCustomerModal"));
      editModal.hide();
      // Recargar la lista de clientes o actualizar la interfaz
      infoModal("Éxito", "Edición exitosa.");
      loadCustomers();
    } else {
      console.error("Error al guardar los cambios:", data.message);
    }
  } catch (error) {
    console.error("Error en la solicitud:", error);
  }
});

async function handleIncidentDelete(customerId) {
  try {
    // 1. Obtener y mostrar incidencias en el modal
    await fetchAndDisplayIncidents(customerId); // <-- Sin asignar a variable

    // 2. Configurar el evento de eliminación
    setupDeleteHandler(customerId);
  } catch (error) {
    console.error("Error en handleIncidentDelete:", error);
    infoModal("Error", "Error al procesar la solicitud");
  }
}

// Función para obtener incidencias y mostrarlas en el modal
async function fetchAndDisplayIncidents(customerId) {
  try {
    const response = await fetch(`${baseUrl}user_admin/controllers/customers.php?action=getCustomerIncidents&id=${customerId}`);
    const data = await response.json();

    if (!data.success) {
      throw new Error(data.message || "Error al cargar incidencias");
    }

    // Mostrar en el modal
    const incidentsList = document.getElementById("incidents-list");
    incidentsList.innerHTML = data.data
      .map(
        (incident) => `
      <div class="form-check">
        <input class="form-check-input incident-checkbox" type="checkbox" 
               value="${incident.id}" id="incident-${incident.id}">
        <label class="form-check-label" for="incident-${incident.id}">
          ${incident.description} - ${new Date(incident.incident_date).toLocaleDateString()}
        </label>
      </div>
    `
      )
      .join("");

    // Mostrar el modal
    new bootstrap.Modal(document.getElementById("deleteIncidentsModal")).show();

    return data.data;
  } catch (error) {
    console.error("Error al obtener incidencias:", error);
    infoModal("Error", "No se pudieron cargar las incidencias");
    throw error;
  }
}

// Función para configurar el manejador de eliminación
function setupDeleteHandler(customerId) {
  const confirmBtn = document.getElementById("confirm-delete");
  confirmBtn.disabled = true;
  const modalElement = document.getElementById("deleteIncidentsModal");

  // Habilitar/deshabilitar botón según selección
  modalElement.addEventListener("change", (e) => {
    if (e.target.classList.contains("incident-checkbox")) {
      confirmBtn.disabled = !document.querySelector("#incidents-list .incident-checkbox:checked");
    }
  });

  // Manejador de eliminación
  confirmBtn.addEventListener("click", async () => {
    const selected = Array.from(document.querySelectorAll("#incidents-list .incident-checkbox:checked")).map((checkbox) => checkbox.value);

    if (selected.length === 0) return;

    try {
      const response = await fetch(`${baseUrl}user_admin/controllers/customers.php`, {
        method: "DELETE",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          action: "deleteIncidents",
          customer_id: customerId,
          incidents: selected,
        }),
      });

      const result = await response.json();

      if (result.success) {
        infoModal("Éxito", "Incidencias eliminadas correctamente");
        bootstrap.Modal.getInstance(modalElement).hide();
        // Aquí podrías recargar datos o actualizar UI si es necesario
      } else {
        throw new Error(result.message || "Error al eliminar");
      }
    } catch (error) {
      console.error("Error al eliminar:", error);
      infoModal("Error", error.message);
    }
  });
}

// Función para bloquear un cliente
async function toggleBlockSubmit(customerId, nota = null) {
  try {
    // Hacer un fetch para bloquear/desbloquear al cliente
    const response = await fetch(`${baseUrl}user_admin/controllers/customers.php?action=blockCustomer`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        customer_id: customerId,
        nota: nota, // Enviar la nota solo si se está bloqueando
      }),
    });

    const { success, message } = await response.json();

    if (success) {
      infoModal("Atención", message); // Mostrar mensaje de éxito
      loadCustomers(); // Recargar la lista de clientes
    } else {
      infoModal("Error", message || "Ocurrió un error al intentar bloquear/desbloquear el cliente.");
    }
  } catch (error) {
    console.error("Error:", error);
    infoModal("Error", "Ocurrió un error al intentar bloquear/desbloquear el cliente.");
  }
}

function toggleBlockWithModal(customer) {
  const modalElement = document.getElementById("modalBloquearCliente");
  if (!modalElement) {
    console.error("Modal no encontrado.");
    return;
  }

  const modal = new bootstrap.Modal(modalElement);

  // Limpiar el formulario cada vez que se abre el modal
  modalElement.addEventListener("show.bs.modal", () => {
    document.getElementById("notaBloqueo").value = "";
  });

  // Manejar el envío del formulario
  const form = document.getElementById("formBloquearCliente");
  form.onsubmit = (event) => {
    event.preventDefault(); // Evitar que el formulario se envíe de forma tradicional

    const notaBloqueo = document.getElementById("notaBloqueo").value;

    if (!notaBloqueo) {
      infoModal("Atención", "Por favor, ingrese una razón para el bloqueo.");
      return;
    }

    // Ocultar el modal
    modal.hide();

    // Llamar a toggleBlockSubmit con la nota
    toggleBlockSubmit(customer.id, notaBloqueo);
  };

  // Mostrar el modal
  modal.show();
}

function modalEliminarCliente(customerId) {
  const modalElement = document.getElementById("modalEliminarCliente");
  if (!modalElement) {
    console.error("Modal no encontrado.");
    return;
  }

  const modal = new bootstrap.Modal(modalElement);
  modal.show();

  document.querySelector("#btnEliminarCliente").onclick = () => {
    eliminarCliente(customerId);
    modal.hide();
  };
}

async function eliminarCliente(customerId) {
  try {
    // Hacer un fetch para obtener la custom_url de la empresa
    const response = await fetch(`${baseUrl}user_admin/controllers/customers.php`, {
      method: "DELETE",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        action: "deleteCustomer",
        customer_id: customerId,
      }),
    });

    const { success, message } = await response.json();

    if (success) {
      infoModal("Atención", message); // Mostrar mensaje de éxito
      loadCustomers(); // Recargar la lista de clientes
    } else {
      infoModal("Error", message || "Ocurrió un error al intentar eliminar el cliente.");
    }
  } catch (error) {
    console.error("Error:", error);
  }
}

async function openCustomerDetail(customerId) {
  try {
    const response = await fetch(`${baseUrl}user_admin/controllers/customers.php?action=getCustomerDetail&id=${customerId}`, {
      method: "GET",
    });
    const { success, data } = await response.json();

    if (success) {
      // Aquí puedes crear un modal o una nueva página para mostrar los detalles del cliente
      showCustomerDetailModal(data);
    } else {
      console.error("Error al obtener los detalles del cliente");
    }
  } catch (error) {
    console.error("Error al obtener los detalles del cliente:", error);
  }
}

function showCustomerDetailModal(customer) {
  console.log("Detalles del cliente:", customer);
  // Llenar la información básica
  document.getElementById("customerName").textContent = customer.name;
  document.getElementById("customerPhone").textContent = customer.phone;
  document.getElementById("customerPhone").href = `https://wa.me/${customer.phone}`;
  document.getElementById("customerEmail").textContent = customer.mail;
  document.getElementById("customerStatus").innerHTML = customer.blocked ? `Bloqueado<br><small class="text-danger">Razón: ${customer.nota_bloqueo || "Sin razón especificada"}</small>` : "Activo";
  document.getElementById("customerIncidents").textContent = customer.has_incidents ? "Sí" : "No";
  document.getElementById("customerNotes").textContent = customer.notes ?? "Sin notas";

  // Llenar los últimos servicios
  const lastServicesList = document.getElementById("customerLastServices");
  lastServicesList.innerHTML =
    customer.last_services && customer.last_services.length > 0
      ? customer.last_services
          .map(
            (service) => `
          <li class="list-group-item">
              <strong>${service.service_name}</strong> - ${service.appointment_date}<br>
          </li>
      `
          )
          .join("")
      : '<li class="list-group-item">No hay servicios registrados.</li>';

  // Llenar las incidencias
  const incidentsList = document.getElementById("customerIncidentsList");
  incidentsList.innerHTML =
    customer.incidents && customer.incidents.length > 0
      ? customer.incidents
          .map(
            (incident) => `
          <li class="list-group-item">
              <strong>${incident.description}</strong> - ${incident.incident_date}<br>
              <small>${incident.note}</small>
          </li>
      `
          )
          .join("")
      : '<li class="list-group-item">No hay incidencias registradas.</li>';

  // Mostrar el modal
  const customerDetailModal = new bootstrap.Modal(document.getElementById("customerDetailModal"));
  customerDetailModal.show();
}

function getStatusIcon(blocked, hasIncidents) {
  if (blocked) return '<i class="fas fa-ban" style="color: red;"></i>'; // Bloqueado
  if (hasIncidents) return '<i class="fas fa-exclamation-triangle" style="color: orange;"></i>'; // Tiene incidencias
  return '<i class="fas fa-check-circle" style="color: green;"></i>'; // Estado normal
}

function updatePaginationControls(hasMoreData) {
  document.getElementById("currentPage").innerText = `Página ${currentPage}`;
  document.getElementById("prevPage").disabled = currentPage === 1;
  document.getElementById("nextPage").disabled = !hasMoreData; // Deshabilitar "Siguiente" si no hay más datos
}

document.getElementById("prevPage").addEventListener("click", () => {
  if (currentPage > 1) {
    loadCustomers(currentPage - 1);
  }
});

document.getElementById("nextPage").addEventListener("click", () => {
  loadCustomers(currentPage + 1);
});

// Sugerencias de autocompletado (puedes hacer una búsqueda mínima de 3 caracteres)

document.getElementById("name").addEventListener("input", autocomplete);
document.getElementById("phone").addEventListener("input", autocomplete);
document.getElementById("mail").addEventListener("input", autocomplete);

let lastQuery = "";

async function autocomplete(e) {
  const input = e.target.id;
  const query = e.target.value;
  const savedStatus = sessionStorage.getItem("customerStatus") || "todos";

  if (query.length >= 3 || input == "status") {
    if (query !== lastQuery) {
      lastQuery = query;
      const response = await fetch(`${baseUrl}user_admin/controllers/autocomplete.php?input=${input}&query=${query}&tab=customers&status=${savedStatus}`);
      const data = await response.json();
      fillTableCustomers(data.data);
    }
  } else if (query === "") {
    loadCustomers(savedStatus);
  }
}

function infoModal(label, body) {
  document.getElementById("infoModalLabel").textContent = label;
  document.getElementById("infoModalMessage").textContent = body;
  const infoModal = new bootstrap.Modal(document.getElementById("infoModal"));
  infoModal.show();
}
