// Función para inicializar la lista de citas y tabs
export function initDateList() {
  // Obtener el último estado guardado o usar "unconfirmed" por defecto
  const savedStatus = sessionStorage.getItem("status") || "unconfirmed";

  // Cargar citas para la pestaña correspondiente al último estado guardado
  loadAppointments(savedStatus);

  const triggerTabList = document.querySelectorAll("#myTab button");

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
      const newStatus = event.target.dataset.bsTarget.substring(1);
      sessionStorage.setItem("status", newStatus);
      loadAppointments(newStatus);
    });
  });
}

// Función para cargar citas de acuerdo al estado de la pestaña
async function loadAppointments(status) {
  try {
    let url = `${baseUrl}user_admin/controllers/appointments.php?status=${status}`;

    // Si la pestaña es "events", cambia el endpoint
    if (status === "events") {
      url = `${baseUrl}user_admin/controllers/unique_events.php?status=inscriptions`;
    }

    const response = await fetch(url, { method: "GET" });
    const { success, data } = await response.json();

    if (success) {
      if (status === "events") {
        fillEventTable(data); // Nueva función para llenar la tabla de eventos
      } else {
        fillTable(data); // Tabla de citas
      }
    }
  } catch (error) {
    console.error("Error al obtener citas:", error);
  }
}

// Función para rellenar la tabla con las citas obtenidas
function fillTable(data) {
  const tableContent = document.getElementById("tableContent");
  let html = "";

  data.forEach((appointment) => {
    html += `
          <tr>
              <td data-cell="servicio" class="data">${appointment.service}</td>
              <td data-cell="nombre" class="data">${appointment.name}</td>
              <td data-cell="telefono" class="data">${appointment.phone}</td>
              <td data-cell="correo" class="data">${appointment.mail}</td>
              <td data-cell="fecha" class="data">${appointment.date}</td>
              <td data-cell="hora" class="data">${appointment.start_time}</td>
              <td data-cell="estado" class="data">${appointment.status ? "Confirmada" : "Pendiente"}</td>
              <td class="d-flex justify-content-around">
              ${
                !appointment.status
                  ? `
                <button id="confirmarBtn${appointment.id}" 
                        class="btn btn-success btn-sm confirm" 
                        title="Confirmar reserva"
                        data-id="${appointment.id}">
                  <i class="fas fa-check"></i>
                  <span class="spinner-border spinner-border-sm d-none" aria-hidden="true"></span>
                  <span class="button-text"></span>
                </button>`
                  : ""
              }
              <button id="eliminarBtn${appointment.id}" 
                      class="btn btn-danger btn-sm eliminarReserva" 
                      title="Eliminar reserva"
                      data-id="${appointment.id}">
                <i class="fas fa-trash"></i>
                <span class="spinner-border spinner-border-sm d-none" aria-hidden="true"></span>
                <span class="button-text"></span>
              </button>
              </td>
          </tr>
      `;
  });
  tableContent.innerHTML = html;

  // Añadir listeners para los botones de confirmación y eliminación después de actualizar el contenido
  data.forEach((appointment) => {
    const confirmarBtn = document.getElementById(`confirmarBtn${appointment.id}`);
    const eliminarBtn = document.getElementById(`eliminarBtn${appointment.id}`);

    if (confirmarBtn) {
      confirmarBtn.addEventListener("click", function () {
        confirmReservation(appointment.id);
      });
    }

    eliminarBtn.addEventListener("click", function () {
      deleteAppointment(appointment.id, appointment.event_id);
    });
  });
}
function fillEventTable(data) {
  const tableContent = document.getElementById("tableContent");
  let html = "";

  data.forEach((event) => {
    html += `
          <tr>
              <td data-cell="servicio" class="data">${event.event_name}</td>
              <td data-cell="nombre" class="data">${event.participant_name}</td>
              <td data-cell="telefono" class="data">${event.phone}</td>
              <td data-cell="correo" class="data">${event.email}</td>
              <td data-cell="fecha" class="data">${event.event_date}</td>
              <td data-cell="hora" class="data">${event.event_start_time}</td>
              <td data-cell="estado" class="data">${event.status ? "Confirmada" : "Pendiente"}</td>
              <td class="d-flex justify-content-around">
              ${
                !event.status
                  ? `
                <button id="confirmarBtn${event.inscription_id}" 
                        class="btn btn-success btn-sm confirm" 
                        title="Confirmar reserva"
                        data-id="${event.inscription_id}">
                  <i class="fas fa-check"></i>
                  <span class="spinner-border spinner-border-sm d-none" aria-hidden="true"></span>
                  <span class="button-text"></span>
                </button>`
                  : ""
              }
              <button id="eliminarBtn${event.inscription_id}" 
                      class="btn btn-danger btn-sm eliminarReserva" 
                      title="Eliminar reserva"
                      data-id="${event.inscription_id}">
                <i class="fas fa-trash"></i>
                <span class="spinner-border spinner-border-sm d-none" aria-hidden="true"></span>
                <span class="button-text"></span>
              </button>
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
        confirmReservation(event_list.inscription_id);
      });
    }

    eliminarBtn.addEventListener("click", function () {
      deleteEvent(event_list.inscription_id);
    });
  });
}

export async function confirmReservation(id) {
  try {
    // Log de inicio de la función
    logAction(`Iniciando confirmación de reserva con ID: ${id}`);

    // Mostrar spinner
    addSpinner(id, true, "confirmar");

    const response = await fetch(`${baseUrl}user_admin/confirm.php`, {
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
      handleSuccess(data);
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
function handleSuccess(data) {
  logAction(`Reserva exitosa: ${data.message}`);
  handleInfoModal("infoAppointment", "Evento creado", data.message);

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

// Función para manejar los errores según el código de respuesta
function handleError(data) {
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

export async function deleteAppointment(appointmentID, calendarEventID) {
  addSpinner(appointmentID, true, "eliminar");

  try {
    const response = await fetch(`${baseUrl}user_admin/delete_calendar_event.php`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        calendarEventID: calendarEventID,
        appointmentID: appointmentID,
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
    addSpinner(appointmentID, false, "eliminar");
  }
}

async function deleteEvent(eventID) {
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
  const button = document.getElementById(btn + "Btn" + appointmentID);
  const spinner = button.querySelector(".spinner-border");
  const buttonText = button.querySelector(".button-text");
  const icon = button.querySelector(".fas");
  // Mostrar spinner y deshabilitar botón
  if (activar) {
    spinner.classList.remove("d-none");
    icon.classList.add("d-none");
    button.disabled = true;
  } else {
    spinner.classList.add("d-none");
    icon.classList.remove("d-none");
    button.disabled = false;
  }
}
