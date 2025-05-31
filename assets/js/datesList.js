// --- Modular imports ---
import { DatesTabManager } from "./dateLists/DatesTabManager.js";
// import { DateFormatter } from "./dateLists/DateFormatter.js";
import { DatesTableRenderer } from "./dateLists/DatesTableRenderer.js";
import { DatesPagination } from "./dateLists/DatesPagination.js";
import { DatesSearch } from "./dateLists/DatesSearch.js";
import { DatesUIHelpers } from "./dateLists/DatesUIHelpers.js";
import { OffcanvasManager } from "./config/OffcanvasManager.js";
import { SpinnerManager } from "./config/SpinnerManager.js";
import { appointmentsStore } from "./dateLists/state.js";

// --- Modular class instances ---
let tableRenderer, pagination, searchManager, tabManager;

export function init() {
  // Tabs
  tabManager = new DatesTabManager("#myTab button", (status) => {
    pagination.currentPage = 1;
    loadAppointments(status, 1);
  });

  // Table renderer
  tableRenderer = new DatesTableRenderer("#tableContent");

  // Pagination
  pagination = new DatesPagination("prevPage", "nextPage", "currentPage", (page) => {
    const status = sessionStorage.getItem("status") || "unconfirmed";
    loadAppointments(status, page);
  });

  // Search
  searchManager = new DatesSearch("#searchForm", handleSearch, handleAutocomplete);

  offCanvas();
  // Initial load
  const savedStatus = sessionStorage.getItem("status") || "unconfirmed";
  loadAppointments(savedStatus);
  setupActionButtonDelegation();
}

let currentPage = 1;
const limit = 10;

// --- Modularized loadAppointments ---
async function loadAppointments(status, page = 1) {
  try {
    let url = `${baseUrl}user_admin/controllers/appointments.php?status=${status}&page=${page}`;
    if (status === "events") {
      url = `${baseUrl}user_admin/controllers/unique_events.php?status=inscriptions&page=${page}`;
    }
    const response = await fetch(url, { method: "GET" });
    const { success, data, show_provider_column, is_owner } = await response.json();
    if (success) {
      appointmentsStore.setAppointments(data); // Guardar en el store modular
      if (status === "events") {
        tableRenderer.renderEventTable(data, DatesUIHelpers.getStatusBadge, getActionButtons);
      } else {
        tableRenderer.renderAppointments(data, show_provider_column, DatesUIHelpers.getStatusBadge, getActionButtons);
      }
      pagination.currentPage = page;
      const hasMoreData = data.length === limit;
      pagination.updateControls(hasMoreData);
    }
  } catch (error) {
    console.error("Error al obtener citas:", error);
  }
}

function getActionButtons(status, id, btnType = "appointment") {
  let buttons = "";
  const confirmId = `confirmarBtn${id}`;
  const deleteId = `eliminarBtn${id}`;
  // Botón de confirmar (solo para citas pendientes)
  if (status === 0) {
    buttons += `
      <div id="${confirmId}" class="action-btn-container inline-block">
        <button class="fa-solid fa-square-check action-icon text-green-800 confirm" title="Confirmar reserva" data-id="${id}">
          <span class="button-text">CONFIRMAR</span>
        </button>
      </div>
    `;
  }
  // Botón de eliminar (siempre visible)
  buttons += `
    <div id="${deleteId}" class="action-btn-container inline-block">
      <button class="fas fa-trash-alt action-icon text-red-500 eliminarReserva" title="Eliminar reserva" data-id="${id}">
        <span class="button-text">ELIMINAR</span>
      </button>
    </div>`;
  return buttons;
}

// Delegación de eventos para los botones de acción de la tabla
function setupActionButtonDelegation() {
  document.addEventListener("click", function (e) {
    // Confirmar
    const confirmBtn = e.target.closest(".action-btn-container .confirm");
    if (confirmBtn) {
      const container = confirmBtn.closest(".action-btn-container");
      if (container && container.id.startsWith("confirmarBtn")) {
        const id = container.id.replace("confirmarBtn", "");
        confirmReservation(id);
        return;
      }
    }
    // Eliminar
    const deleteBtn = e.target.closest(".action-btn-container .eliminarReserva");
    if (deleteBtn) {
      const container = deleteBtn.closest(".action-btn-container");
      if (container && container.id.startsWith("eliminarBtn")) {
        const id = container.id.replace("eliminarBtn", "");
        // Buscar la cita completa usando el store modular
        const appointment = appointmentsStore.getAppointment(id);
        openDeleteModal(appointment || { id_appointment: id });
        return;
      }
    }
  });
}

