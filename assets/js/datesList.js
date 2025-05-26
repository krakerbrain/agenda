export function init() {
  // Obtener el último estado guardado o usar "unconfirmed" por defecto
  const savedStatus = sessionStorage.getItem("status") || "unconfirmed";

  // Cargar citas para la pestaña correspondiente al último estado guardado
  loadAppointments(savedStatus);

  const triggerTabList = document.querySelectorAll("#myTab button");
  // Seleccionar el tab correspondiente al estado guardado
  if (triggerTabList) {
    triggerTabList.forEach((triggerEl) => {
      const status = triggerEl.dataset.bsTarget.substring(1); // Extraer el estado del atributo data-bs-target
      if (status === savedStatus) {
        const tabTrigger = new bootstrap.Tab(triggerEl);
        tabTrigger.show(); // Mostrar el tab correspondiente al estado guardado
      }

      // Agregar el evento de clic para cambiar de tab y actualizar el estado en sessionStorage
      triggerEl.addEventListener("click", (event) => {
        event.preventDefault();
        document.querySelector("#searchForm").reset();
        const newStatus = event.target.dataset.bsTarget.substring(1);
        sessionStorage.setItem("status", newStatus);
        loadAppointments(newStatus);
      });
    });
  }
}

let currentPage = 1;
const limit = 10;

// Función para cargar citas de acuerdo al estado de la pestaña
async function loadAppointments(status, page = 1) {
  try {
    let url = `${baseUrl}user_admin/controllers/appointments.php?status=${status}&page=${page}`;

    // Si la pestaña es "events", cambia el endpoint
    if (status === "events") {
      url = `${baseUrl}user_admin/controllers/unique_events.php?status=inscriptions&page=${page}`;
    }

    const response = await fetch(url, { method: "GET" });
    const { success, data, show_provider_column, is_owner } = await response.json();

    if (success) {
      if (status === "events") {
        fillEventTable(data); // Nueva función para llenar la tabla de eventos
      } else {
        fillTable(data, show_provider_column, is_owner);
        currentPage = page;
        // Si el número de citas recibidas es menor que el límite, no hay más páginas
        const hasMoreData = data.length === limit;
        updatePaginationControls(hasMoreData);
      }
    }
  } catch (error) {
    console.error("Error al obtener citas:", error);
  }
}

// Función para rellenar la tabla con las citas obtenidas
function fillTable(data, showProviderColumn, isOwner) {
  if (!Array.isArray(data)) {
    console.warn("fillTable recibió un dato no válido:", data);
    data = [];
  }
  const tableContent = document.getElementById("tableContent");
  tableContent.innerHTML = "";
  data.forEach((item) => {
    const row = document.createElement("tr");
    row.classList.add("body-table");

    row.innerHTML = `
      <td data-cell='servicio' class='data'>${item.service}</td>
      <td data-cell='categoria' class='data'>${item.category}</td>
      <td data-cell='nombre' class='data'>${item.name}</td>
      <td data-cell='telefono' class='data'>
        <i class="fab fa-whatsapp pe-1" style="font-size:0.85rem"></i>
        <a href="https://wa.me/${item.phone}" target="_blank">+${item.phone}</a>
      </td>
      <td data-cell='correo' class='data'>${item.mail}</td>
      ${showProviderColumn ? `<td data-cell='prestador' class='data'>${item.provider_name}</td>` : ""}
      <td data-cell='fecha' class='data'>${item.date}</td>
      <td data-cell='hora' class='data'>${item.start_time}</td>
      <td data-cell='estado' class='data'>${getStatusBadge(item.status)}</td>
      <td><div class="actionBtns d-flex justify-content-evenly">${getActionButtons(item)}</div></td>
    `;

    tableContent.appendChild(row);
  });

  updateTableHeaders(showProviderColumn);
}

function updateTableHeaders(showProviderColumn = false) {
  const headers = `
    <th>Servicio</th>
    <th>Categoria</th>
    <th>Nombre</th>
    <th>Teléfono</th>
    <th>Correo</th>
    ${showProviderColumn ? "<th>Prestador</th>" : ""}
    <th>Fecha</th>
    <th>Hora</th>
    <th>Estado</th>
    <th>Acción</th>
  `;

  document.querySelector(".head-table").innerHTML = headers;
}

