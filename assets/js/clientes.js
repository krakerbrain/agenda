import { TabManager } from "./config/TabManager.js";
import { Pagination } from "./config/Pagination.js";
import { OffcanvasManager } from "./config/OffcanvasManager.js";
import { SearchManager } from "./config/SearchManager.js";
import { DatesUIHelpers } from "./dateLists/DatesUIHelpers.js";
import { CustomersTableRenderer } from "./config/customers/CustomersTableRenderer.js";
import { ModalManager } from "./config/ModalManager.js";

let pagination, tableRenderer;

export function init() {
  // Tabs usando TabManager
  new TabManager({
    tabContainerSelector: "#customerTab",
    onTabChange: (status) => {
      sessionStorage.setItem("customerStatus", status);
      loadCustomers(1);
    },
    storageKey: "customerStatus",
    defaultTab: sessionStorage.getItem("customerStatus") || "todos",
    resetFormSelector: "#searchCustomerForm",
  });

  // Pagination global
  pagination = new Pagination("prevPage", "nextPage", "currentPage", (page) => {
    loadCustomers(page);
  });

  // Offcanvas buscador
  new OffcanvasManager({
    toggleSelector: "#offcanvasToggleSearch",
    menuSelector: "#offcanvasSearch",
    closeSelector: "#offcanvasSearchClose",
    backdropSelector: "#offcanvasSearchBackdrop",
    direction: "top",
    onOpen: () => {},
    onClose: () => {},
  });

  // SearchManager global para clientes
  new SearchManager("#searchCustomerForm", handleSearch, handleAutocomplete, ["name", "phone", "mail"]);

  // Table renderer específico para clientes
  tableRenderer = new CustomersTableRenderer("#tableContent");

  // Cargar clientes para la pestaña correspondiente al último estado guardado
  loadCustomers();
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
      pagination.currentPage = page;
      // Si el número de cliente recibidas es menor que el límite, no hay más páginas
      const hasMoreData = data.length === limit;
      pagination.updateControls(hasMoreData);
    }
  } catch (error) {
    console.error("Error al obtener cliente:", error);
  }
}

function fillTableCustomers(data) {
  tableRenderer.render(data, DatesUIHelpers.getCustomerStatusBadge, getActionIcons);
  // Delegación de eventos para los action buttons
  document.getElementById("tableContent").onclick = function (e) {
    const agendarBtn = e.target.closest(".action-icon[id^='agendar-']");
    const editarBtn = e.target.closest(".action-icon[id^='editar-']");
    const bloquearBtn = e.target.closest(".action-icon[id^='bloquear-']");
    const desbloquearBtn = e.target.closest(".action-icon[id^='desbloquear-']");
    const eliminarBtn = e.target.closest(".action-icon[id^='eliminar-']");
    const eliminarIncidenciaBtn = e.target.closest(".action-icon[id^='eliminar-incidencia-']");

    if (agendarBtn) {
      const id = agendarBtn.id.replace("agendar-", "");
      const customer = data.find((c) => c.id == id);
      if (customer) agendarCliente(customer);
      return;
    }
    if (editarBtn) {
      const id = editarBtn.id.replace("editar-", "");
      const customer = data.find((c) => c.id == id);
      if (customer) editarCliente(customer);
      return;
    }
    if (bloquearBtn) {
      const id = bloquearBtn.id.replace("bloquear-", "");
      const customer = data.find((c) => c.id == id);
      if (customer) toggleBlockWithModal(customer);
      return;
    }
    if (desbloquearBtn) {
      const id = desbloquearBtn.id.replace("desbloquear-", "");
      toggleBlockSubmit(id);
      return;
    }
    if (eliminarBtn) {
      const id = eliminarBtn.id.replace("eliminar-", "");
      modalEliminarCliente(id);
      return;
    }
    if (eliminarIncidenciaBtn) {
      const id = eliminarIncidenciaBtn.id.replace("eliminar-incidencia-", "");
      handleIncidentDelete(id);
      return;
    }
    // Detalle cliente
    const detailLink = e.target.closest("a[id^='customerDetailLink']");
    if (detailLink) {
      e.preventDefault();
      const id = detailLink.dataset.id;
      openCustomerDetail(id);
      return;
    }
  };
}