function showSpinner(containerId, options = {}) {
  SpinnerManager.show(containerId, options);
}
function hideSpinner(containerId) {
  SpinnerManager.hide(containerId);
}

export async function confirmReservation(id, type = null) {
  try {
    // Log de inicio de la función
    logAction(`Iniciando confirmación de reserva con ID: ${id}`);

    // Mostrar spinner
    const containerId = `confirmarBtn${id}`;
    showSpinner(containerId, { size: "1rem" });
    let url = type == "event" ? "eventos/controller/confirmar_inscripcion.php" : "user_admin/controllers/confirm.php";
    const response = await fetch(`${baseUrl}${url}`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ id }),
    });

    logAction(`Respuesta recibida con estado HTTP: ${response.status}`);

    // Verificar si la respuesta fue exitosa (código de estado 2xx)
    if (response.status === 401) {
      logAction("Estado 401 detectado: autenticación requerida");
      handleAuthenticationModal();
      return; // Salir de la función para evitar continuar con la confirmación
    }

    const data = await response.json();
    logAction(`Respuesta JSON recibida: ${JSON.stringify(data)}`);

    if (data.success) {
      logAction("Reserva confirmada exitosamente");
      handleSuccess(data, type);
    } else {
      logAction(`Error en la confirmación: ${data.message || "Sin mensaje"}`);
      handleError(data);
    }
  } catch (error) {
    logAction(`Error capturado: ${error.message}`);
    console.error("Error:", error);
  } finally {
    hideSpinner(`confirmarBtn${id}`);
    logAction("Finalizando confirmación de reserva");
  }
}

export async function deleteAppointment(appointment, reason, notes, generateIncident) {
  try {
    const response = await fetch(`${baseUrl}user_admin/delete_calendar_event.php`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        calendarEventID: appointment.event_id,
        appointmentID: appointment.id_appointment,
        customerID: appointment.id_customer,
        reason: reason, // Razón para eliminar la reserva
        notes: notes, // Notas adicionales
        generateIncident: generateIncident,
      }),
    });

    logAction(`Respuesta recibida con estado HTTP: ${response.status}`);

    // Verificar si la respuesta fue exitosa (código de estado 2xx)
    if (response.status === 401) {
      logAction("Estado 401 detectado: autenticación requerida");
      handleAuthenticationModal();
      return; // Salir de la función para evitar continuar con la confirmación
    }

    const data = await response.json();
    logAction(`Respuesta JSON recibida: ${JSON.stringify(data)}`);
    if (data.success) {
      handleInfoModal("Evento eliminado", data.message);
      //obtener status de session storage
      let status = sessionStorage.getItem("status");
      loadAppointments(status);
    } else {
      alert("Error desconocido al eliminar la reserva.");
    }
  } catch (error) {
    console.error("Error:", error);
    alert("Error al eliminar la reserva.");
  }
}

async function deleteEvent(eventID) {
  //
  showSpinner(eventID);

  try {
    const response = await fetch(`${baseUrl}user_admin/controllers/event_inscription_list.php`, {
      method: "DELETE",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        eventID: eventID,
      }),
    });

    logAction(`Respuesta recibida con estado HTTP: ${response.status}`);

    const data = await response.json();
    logAction(`Respuesta JSON recibida: ${JSON.stringify(data)}`);

    if (data.success) {
      handleInfoModal("Evento eliminado", data.message);
      //obtener status de session storage
      let status = sessionStorage.getItem("status");
      loadAppointments(status);
    } else {
      alert("Error desconocido al eliminar la reserva.");
    }
  } catch (error) {
    console.error("Error:", error);
    alert("Error al eliminar la reserva.");
  } finally {
    // Ocultar spinner y habilitar botón después de que la solicitud se complete
    hideSpinner(eventID);
  }
}

// --- Modularized search and autocomplete handlers ---
function handleSearch(formData) {
  const searchParams = new URLSearchParams();
  for (let [key, value] of formData.entries()) {
    if (value) searchParams.append(key, value);
  }
  loadAppointmentsWithSearch(searchParams);
}

async function loadAppointmentsWithSearch(searchParams) {
  try {
    const savedStatus = sessionStorage.getItem("status") || "unconfirmed";
    searchParams.append("tab", savedStatus);
    let url = `${baseUrl}user_admin/controllers/autocomplete.php?${searchParams.toString()}`;
    const response = await fetch(url, { method: "GET" });
    const { success, data } = await response.json();
    if (success) {
      if (savedStatus !== "events") {
        tableRenderer.renderAppointments(data, undefined, DatesUIHelpers.getStatusBadge, getActionButtons);
      } else {
        tableRenderer.renderEventTable(data, DatesUIHelpers.getStatusBadge, getActionButtons);
      }
    }
  } catch (error) {
    console.error("Error al realizar la búsqueda:", error);
  }
}

