export function initDateList() {
  const confirmButton = document.querySelector(".confirm");
  const eliminarReserva = document.querySelectorAll(".eliminarReserva");
  if (confirmButton) {
    confirmButton.addEventListener("click", function (event) {
      event.preventDefault();
      confirmReservation(event.currentTarget.dataset.id);
    });
  }
  if (eliminarReserva) {
    eliminarReserva.forEach((button) => {
      button.addEventListener("click", function (event) {
        event.preventDefault();
        deleteAppointment(event.currentTarget.dataset.id, event.currentTarget.dataset.eventid);
      });
    });
  }

  function confirmReservation(id) {
    addSpinner(id, true, "confirmar");
    fetch(`${baseUrl}user_admin/confirm.php`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        id: id,
      }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          if (data.success) {
            alert(data.message);
            location.reload();
          }
        } else {
          alert("Error desconocido al confirmar la reserva.");
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        alert("Error al confirmar la reserva.");
      })
      .finally(() => {
        // Ocultar spinner y habilitar botón después de que la solicitud se complete
        addSpinner(id, false, "confirmar");
      });
  }

  function deleteAppointment(appointmentID, calendarEventID) {
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

  function addSpinner(appointmentID, activar, btn) {
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
}
