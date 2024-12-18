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
                        <td class="form-floating"><input type="date" class="form-control" id="floatingDate"
                                name="event_dates[]" required>
                            <label for="floatingDate">Selecciona una fecha</label>
                        </td>
                        <td class="form-floating"><input type="time" id="floatingStartTime" class="form-control"
                                name="start_time[]" required>
                            <label for="floatingStartTime">Hora de inicio</label>
                        </td>
                        <td class="form-floating"><input type="time" id="floatingEndTime" class="form-control"
                                name="end_time[]" required>
                            <label for="floatingEndTime">Hora de inicio</label>
                        </td>
                        <td class="form-floating"><button type="button"
                                class="btn btn-danger removeRow">Eliminar</button></td>
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
    const response = await fetch(`${baseUrl}user_admin/controllers/unique_events.php`, { method: "GET" });
    const { success, events, url } = await response.json();

    if (!success) {
      alert(`Error: ${result.message}`);
      return;
    }

    const containerEventList = document.querySelector(".event-container");
    containerEventList.innerHTML = "";

    events.forEach((event) => {
      const eventHTML = `
      <div>
        <div>
          <div class="d-sm-flex justify-content-between">
              <div class="d-flex">
                  <h6 class="m-0 align-content-around table-title"> ${event.name}</h6>
                  <span class="cupo_actual align-content-around ms-2">Cupo Actual (${event.cupo_maximo} personas)</span>
              </div>
              <div class="d-flex gap-2 py-2 py-sm-0">
                  <button class="btn btn-success copy-url-btn text-nowrap" data-url="${baseUrl}eventos/${url}">Copiar URL</button>
                  <button class="btn btn-danger delete-event-btn text-nowrap" data-event-id="${event.id}">Eliminar Evento</button>
              </div>
          </div>
          <span class="table-description">${event.description}</span>
        </div>
        <table class="table eventListBody table-borderless">
          <thead>
              <!-- Segunda fila: Encabezados de las columnas -->
              <tr>
                  <th>Fecha</th>
                  <th>Horario</th>
                  <th>Acciones</th>
              </tr>
          </thead>
          <tbody id="events-list-${event.id}">
              <!-- Aquí se cargarán los eventos creados -->
          </tbody>
        </table>
      </div>
      <hr>`;

      containerEventList.innerHTML += eventHTML;

      // Crear filas de fechas y horarios del evento
      const eventListBody = document.querySelector(`#events-list-${event.id}`);
      event.dates.forEach((date) => {
        const dateRow = createEventRow(event.id, date);
        eventListBody.appendChild(dateRow);
      });
    });

    attachEventHandlers(".delete-event-btn", handleDeleteEvent);
    attachEventHandlers(".delete-date-btn", handleDeleteDate);
    attachEventHandlers(".copy-url-btn", handleCopyUrl);
  } catch (error) {
    console.error("Error al obtener los eventos:", error);
    alert("Ocurrió un error inesperado al obtener los eventos.");
  }
}

function createEventRow(eventId, date) {
  const dateRow = document.createElement("tr");
  dateRow.innerHTML = `
    <td>${date.event_date}</td>
    <td>${date.event_start_time} - ${date.event_end_time}</td>
    <td>
      <button class="btn btn-danger delete-date-btn"
              data-event-id="${eventId}"
              data-event-date="${date.event_date}"
              data-start-time="${date.event_start_time}">
        Eliminar
      </button>
    </td>
  `;
  return dateRow;
}

// Asignar controladores de eventos a botones
function attachEventHandlers(selector, handler) {
  const buttons = document.querySelectorAll(selector);
  buttons.forEach((button) => button.addEventListener("click", handler));
}

// Manejar eliminación de evento completo
async function handleDeleteEvent(event) {
  const eventId = event.target.getAttribute("data-event-id");
  if (confirm("¿Estás seguro de que quieres eliminar todo el evento?")) {
    await deleteEventoCompleto(eventId);
  }
}

// Manejar eliminación de fecha específica
async function handleDeleteDate(event) {
  const button = event.target;
  const eventId = button.getAttribute("data-event-id");
  const eventDate = button.getAttribute("data-event-date");
  const startTime = button.getAttribute("data-start-time");

  if (confirm("¿Estás seguro de que quieres eliminar esta fecha del evento?")) {
    await deleteFechaEvento(eventId, eventDate, startTime);
  }
}

// Función para copiar la URL al portapapeles
function handleCopyUrl(event) {
  const button = event.target;
  const url = button.getAttribute("data-url");

  navigator.clipboard
    .writeText(url)
    .then(() => {
      alert("URL copiada al portapapeles");
    })
    .catch((err) => {
      console.error("Error al copiar la URL:", err);
      alert("No se pudo copiar la URL");
    });
}