function handleAutocomplete(e) {
  const input = e.target.id;
  const query = e.target.value;
  const savedStatus = sessionStorage.getItem("status") || "unconfirmed";
  if (query.length >= 3 || input === "status") {
    fetch(`${baseUrl}user_admin/controllers/autocomplete.php?input=${input}&query=${query}&tab=${savedStatus}`)
      .then((res) => res.json())
      .then((data) => {
        if (savedStatus !== "events") {
          tableRenderer.renderAppointments(data.data, undefined, DatesUIHelpers.getStatusBadge, getActionButtons);
        } else {
          tableRenderer.renderEventTable(data.data, DatesUIHelpers.getStatusBadge, getActionButtons);
        }
      });
  } else if (query === "") {
    loadAppointments(savedStatus);
  }
}

// Manejo del formulario de búsqueda
document.getElementById("searchForm").addEventListener("submit", function (e) {
  e.preventDefault();

  const formData = new FormData(this);
  const searchParams = new URLSearchParams();

  // Convertir el FormData a parámetros URL
  for (let [key, value] of formData.entries()) {
    if (value) {
      // Solo agregar parámetros no vacíos
      searchParams.append(key, value);
    }
  }
  // Realizar la búsqueda
  loadAppointmentsWithSearch(searchParams);
});

// Sugerencias de autocompletado (puedes hacer una búsqueda mínima de 3 caracteres)
document.getElementById("service").addEventListener("input", autocomplete);
document.getElementById("name").addEventListener("input", autocomplete);
document.getElementById("phone").addEventListener("input", autocomplete);
document.getElementById("mail").addEventListener("input", autocomplete);
document.getElementById("date").addEventListener("input", autocomplete);
document.getElementById("hour").addEventListener("input", autocomplete);
document.getElementById("status").addEventListener("change", autocomplete);

let lastQuery = "";

async function autocomplete(e) {
  const input = e.target.id;
  const query = e.target.value;
  const savedStatus = sessionStorage.getItem("status") || "unconfirmed";

  if (query.length >= 3 || input === "status") {
    if (query !== lastQuery) {
      lastQuery = query;
      const response = await fetch(`${baseUrl}user_admin/controllers/autocomplete.php?input=${input}&query=${query}&tab=${savedStatus}`);
      const data = await response.json();
      if (savedStatus !== "events") {
        tableRenderer.renderAppointments(data.data, undefined, DatesUIHelpers.getStatusBadge, getActionButtons);
      } else {
        tableRenderer.renderEventTable(data.data, DatesUIHelpers.getStatusBadge, getActionButtons);
      }
    }
  } else if (query === "") {
    const savedStatus = sessionStorage.getItem("status") || "unconfirmed";
    loadAppointments(savedStatus);
  }
}

function offCanvas() {
  // Superior (buscador en datesList)
  const searchOffcanvas = new OffcanvasManager({
    toggleSelector: "#offcanvasToggleSearch",
    menuSelector: "#offcanvasSearch",
    closeSelector: "#offcanvasSearchClose",
    backdropSelector: "#offcanvasSearchBackdrop",
    direction: "top",
    onOpen: () => console.log("Buscador abierto"),
    onClose: () => console.log("Buscador cerrado"),
  });
}

// Función para manejar el modal de autenticación
function handleAuthenticationModal() {
  // cleanBackdrop();
  logAction("Mostrando modal de autenticación");
  showModal("googleAuthenticateModal");

  const confirmButton = document.getElementById("confirmAuthenticate");
  confirmButton.addEventListener("click", function (event) {
    event.preventDefault();
    logAction("Redirigiendo para autenticación en Google");
    window.location.href = `${baseUrl}google_services/google_auth.php`;
  });
}

// Función para manejar la respuesta exitosa
function handleSuccess(data, type) {
  logAction(`Reserva exitosa: ${data.message}`);
  handleInfoModal("Evento creado", data.message);
  if (type == "event") {
    loadAppointments("events");
  } else {
    sessionStorage.setItem("status", "confirmed");
    if (tabManager && typeof tabManager.setActiveTab === "function") {
      tabManager.setActiveTab("confirmed");
    }
    loadAppointments("confirmed");
  }
}

