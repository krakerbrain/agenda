export function initEventosUnicos() {
  getEventos();
  // Inicializa Flatpickr para seleccionar múltiples fechas
  flatpickr("#eventDates", {
    mode: "multiple",
    dateFormat: "d-m-Y", // Cambié el formato a d-m-Y según tu implementación
    minDate: "today",
    disable: [], // Puedes agregar fechas bloqueadas aquí si es necesario
  });

  const form = document.getElementById("uniqueEventForm");
  if (form) {
    form.addEventListener("submit", async function (event) {
      event.preventDefault();

      const formData = new FormData(form);

      try {
        const response = await fetch(`${baseUrl}user_admin/controllers/unique_events.php`, {
          method: "POST",
          body: formData,
        });

        const result = await response.json();

        if (result.success) {
          // Muestra un mensaje de éxito al usuario
          alert("Evento creado exitosamente.");
          // Limpia el formulario o realiza alguna acción adicional si es necesario
          getEventos();
          form.reset();
        } else {
          // Muestra un mensaje de error al usuario
          alert(`Error: ${result.message}`);
        }
      } catch (error) {
        // Manejo de errores en la solicitud
        console.error("Error al enviar el formulario:", error);
        alert("Ocurrió un error inesperado. Intenta nuevamente.");
      }
    });
  }

  // Agregar nueva fila para fecha y hora
  document.getElementById("addDateRow").addEventListener("click", function () {
    var tableBody = document.querySelector("#eventDatesTable tbody");
    var newRow = document.createElement("tr");
    newRow.innerHTML = `
          <td><input type="date" class="form-control" name="event_dates[]" required></td>
          <td><input type="time" class="form-control" name="start_time[]" required></td>
          <td><input type="time" class="form-control" name="end_time[]" required></td>
          <td><button type="button" class="btn btn-danger removeRow">Eliminar</button></td>
      `;
    tableBody.appendChild(newRow);
  });

  // Eliminar fila
  document.querySelector("#eventDatesTable").addEventListener("click", function (event) {
    if (event.target.classList.contains("removeRow")) {
      event.target.closest("tr").remove();
    }
  });
}

async function getEventos() {
  try {
    const response = await fetch(`${baseUrl}user_admin/controllers/unique_events.php`, {
      method: "GET",
    });
    const { success, events } = await response.json();

    if (success) {
      const eventsList = document.getElementById("events-list");
      eventsList.innerHTML = ""; // Limpiar la lista antes de agregar los nuevos eventos

      events.forEach((event) => {
        // Fila principal: Nombre, descripción y botón para eliminar todo el evento
        const eventRow = document.createElement("tr");
        eventRow.innerHTML = `
          <td colspan="3">
            <strong>${event.name}</strong><br>${event.description}
          </td>
          <td>
            <button class="btn btn-danger delete-event-btn" 
                    data-event-id="${event.id}">
              Eliminar Todo
            </button>
          </td>
        `;
        eventsList.appendChild(eventRow);

        // Fila para las fechas y horarios del evento
        event.dates.forEach((date) => {
          const dateRow = document.createElement("tr");
          dateRow.innerHTML = `
            <td style="padding-left: 20px;">${date.event_date}</td>
            <td>${date.event_start_time} - ${date.event_end_time}</td>
            <td>
              <button class="btn btn-danger delete-date-btn" 
                      data-event-id="${event.id}" 
                      data-event-date="${date.event_date}" 
                      data-start-time="${date.event_start_time}">
                Eliminar Fecha
              </button>
            </td>
          `;
          eventsList.appendChild(dateRow);
        });
      });

      // Eventos para eliminar un evento completo
      const deleteEventButtons = document.querySelectorAll(".delete-event-btn");
      deleteEventButtons.forEach((button) => {
        button.addEventListener("click", async function () {
          const eventId = button.getAttribute("data-event-id");
          if (confirm("¿Estás seguro de que quieres eliminar todo el evento?")) {
            await deleteEventoCompleto(eventId);
          }
        });
      });

      // Eventos para eliminar una fecha específica
      const deleteDateButtons = document.querySelectorAll(".delete-date-btn");
      deleteDateButtons.forEach((button) => {
        button.addEventListener("click", async function () {
          const eventId = button.getAttribute("data-event-id");
          const eventDate = button.getAttribute("data-event-date");
          const startTime = button.getAttribute("data-start-time");
          if (confirm("¿Estás seguro de que quieres eliminar esta fecha del evento?")) {
            await deleteFechaEvento(eventId, eventDate, startTime);
          }
        });
      });
    } else {
      alert(`Error: ${result.message}`);
    }
  } catch (error) {
    console.error("Error al obtener los eventos:", error);
    alert("Ocurrió un error inesperado al obtener los eventos.");
  }
}

// Función para eliminar un evento
async function deleteEventoCompleto(eventId) {
  try {
    const response = await fetch(`${baseUrl}user_admin/controllers/unique_events.php`, {
      method: "DELETE",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ event_id: eventId }),
    });
    const { success, message } = await response.json();
    if (success) {
      alert("Evento eliminado correctamente.");
      getEventos(); // Actualizar la lista
    } else {
      alert(`Error al eliminar el evento: ${message}`);
    }
  } catch (error) {
    console.error("Error al eliminar el evento completo:", error);
    alert("Ocurrió un error inesperado al eliminar el evento.");
  }
}

async function deleteFechaEvento(eventId, eventDate, startTime) {
  try {
    const response = await fetch(`${baseUrl}user_admin/controllers/unique_events.php`, {
      method: "DELETE",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        event_id: eventId,
        event_date: eventDate,
        start_time: startTime,
      }),
    });
    const { success, message } = await response.json();
    if (success) {
      alert("Fecha eliminada correctamente.");
      getEventos(); // Actualizar la lista
    } else {
      alert(`Error al eliminar la fecha: ${message}`);
    }
  } catch (error) {
    console.error("Error al eliminar la fecha del evento:", error);
    alert("Ocurrió un error inesperado al eliminar la fecha.");
  }
}