function getStatusBadge(status) {
  const statusMap = {
    0: { text: "Pendiente", class: "badge-primary text-primary" },
    1: { text: "Confirmada", class: "badge-success text-success" },
    // Agrega más estados si es necesario
  };

  const statusInfo = statusMap[status] || { text: "Desconocido", class: "badge-secondary" };

  return `<span class="badge ${statusInfo.class}">${statusInfo.text}</span>`;
}

function getActionButtons(data) {
  let buttons = "";

  // Botón de confirmar (solo para citas pendientes)
  if (data.status === 0) {
    buttons += `
                <i id="confirmarBtn${data.id_appointment}" class="fa-solid fa-square-check action-icon text-success text-center confirm" title="Confirmar reserva" data-id="${data.id_appointment}">
                      <span class="button-text">CONFIRMAR</span>
                      <span class="spinner-border spinner-border-sm d-none text-success"></span>
                </i>
              `;
  }
  // Botón de eliminar (siempre visible)
  buttons += `
             <i id="eliminarBtn${data.id_appointment}" class="fas fa-trash action-icon text-danger text-center eliminarReserva" title="Eliminar reserva" data-id="${data.id_appointment}">
                <span class="button-text">ELIMINAR</span>
                <span class="spinner-border spinner-border-sm d-none text-danger"></span>
             </i>`;

  document.addEventListener("click", function (e) {
    if (e.target.closest(`#confirmarBtn${data.id_appointment}`)) {
      confirmReservation(data.id_appointment);
    }

    if (e.target.closest(`#eliminarBtn${data.id_appointment}`)) {
      openDeleteModal(data);
    }
  });

  return buttons;
}

function fillEventTable(data) {
  const tableContent = document.getElementById("tableContent");
  let html = "";

  data.forEach((event) => {
    html += `
         <tr class="body-table">
              <td data-cell="servicio" class="data">${event.event_name}</td>
              <td data-cell="nombre" class="data">${event.participant_name}</td>
              <td data-cell="telefono" class="data"><i class="fab fa-whatsapp pe-1" style="font-size:0.85rem"></i><a href="https://wa.me/${event.phone}" target="_blank">+${event.phone}</a></td>
              <td data-cell="correo" class="data">${event.email}</td>
              <td data-cell="fecha" class="data">${event.event_date}</td>
              <td data-cell="hora" class="data">${event.event_start_time}</td>
              <td data-cell="estado" class="data">${event.status ? "Confirmada" : "Pendiente"}</td>
              <td class="d-flex justify-content-around">
              ${
                !event.status
                  ? `
                <i id="confirmarBtn${event.inscription_id}" 
                   class="fas fa-check action-icon text-success text-center confirm" 
                   title="Confirmar reserva"
                   data-id="${event.inscription_id}"
                   data-type="event">
                   <span class="button-text">CONFIRMAR</span>
                   <span class="spinner-border spinner-border-sm d-none" aria-hidden="true"></span>
                </i>`
                  : ""
              }
                <i id="eliminarBtn${event.inscription_id}" 
                   class="fas fa-trash action-icon text-danger text-center eliminarReserva" 
                   title="Eliminar reserva"
                   data-id="${event.inscription_id}"
                   data-type="evento">
                   <span class="button-text">ELIMINAR</span>
                   <span class="spinner-border spinner-border-sm d-none" aria-hidden="true"></span>
                </i>
              </td>
          </tr>
      `;
  });
  tableContent.innerHTML = html;

  // Añadir listeners para los botones de confirmación y eliminación después de actualizar el contenido
  data.forEach((event_list) => {
    const confirmarBtn = document.getElementById(`confirmarBtn${event_list.inscription_id}`);
    const eliminarBtn = document.getElementById(`eliminarBtn${event_list.inscription_id}`);

    if (confirmarBtn) {
      confirmarBtn.addEventListener("click", function () {
        const type = confirmarBtn.getAttribute("data-type");
        confirmReservation(event_list.inscription_id, type);
      });
    }

    eliminarBtn.addEventListener("click", function () {
      deleteEvent(event_list.inscription_id);
    });
  });
}