// Función para manejar los errores según el código de respuesta
function handleError(data) {
  // cleanBackdrop();
  logAction(`Manejando error con código: ${data.code}`);
  const errorHandlers = {
    401: () => handleAuthenticationModal(),
    403: () => alert("No tienes permiso para realizar esta acción."),
    503: () => alert("Error de conexión. Intenta nuevamente más tarde."),
    500: () => alert("Hubo un error interno en el servidor. Por favor, intenta más tarde."),
    default: () => alert(data.message || "Hubo un error inesperado."),
  };

  const handleError = errorHandlers[data.code] || errorHandlers.default;
  handleError();
}

// Función para registrar eventos en localStorage
function logAction(message) {
  const logs = JSON.parse(localStorage.getItem("reservationLogs")) || [];
  const timestamp = new Date().toISOString();
  logs.push(`[${timestamp}] ${message}`);
  localStorage.setItem("reservationLogs", JSON.stringify(logs));
  console.log(`[LOG]: ${message}`); // También muestra el log en la consola para depuración en tiempo real
}

function showModal(modalId) {
  DatesUIHelpers.showModal(modalId);
}

function hideModal(modalId) {
  DatesUIHelpers.hideModal(modalId);
}

// Close modals when clicking on close buttons or outside
document.querySelectorAll(".close-modal").forEach((button) => {
  button.addEventListener("click", function () {
    const modal = this.closest('[id$="Modal"], #infoAppointment');
    if (modal) {
      hideModal(modal.id);
    }
  });
});

// Show info modal helper function
function handleInfoModal(title, message) {
  document.getElementById("infoAppointmentLabel").textContent = title;
  document.getElementById("infoAppointmentMessage").textContent = message;
  showModal("infoAppointment");
}
// Función para abrir el modal de eliminación
function openDeleteModal(appointment) {
  const appointmentId = appointment.id_appointment;
  let containerId = `eliminarBtn${appointmentId}`;
  showSpinner(containerId, { size: "1rem" }); // Mostrar spinner de eliminación

  // Mostrar el modal (versión Tailwind)
  showModal("deleteModal");

  // Botón "Eliminar cita"
  document.getElementById("delete-button").addEventListener("click", () => {
    const reason = document.querySelector('input[name="reason"]:checked')?.value || "Otro";
    const notes = document.getElementById("notes").value;
    deleteAppointment(appointment, reason, notes, false); // No generar incidencia
    hideModal("deleteModal");
    resetDeleteModal(appointmentId);
  });

  // Botón "Eliminar y generar incidencia"
  document.getElementById("delete-and-incident-button").addEventListener("click", () => {
    const reason = document.querySelector('input[name="reason"]:checked')?.value || null;
    const notes = document.getElementById("notes").value;

    if (!reason || (reason === "Otro" && !notes.trim())) {
      // No se seleccionó una razón válida, abrir el modal de advertencia
      hideModal("deleteModal");
      showModal("warningModal");
    } else {
      // Si hay una razón, proceder a eliminar y generar la incidencia
      deleteAppointment(appointment, reason, notes, true); // Generar incidencia
      hideModal("deleteModal");
      resetDeleteModal(appointmentId);
    }
  });

  // Botón "Regresar" en el modal de advertencia
  const goBackButton = document.querySelector("#warningModal button");
  if (goBackButton) {
    goBackButton.addEventListener("click", () => {
      hideModal("warningModal");
      showModal("deleteModal");
      hideSpinner(containerId); // Restaurar el botón eliminar al regresar
    });
  }

  // Evento para "Cancelar" en el modal de eliminación
  document.querySelector("#deleteModal .close-modal").addEventListener("click", () => {
    resetDeleteModal(appointmentId); // Restablecer el modal al cancelar
    hideSpinner(containerId); // Restaurar el botón eliminar al cancelar
  });

  // Evento para cerrar haciendo click fuera del modal
  document.getElementById("deleteModal").addEventListener("click", (e) => {
    if (e.target === e.currentTarget) {
      resetDeleteModal(appointmentId);
      hideSpinner(containerId); // Restaurar el botón eliminar al cancelar
    }
  });
}

// Función para resetear el modal de eliminación
function resetDeleteModal(appointmentId) {
  if (appointmentId) {
    showSpinner(appointmentId); // Ocultar el spinner de eliminación
  }

  // Desmarcar todas las opciones de razón
  const reasonInputs = document.querySelectorAll('input[name="reason"]');
  reasonInputs.forEach((input) => (input.checked = false));

  // Limpiar el campo de notas
  document.getElementById("notes").value = "";
}
