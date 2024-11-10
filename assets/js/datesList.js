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
    const response = await fetch(`${baseUrl}user_admin/controllers/appointments.php?status=${status}`, {
      method: "GET",
    });

    const { success, data } = await response.json();

    if (success) {
      fillTable(data);
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

export async function confirmReservation(id) {
  try {
    // Mostrar spinner
    addSpinner(id, true, "confirmar");

    const response = await fetch(`${baseUrl}user_admin/confirm.php`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        id: id,
      }),
    });

    const data = await response.json();

    if (data.success) {
      alert(data.message);

      // Cambiar el tab a "confirmed" y actualizar la sesión
      sessionStorage.setItem("status", "confirmed");

      // Mostrar el tab "confirmed"
      const confirmedTabTrigger = document.querySelector('button[data-bs-target="#confirmed"]');
      if (confirmedTabTrigger) {
        const confirmedTab = new bootstrap.Tab(confirmedTabTrigger);
        confirmedTab.show();
      }

      loadAppointments("confirmed");
      // location.reload(); // Recargar la página si la confirmación fue exitosa
    } else {
      alert("Error desconocido al confirmar la reserva.");
    }
  } catch (error) {
    console.error("Error:", error);
    alert("Error al confirmar la reserva.");
  } finally {
    // Ocultar spinner y habilitar botón después de que la solicitud se complete
    addSpinner(id, false, "confirmar");
  }
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

    const data = await response.json();

    if (data.success) {
      alert(data.message);
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