// Función para actualizar los controles de paginación
function updatePaginationControls(hasMoreData) {
  document.getElementById("currentPage").innerText = `Página ${currentPage}`;
  document.getElementById("prevPage").disabled = currentPage === 1;
  document.getElementById("nextPage").disabled = !hasMoreData; // Deshabilitar "Siguiente" si no hay más datos
}

document.getElementById("prevPage").addEventListener("click", () => {
  if (currentPage > 1) {
    loadAppointments("all", currentPage - 1);
  }
});

document.getElementById("nextPage").addEventListener("click", () => {
  loadAppointments("all", currentPage + 1);
});

export async function confirmReservation(id, type = null) {
  try {
    // Log de inicio de la función
    logAction(`Iniciando confirmación de reserva con ID: ${id}`);

    // Mostrar spinner
    addSpinner(id, true, "confirmar");
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
    // Ocultar spinner y habilitar botón después de que la solicitud se complete
    addSpinner(id, false, "confirmar");
    logAction("Finalizando confirmación de reserva");
  }
}

// Función para manejar el modal de autenticación
function handleAuthenticationModal() {
  cleanBackdrop();
  logAction("Mostrando modal de autenticación");
  const modal = new bootstrap.Modal(document.getElementById("googleAuthenticateModal"));
  modal.show();

  const confirmButton = document.getElementById("confirmAuthenticate");
  confirmButton.addEventListener("click", function (event) {
    event.preventDefault();
    logAction("Redirigiendo para autenticación en Google");
    window.location.href = `${baseUrl}google_services/google_auth.php`;
  });
}

// Función para manejar la respuesta exitosa
function handleSuccess(data, type) {
  cleanBackdrop();
  logAction(`Reserva exitosa: ${data.message}`);
  handleInfoModal("infoAppointment", "Evento creado", data.message);
  if (type == "event") {
    loadAppointments("events");
  } else {
    // Cambiar el tab a "confirmed" y actualizar la sesión
    sessionStorage.setItem("status", "confirmed");

    // Mostrar el tab "confirmed"
    const confirmedTabTrigger = document.querySelector('button[data-bs-target="#confirmed"]');
    if (confirmedTabTrigger) {
      const confirmedTab = new bootstrap.Tab(confirmedTabTrigger);
      confirmedTab.show();
    }

    loadAppointments("confirmed");
  }
}

// Función para manejar los errores según el código de respuesta
function handleError(data) {
  cleanBackdrop();
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

// Función para abrir el modal
function openDeleteModal(appointment) {
  cleanBackdrop();
  let appointmentId = appointment.id_appointment;
  addSpinner(appointmentId, true, "eliminar");
  const modal = new bootstrap.Modal(document.getElementById("deleteModal"));

  // Mostrar el modal
  modal.show();

  // Botón "Eliminar cita"
  document.getElementById("delete-button").addEventListener("click", () => {
    const reason = document.querySelector('input[name="reason"]:checked')?.value || "Otro";
    const notes = document.getElementById("notes").value;
    deleteAppointment(appointment, reason, notes, false); // No generar incidencia
    modal.hide();
  });

  document.getElementById("delete-and-incident-button").addEventListener("click", () => {
    const reason = document.querySelector('input[name="reason"]:checked')?.value || null;
    const notes = document.getElementById("notes").value;

    if (!reason) {
      // No se seleccionó una razón, abrir el modal de advertencia
      const warningModal = new bootstrap.Modal(document.getElementById("warningModal"));
      warningModal.show();
    } else {
      // Si hay una razón, proceder a eliminar y generar la incidencia
      deleteAppointment(appointment, reason, notes, true); // Generar incidencia
      resetDeleteModal(); // Limpiar el modal después de eliminar
    }
  });

  // Botón "Aceptar" en el modal de advertencia. verificar antes si el botón existe
  if (document.getElementById("go-back-button")) {
    document.getElementById("go-back-button").addEventListener("click", () => {
      const warningModal = new bootstrap.Modal(document.getElementById("warningModal"));
      warningModal.hide(); // Cerrar el modal de advertencia
    });
  }

  // Evento para "Cancelar" en el modal de eliminación
  document.querySelector("#deleteModal .btn-secondary").addEventListener("click", () => {
    resetDeleteModal(appointmentId); // Restablecer el modal al cancelar
  });

  // Evento para cerrar el modal de eliminación con la "X"
  document.querySelector("#deleteModal .btn-close").addEventListener("click", () => {
    resetDeleteModal(appointmentId); // Restablecer el modal al cerrar
  });
}

function resetDeleteModal(appointmentId) {
  addSpinner(appointmentId, false, "eliminar"); // Ocultar el spinner de eliminación
  // Desmarcar todas las opciones de razón
  const reasonInputs = document.querySelectorAll('input[name="reason"]');
  reasonInputs.forEach((input) => (input.checked = false));

  // Limpiar el campo de notas
  document.getElementById("notes").value = "";
}
export async function deleteAppointment(appointment, reason, notes, generateIncident) {
  addSpinner(appointment.id_appointment, false, "eliminar");

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
      handleInfoModal("infoAppointment", "Evento eliminado", data.message);
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
    addSpinner(appointment.id_appointment, false, "eliminar");
  }
}

