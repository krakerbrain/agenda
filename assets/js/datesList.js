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
      });
  }

  function deleteAppointment(appointmentID, calendarEventID) {
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
      });
  }
}
