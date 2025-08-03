// ModalManager for handling notifications
import { ModalManager } from "./config/ModalManager.js";

export function init() {
  fetchBlockedDays();
  const hourRange = document.getElementById("hour-range");

  document.getElementById("all-day").addEventListener("change", function () {
    if (this.checked) {
      hourRange.classList.add("hidden");
      document.getElementById("start-hour").required = false;
      document.getElementById("end-hour").required = false;
    } else {
      hourRange.classList.remove("hidden");
      document.getElementById("start-hour").required = true;
      document.getElementById("end-hour").required = true;
    }
  });

  document.querySelector("#block-date-form button").addEventListener("click", async function (e) {
    e.preventDefault();

    const blockDate = document.getElementById("block-date").value;
    const allDay = document.getElementById("all-day").checked;
    const startHour = document.getElementById("start-hour").value;
    const endHour = document.getElementById("end-hour").value;
    const user_id = e.target.form.user_id.value;

    if (!blockDate) {
      handleModal("Por favor, seleccione una fecha.");
      return;
    }

    const requestData = {
      date: blockDate,
      all_day: allDay,
      start_hour: allDay ? null : startHour,
      end_hour: allDay ? null : endHour,
      user_id: user_id,
    };

    try {
      const response = await fetch(`${baseUrl}user_admin/controllers/blockHourController.php`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(requestData),
      });

      const result = await response.json();

      if (result.success) {
        handleModal("Fecha bloqueada correctamente.", "Éxito");
        // Limpiar formulario
        hourRange.style.display = "none";
        document.getElementById("block-date-form").reset();
        fetchBlockedDays(user_id);
      } else {
        handleModal(result.message, "Error");
      }
    } catch (error) {
      console.error("Error:", error);
    }
  });

  async function fetchBlockedDays(user_id = null) {
    let userIdUrl = user_id !== null ? `?user_id=${user_id}` : "";
    const response = await fetch(`${baseUrl}user_admin/controllers/getDeleteBlockedHours.php${userIdUrl}`);
    const { success, data } = await response.json();

    if (success) {
      updateBlockedDatesTable(data);
    } else {
      handleModal(result.message || "Error al obtener los días bloqueados.", "Error");
    }
  }

  if (document.getElementById("userSelect")) {
    document.getElementById("userSelect").addEventListener("change", async function () {
      const userId = this.value;
      await fetchBlockedDays(userId);
    });
  }
  function updateBlockedDatesTable(blockedDays) {
    const container = document.getElementById("blocked-dates-list");
    container.innerHTML = ""; // Limpiar el contenedor

    if (blockedDays.length === 0) {
      container.innerHTML = `
            <div class="col-span-full text-center py-4 text-gray-500">
                No hay fechas bloqueadas.
            </div>
        `;
      return;
    }

    blockedDays.forEach((day) => {
      const card = `
            <div class="relative bg-white rounded-lg shadow p-4 border border-gray-200 hover:shadow-md transition-shadow">
                <!-- Botón de eliminar en esquina superior derecha -->
                <button class="absolute top-2 right-2 text-red-500 hover:text-red-700 deleteBlockedDay" 
                        data-token="${day.token}" 
                        title="Eliminar fecha bloqueada">
                    <i class="fas fa-trash-alt"></i>
                </button>
                
                <!-- Contenido de la card -->
                <div class="space-y-2">
                    <div class="flex items-center">
                        <i class="fa fa-calendar-alt text-gray-500 mr-2"></i>
                        <span class="font-medium">${day.date}</span>
                    </div>
                    
                    <div class="flex items-center">
                        <i class="fa fa-clock text-gray-500 mr-2"></i>
                        <span>${day.start_time || "Todo el día"}</span>
                        ${day.start_time ? " - " + day.end_time : ""}
                    </div>
                </div>
            </div>
        `;
      container.innerHTML += card;
    });

    // Agregar event listeners a los botones de eliminar
    document.querySelectorAll(".deleteBlockedDay").forEach((button) => {
      button.addEventListener("click", function (event) {
        const token = this.getAttribute("data-token");
        deleteBlockedDay(token);
      });
    });
  }

  async function deleteBlockedDay(token) {
    const requestData = {
      token,
    };

    try {
      const response = await fetch(`${baseUrl}user_admin/controllers/getDeleteBlockedHours.php`, {
        method: "DELETE",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(requestData),
      });

      const result = await response.json();

      if (result.success) {
        handleModal("Fecha desbloqueada correctamente.", "Éxito");
        let user_id = document.querySelector("#userSelect").value;
        fetchBlockedDays(user_id);
      } else {
        handleModal(result.message, "Error");
      }
    } catch (error) {
      console.error("Error:", error);
    }
  }

  function handleModal(message, title = "Información") {
    ModalManager.show("infoModal", {
      title: title,
      message: message,
    });
  }

  ModalManager.setupCloseListeners();
}
