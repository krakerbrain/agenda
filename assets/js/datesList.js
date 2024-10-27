export function initDateList() {
  async function loadAppointments(status = "unconfirmed") {
    try {
      const response = await fetch(`${baseUrl}user_admin/controllers/appointments.php?status=${status}`, {
        method: "GET",
      });

      const { success, data } = await response.json();

      if (success) {
        // Llenar la tabla correspondiente según el estado
        if (status === "unconfirmed") {
          fillTable(data, "unconfirmedAppointmentsList");
        } else if (status === "confirmed") {
          fillTable(data, "confirmedAppointmentsList");
        } else if (status === "past") {
          fillTable(data, "pastAppointmentsList");
        } else {
          fillTable(data, "appointmentsList"); // Para 'all'
        }
      }
    } catch (error) {
      console.error(error);
    }
  }

  // Cargar citas inicialmente para la pestaña "Todas"
  loadAppointments("unconfirmed");

  const triggerTabList = document.querySelectorAll("#myTab button");
  triggerTabList.forEach((triggerEl) => {
    const tabTrigger = new bootstrap.Tab(triggerEl);

    triggerEl.addEventListener("click", (event) => {
      event.preventDefault();
      const status = event.target.dataset.bsTarget.substring(1);
      loadAppointments(status);
      tabTrigger.show();
    });
  });
}

function fillTable(data, tableId) {
  const appointmentsList = document.getElementById(tableId);
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
  appointmentsList.innerHTML = html;

  // Agrega los event listeners para los botones después de que el HTML se haya renderizado
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
      location.reload(); // Recargar la página si la confirmación fue exitosa
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

export function deleteAppointment(appointmentID, calendarEventID) {
  addSpinner(appointmentID, true, "eliminar");
  fetch(`${baseUrl}user_admin/delete_calendar_event.php`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      calendarEventID: calendarEventID,
      appointmentID: appointmentID,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        alert(data.message);
        location.reload();
      } else {
        alert("Error desconocido al eliminar la reserva.");
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      alert("Error al eliminar la reserva.");
    })
    .finally(() => {
      // Ocultar spinner y habilitar botón después de que la solicitud se complete
      addSpinner(appointmentID, false, "eliminar");
    });
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