function getActionIcons(customerId, isBlocked) {
  let icons = "";
  const status = sessionStorage.getItem("customerStatus") || "todos";

  if (status === "todos") {
    icons = `
      <i id="agendar-${customerId}" class="fa-solid fa-calendar-plus action-icon cursor-pointer text-cyan-700 hover:text-cyan-900 text-center" title="Agendar"><span class="button-text">AGENDAR</span></i>
      <i id="editar-${customerId}" class="fa-solid fa-edit action-icon cursor-pointer text-yellow-600 hover:text-yellow-800 text-center" title="Editar"><span class="button-text">EDITAR</span></i>
        ${
          isBlocked === 1
            ? `<i id="desbloquear-${customerId}" class="fa-solid fa-unlock action-icon cursor-pointer text-green-700 hover:text-green-900 text-center" title="Desbloquear"><span class="button-text">DESBLOQUEAR</span></i>`
            : `<i id="bloquear-${customerId}" class="fa-solid fa-lock action-icon cursor-pointer text-red-700 hover:text-red-900 text-center" title="Bloquear"><span class="button-text">BLOQUEAR</span></i>`
        }
      <i id="eliminar-${customerId}" class="fa-solid fa-trash-alt action-icon cursor-pointer text-red-600 hover:text-red-800 text-center" title="Eliminar"><span class="button-text">ELIMINAR</span></i>
    `;
  } else if (status === "incidencias") {
    icons = `
      <i id="eliminar-incidencia-${customerId}" class="fa-solid fa-trash-alt action-icon cursor-pointer text-red-600 hover:text-red-800 text-center" title="Eliminar incidencia"><span class="button-text">ELIMINAR INCIDENCIA</span></i>
    `;
  } else if (status === "blocked") {
    icons = `
      <i id="desbloquear-${customerId}" class="fa-solid fa-unlock action-icon cursor-pointer text-green-700 hover:text-green-900 text-center" title="Desbloquear"><span class="button-text">DESBLOQUEAR</span></i>
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
      ModalManager.show("editCustomerModal");
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
      ModalManager.hide("editCustomerModal");
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

    ModalManager.show("deleteIncidentsModal");

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
  confirmBtn.onclick = async () => {
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
        ModalManager.hide("deleteIncidentsModal");
      } else {
        throw new Error(result.message || "Error al eliminar");
      }
    } catch (error) {
      console.error("Error al eliminar:", error);
      infoModal("Error", error.message);
    }
  };
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
  document.getElementById("notaBloqueo").value = "";
  ModalManager.show("modalBloquearCliente");
  const form = document.getElementById("formBloquearCliente");
  form.onsubmit = (event) => {
    event.preventDefault();
    const notaBloqueo = document.getElementById("notaBloqueo").value;
    if (!notaBloqueo) {
      infoModal("Atención", "Por favor, ingrese una razón para el bloqueo.");
      return;
    }
    ModalManager.hide("modalBloquearCliente");
    toggleBlockSubmit(customer.id, notaBloqueo);
  };
}

function infoModal(label, body) {
  document.getElementById("infoModalLabel").textContent = label;
  document.getElementById("infoModalMessage").textContent = body;
  ModalManager.show("infoModal");
}

function modalEliminarCliente(customerId) {
  const modalElement = document.getElementById("modalEliminarCliente");
  if (!modalElement) {
    console.error("Modal no encontrado.");
    return;
  }
  ModalManager.show("modalEliminarCliente");
  document.querySelector("#btnEliminarCliente").onclick = () => {
    eliminarCliente(customerId);
    ModalManager.hide("modalEliminarCliente");
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
  document.getElementById("customerName").textContent = customer.name;
  document.getElementById("customerPhone").textContent = customer.phone;
  document.getElementById("customerPhone").href = `https://wa.me/${customer.phone}`;
  document.getElementById("customerEmail").textContent = customer.mail;
  document.getElementById("customerStatus").innerHTML = customer.blocked ? `Bloqueado<br><small class='text-red-600'>Razón: ${customer.nota_bloqueo || "Sin razón especificada"}</small>` : "Activo";
  document.getElementById("customerIncidents").textContent = customer.has_incidents ? "Sí" : "No";
  document.getElementById("customerNotes").textContent = customer.notes ?? "Sin notas";

  // Últimos servicios
  const lastServicesList = document.getElementById("customerLastServices");
  lastServicesList.innerHTML =
    customer.last_services && customer.last_services.length > 0
      ? customer.last_services
          .map(
            (service) => `
          <li><strong>${service.service_name}</strong> - ${service.appointment_date}<br></li>
      `
          )
          .join("")
      : "<li>No hay servicios registrados.</li>";

  // Incidencias
  const incidentsList = document.getElementById("customerIncidentsList");
  incidentsList.innerHTML =
    customer.incidents && customer.incidents.length > 0
      ? customer.incidents
          .map(
            (incident) => `
          <li><strong>${incident.description}</strong> - ${incident.incident_date}<br><small>${incident.note}</small></li>
      `
          )
          .join("")
      : "<li>No hay incidencias registradas.</li>";

  ModalManager.show("customerDetailModal");
}

// Cerrar modales al hacer click en .close-modal o fuera del modal
function setupModalCloseListeners() {
  document.querySelectorAll(".close-modal").forEach((button) => {
    button.addEventListener("click", function () {
      const modal = this.closest(".fixed.inset-0");
      if (modal) {
        const modalId = modal.id;
        ModalManager.hide(modalId);
      }
    });
  });
  document.querySelectorAll(".fixed.inset-0").forEach((modal) => {
    modal.addEventListener("click", function (e) {
      if (e.target === modal) {
        ModalManager.hide(modal.id);
      }
    });
  });
}

// Llama esto al final de init
setupModalCloseListeners();

function handleSearch(formData) {
  const searchParams = new URLSearchParams();
  for (let [key, value] of formData.entries()) {
    if (value) searchParams.append(key, value);
  }
  loadCustomersWithSearch(searchParams);
}

async function loadCustomersWithSearch(searchParams) {
  try {
    const savedStatus = sessionStorage.getItem("customerStatus") || "todos";
    searchParams.append("status", savedStatus);
    let url = `${baseUrl}user_admin/controllers/customers.php?${searchParams.toString()}`;
    const response = await fetch(url, { method: "GET" });
    const { success, data } = await response.json();
    if (success) {
      fillTableCustomers(data);
      // El paginador se mantiene igual, solo se actualiza la tabla
    }
  } catch (error) {
    console.error("Error al realizar la búsqueda:", error);
  }
}

function handleAutocomplete(e) {
  const input = e.target.id;
  const query = e.target.value;
  const savedStatus = sessionStorage.getItem("customerStatus") || "todos";
  if (query.length >= 3) {
    fetch(`${baseUrl}user_admin/controllers/autocomplete.php?input=${input}&query=${query}&tab=customers&status=${savedStatus}`)
      .then((res) => res.json())
      .then((data) => {
        fillTableCustomers(data.data);
      });
  } else if (query === "") {
    loadCustomers(savedStatus);
  }
}
