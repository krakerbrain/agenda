export function initClientes() {
  // Obtener el último estado guardado o usar "unconfirmed" por defecto
  const savedStatus = sessionStorage.getItem("customerStatus") || "todos";

  // Cargar citas para la pestaña correspondiente al último estado guardado
  loadCustomers(savedStatus);

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
      loadCustomers(newStatus);
    });
  });
}

let currentPage = 1;
const limit = 10;

// Función para cargar citas de acuerdo al estado de la pestaña
async function loadCustomers(status, page = 1) {
  try {
    let url = `${baseUrl}user_admin/controllers/customers.php?status=${status}&page=${page}`;

    const response = await fetch(url, { method: "GET" });
    const { success, data, totalPages: total } = await response.json();

    if (success) {
      fillTableCustomers(data, status);
      currentPage = page;
      // Si el número de citas recibidas es menor que el límite, no hay más páginas
      const hasMoreData = data.length === limit;
      updatePaginationControls(hasMoreData);
    }
  } catch (error) {
    console.error("Error al obtener citas:", error);
  }
}

function fillTableCustomers(data, status) {
  const tableContent = document.getElementById("tableContent");
  let html = "";

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
              <div class="d-flex justify-content-evenly">
                ${getActionIcons(customer.id, status)}
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
      document.getElementById(`editar-${customer.id}`).onclick = () => editarCliente(customer.id);
      document.getElementById(`bloquear-${customer.id}`).onclick = () => bloquearCliente(customer.id);
      document.getElementById(`eliminar-${customer.id}`).onclick = () => eliminarCliente(customer.id);
    } else if (status === "incidencias") {
      document.getElementById(`eliminar-incidencia-${customer.id}`).onclick = () => eliminarIncidencia(customer.id);
    } else if (status === "blocked") {
      document.getElementById(`desbloquear-${customer.id}`).onclick = () => desbloquearCliente(customer.id);
    }
  });
}

function getActionIcons(customerId, status) {
  let icons = "";

  if (status === "todos") {
    icons = `
      <i id="agendar-${customerId}" class="fas fa-calendar-plus action-icon text-primary" title="Agendar"></i>
      <i id="editar-${customerId}" class="fas fa-edit action-icon text-warning" title="Editar"></i>
      <i id="bloquear-${customerId}" class="fas fa-lock action-icon" title="Bloquear"></i>
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

// function openReservationModal(customer) {
//   // Precargar los datos del cliente en el formulario
//   document.getElementById("name").value = customer.name;
//   document.getElementById("phone").value = customer.phone;
//   document.getElementById("mail").value = customer.mail;

//   // Mostrar el modal
//   const reservationModal = new bootstrap.Modal(document.getElementById("reservationModal"));
//   reservationModal.show();
// }

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
  document.getElementById("customerStatus").textContent = customer.blocked ? "Bloqueado" : "Activo";
  document.getElementById("customerIncidents").textContent = customer.has_incidents ? "Sí" : "No";

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
    const savedStatus = sessionStorage.getItem("customerStatus") || "todos";
    loadCustomers(savedStatus, currentPage - 1);
  }
});

document.getElementById("nextPage").addEventListener("click", () => {
  const savedStatus = sessionStorage.getItem("customerStatus") || "todos";
  loadCustomers(savedStatus, currentPage + 1);
});