async function deleteEvent(eventID) {
  //
  addSpinner(eventID, true, "eliminar");

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
      handleInfoModal("infoAppointment", "Evento eliminado", data.message);
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
    addSpinner(eventID, false, "eliminar");
  }
}

export function handleInfoModal(id, title = null, message = null) {
  let titulo = document.getElementById(id + "Label");
  let mensaje = document.getElementById(id + "Message");
  titulo.textContent = title;
  mensaje.textContent = message;
  const modal = new bootstrap.Modal(document.getElementById(id));
  modal.show();
}

export function addSpinner(appointmentID, activar, btn) {
  const elementId = `${btn}Btn${appointmentID}`; // "confirmarBtn123" o "eliminarBtn123"
  const icon = document.getElementById(elementId);
  if (!icon) return;

  const spinner = icon.querySelector(".spinner-border");
  const text = icon.querySelector(".button-text");

  if (activar) {
    spinner.classList.remove("d-none");
    text.classList.add("d-none"); // solo ocultás el texto
    icon.classList.add("disabled");
    icon.classList.add("loading");
  } else {
    spinner.classList.add("d-none");
    text.classList.remove("d-none");
    icon.classList.remove("disabled");
    icon.classList.remove("loading");
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

// Función para realizar la búsqueda con parámetros de búsqueda
async function loadAppointmentsWithSearch(searchParams) {
  try {
    const savedStatus = sessionStorage.getItem("status") || "unconfirmed"; // Obtén el tab actual
    searchParams.append("tab", savedStatus); // Agrega el contexto del tab
    let url = `${baseUrl}user_admin/controllers/autocomplete.php?${searchParams.toString()}`;

    const response = await fetch(url, { method: "GET" });
    const { success, data } = await response.json();

    if (success) {
      if (savedStatus != "events") {
        fillTable(data); // Mostrar los datos filtrados
      } else {
        fillEventTable(data);
      }
    }
  } catch (error) {
    console.error("Error al realizar la búsqueda:", error);
  }
}

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

  if (query.length >= 3 || input == "status") {
    if (query !== lastQuery) {
      lastQuery = query;
      const response = await fetch(`${baseUrl}user_admin/controllers/autocomplete.php?input=${input}&query=${query}&tab=${savedStatus}`);
      const data = await response.json();
      if (savedStatus != "events") {
        fillTable(data.data);
      } else {
        fillEventTable(data.data);
      }
    }
  } else if (query === "") {
    const savedStatus = sessionStorage.getItem("status") || "unconfirmed";
    loadAppointments(savedStatus);
  }
}

function cleanBackdrop() {
  document.querySelectorAll(".modal-backdrop").forEach((el) => el.remove());
}
