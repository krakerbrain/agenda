export function initCorreos() {
  const tipoCorreoSelect = document.getElementById("tipoCorreo");
  const notasContainer = document.getElementById("notasContainer");
  const agregarNotaBtn = document.getElementById("agregarNota");
  const formNotas = document.getElementById("formNotas");
  const eventoSelectContainer = document.getElementById("eventoSelectContainer");

  // const baseUrl = "tu-url-base-aqui"; // Ajusta según tu proyecto

  let tipoActual = "reserva"; // Valor inicial
  let dataType = "companies";
  tipoCorreoSelect.addEventListener("change", (event) => {
    const selectedOption = event.target.selectedOptions[0];
    dataType = selectedOption.getAttribute("data-type");
    tipoActual = tipoCorreoSelect.value;
    if (dataType === "unique_events") {
      notasContainer.innerHTML = "";
      cargarEventos();
    } else {
      eventoSelectContainer.innerHTML = ""; // Limpiar si no es uni
      cargarNotas(tipoActual, dataType);
    }
  });

  async function cargarEventos() {
    try {
      const response = await fetch(`${baseUrl}user_admin/controllers/unique_events.php`, {
        method: "GET",
      });

      const { success, events } = await response.json();

      if (success && events.length > 0) {
        // Construir el select dinámico con una opción vacía al inicio
        let selectHtml = `
          <label for="eventoSelect" class="form-label">Seleccionar Evento:</label>
          <select id="eventoSelect" class="form-select">
            <option value="" disabled selected>Selecciona un evento</option>
            ${events.map((event) => `<option value="${event.id}">${event.name}</option>`).join("")}
          </select>
        `;
        eventoSelectContainer.innerHTML = selectHtml;

        // Agregar evento al select para cargar notas al seleccionar un evento
        const eventoSelect = document.getElementById("eventoSelect");
        eventoSelect.addEventListener("change", () => {
          cargarNotas(tipoActual, dataType); // Recarga las notas al cambiar de evento
        });
      } else {
        eventoSelectContainer.innerHTML = "<p>No hay eventos disponibles.</p>";
      }
    } catch (error) {
      console.error("Error al cargar los eventos:", error);
      eventoSelectContainer.innerHTML = "<p>Error al cargar los eventos.</p>";
    }
  }

  // Función para obtener el ID del evento seleccionado
  function obtenerIdEvento(table) {
    if (table === "unique_events") {
      const eventoSelect = document.getElementById("eventoSelect");
      if (eventoSelect) {
        return eventoSelect.value; // Devuelve el ID del evento seleccionado
      }
    }
    return null; // Devuelve null si no es un evento o si no hay selección
  }

  async function cargarNotas(tipo, table) {
    try {
      // Limpia las notas actuales
      notasContainer.innerHTML = "";

      // Obtener el ID del evento si es necesario
      const eventId = obtenerIdEvento(table);

      // Realiza la solicitud usando fetch
      const response = await fetch(`${baseUrl}user_admin/controllers/correosController.php`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          action: "getTemplates",
          tipo, // Envía el tipo para obtener las notas correspondientes
          table,
          eventId, // Incluye el ID del evento si es un evento
        }),
      });

      // Convierte la respuesta a JSON
      const { success, data } = await response.json();

      // Procesa las notas si la solicitud fue exitosa
      if (success) {
        if (data[0].nota_correo != "") {
          data.forEach((nota, index) => agregarNotaAlHtml(index + 1, nota));
        } else {
          agregarNotaAlHtml(0);
        }
      }
    } catch (error) {
      console.error("Error al cargar las notas:", error);
    }
  }

  function agregarNotaAlHtml(index, texto = "") {
    let textoNota = texto !== "" ? JSON.parse(texto.nota_correo) : ["No hay notas de correo configuradas"]; // Convierte en arreglo si es necesario

    // Si textoNota no es un arreglo, lo convertimos en uno
    if (!Array.isArray(textoNota)) {
      textoNota = [textoNota];
    }

    textoNota.forEach((nota, i) => {
      const notaDiv = document.createElement("div");
      notaDiv.classList.add("mb-3");
      notaDiv.innerHTML = `
        <div class="d-flex justify-content-between mb-1">
          <label for="${tipoActual}Nota${index + i}" class="form-label">Nota ${index + i}:</label>
          <button type="button" class="btn btn-danger eliminarNota" style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
            Eliminar Nota
          </button>
        </div>
        <textarea class="form-control" id="${tipoActual}Nota${index + i}" name="notas[]" rows="3">${nota}</textarea>
      `;
      notasContainer.appendChild(notaDiv);

      notaDiv.querySelector(".eliminarNota").addEventListener("click", () => {
        notaDiv.remove();
      });
    });
  }

  function guardarNotas() {
    const notas = Array.from(notasContainer.querySelectorAll('textarea[name="notas[]"]')).map((nota) => nota.value);
    // Obtener el ID del evento si es necesario
    const eventId = obtenerIdEvento(dataType);
    fetch(`${baseUrl}user_admin/controllers/correosController.php`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        action: "saveTemplate",
        tipo: tipoActual,
        table: dataType,
        eventId,
        notas,
      }),
    })
      .then((response) => response.json())
      .then(({ success }) => {
        if (success) {
          alert("Notas guardadas exitosamente.");
        } else {
          alert("Error al guardar las notas.");
        }
      });
  }

  agregarNotaBtn.addEventListener("click", () => {
    const index = notasContainer.querySelectorAll(".form-label").length + 1;
    agregarNotaAlHtml(index);
  });

  formNotas.addEventListener("submit", (event) => {
    event.preventDefault();
    guardarNotas();
  });

  // Cargar notas iniciales
  if (tipoActual == "reserva") {
    cargarNotas(tipoActual, dataType);
  }

  const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
  [...popoverTriggerList].map((popoverTriggerEl) => new bootstrap.Popover(popoverTriggerEl));
}
