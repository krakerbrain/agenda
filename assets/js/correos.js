import { ModalManager } from "./config/ModalManager.js";

export function init() {
  const tipoCorreoSelect = document.getElementById("tipoCorreo");
  const notasContainer = document.getElementById("notasContainer");
  const agregarNotaBtn = document.getElementById("agregarNota");
  const formNotas = document.getElementById("formNotas");
  const eventoSelectContainer = document.getElementById("eventoSelectContainer");

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
      eventoSelectContainer.innerHTML = ""; // Limpiar si no es único
      eventoSelectContainer.classList.add("hidden");
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
        // Construir el select dinámico con Tailwind
        let selectHtml = `
          <div class="mb-4">
            <label for="eventoSelect" class="block text-sm font-medium text-gray-700 mb-1">Seleccionar Evento:</label>
            <select id="eventoSelect" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
              <option value="" disabled selected>Selecciona un evento</option>
              ${events.map((event) => `<option value="${event.id}">${event.name}</option>`).join("")}
            </select>
          </div>
        `;
        eventoSelectContainer.innerHTML = selectHtml;
        eventoSelectContainer.classList.remove("hidden");

        // Agregar evento al select para cargar notas al seleccionar un evento
        const eventoSelect = document.getElementById("eventoSelect");
        eventoSelect.addEventListener("change", () => {
          cargarNotas(tipoActual, dataType);
        });
      } else {
        eventoSelectContainer.innerHTML = '<p class="text-gray-500">No hay eventos disponibles.</p>';
        eventoSelectContainer.classList.remove("hidden");
      }
    } catch (error) {
      console.error("Error al cargar los eventos:", error);
      eventoSelectContainer.innerHTML = '<p class="text-red-500">Error al cargar los eventos.</p>';
      eventoSelectContainer.classList.remove("hidden");
    }
  }

  function obtenerIdEvento(table) {
    if (table === "unique_events") {
      const eventoSelect = document.getElementById("eventoSelect");
      if (eventoSelect) {
        return eventoSelect.value;
      }
    }
    return null;
  }

  async function cargarNotas(tipo, table) {
    try {
      notasContainer.innerHTML = "";
      const eventId = obtenerIdEvento(table);

      const response = await fetch(`${baseUrl}user_admin/controllers/correosController.php`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          action: "getTemplates",
          tipo,
          table,
          eventId,
        }),
      });

      const { success, data } = await response.json();

      if (success) {
        if (data[0].nota_correo != "") {
          data.forEach((nota, index) => agregarNotaAlHtml(index + 1, nota));
        } else {
          agregarNotaAlHtml(0);
        }
      }
    } catch (error) {
      console.error("Error al cargar las notas:", error);
      ModalManager.show("infoModal", {
        title: "Error",
        message: "No se pudieron cargar las notas. Por favor, intente nuevamente.",
      });
    }
  }

  function agregarNotaAlHtml(index, texto = "") {
    let textoNota = texto !== "" ? JSON.parse(texto.nota_correo) : ["No hay notas de correo configuradas"];

    if (!Array.isArray(textoNota)) {
      textoNota = [textoNota];
    }

    textoNota.forEach((nota, i) => {
      const notaDiv = document.createElement("div");
      notaDiv.classList.add("mb-4", "p-4", "border", "border-gray-200", "rounded-lg", "bg-white", "mailCard");
      notaDiv.innerHTML = `
        <div class="flex justify-between items-center mb-2">
          <label for="${tipoActual}Nota${index + i}" class="block text-sm font-medium text-gray-700 form-label">Nota ${index + i}:</label>
          <button type="button" class="eliminarNota inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 cursor-pointer">
            Eliminar Nota
          </button>
        </div>
        <textarea class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
          id="${tipoActual}Nota${index + i}" name="notas[]" rows="3">${nota}</textarea>
      `;
      notasContainer.appendChild(notaDiv);

      notaDiv.querySelector(".eliminarNota").addEventListener("click", () => {
        notaDiv.remove();
        guardarNotas();
      });
    });
  }

  function guardarNotas() {
    const notas = Array.from(notasContainer.querySelectorAll('textarea[name="notas[]"]')).map((nota) => nota.value);
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
      .then(({ success, message }) => {
        if (success) {
          ModalManager.show("infoModal", {
            title: "Éxito",
            message: message || "Notas guardadas correctamente",
          });
        } else {
          throw new Error(message || "Error al guardar las notas");
        }
      })
      .catch((error) => {
        ModalManager.show("infoModal", {
          title: "Error",
          message: error.message,
        });
      });
  }

  agregarNotaBtn.addEventListener("click", () => {
    const index = notasContainer.querySelectorAll(".form-label").length + 1;
    agregarNotaAlHtml(index);
  });

  document.getElementById("saveNotes").addEventListener("click", (event) => {
    event.preventDefault();
    guardarNotas();
  });

  // Cargar notas iniciales
  if (tipoActual == "reserva") {
    cargarNotas(tipoActual, dataType);
  }

  // Configurar popover personalizado (si es necesario)
  // Reemplazar el popover de Bootstrap con una implementación personalizada o librería alternativa
  setupCustomPopovers();

  // Configurar listeners para modales
  ModalManager.setupCloseListeners();
}

// Función alternativa para popovers (opcional)
function setupCustomPopovers() {
  const popoverTriggers = document.querySelectorAll('[data-bs-toggle="popover"]');

  popoverTriggers.forEach((trigger) => {
    trigger.addEventListener("click", (e) => {
      e.preventDefault();
      // Implementar lógica de popover personalizado aquí
      // Puedes usar un modal pequeño o un tooltip avanzado
    });
  });
}
