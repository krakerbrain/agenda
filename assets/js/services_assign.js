import { ModalManager } from "./config/ModalManager.js";

export function init() {
  // Cargar usuarios
  loadUsers();

  // Evento para cargar servicios cuando se selecciona un usuario
  document.getElementById("userSelect").addEventListener("change", function () {
    const userId = this.value;
    const noServicesMsg = document.getElementById("noServicesMessage");
    const container = document.getElementById("servicesContainer");
    container.innerHTML = "";
    if (userId) {
      noServicesMsg.classList.add("hidden");
      loadServicesForUser(userId);
    } else {
      noServicesMsg.classList.remove("hidden");
    }
  });

  // Evento para guardar asignaciones
  document.getElementById("saveAssignments").addEventListener("click", saveAssignments);

  ModalManager.setupCloseListeners();

  async function loadUsers() {
    try {
      const response = await fetch(`${baseUrl}user_admin/controllers/services_assign_controller.php?action=get_users`, {
        method: "GET",
      });

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const data = await response.json();

      if (data.status === "success") {
        const select = document.getElementById("userSelect");

        data.users.forEach((user) => {
          const option = document.createElement("option");
          option.value = user.id;
          option.textContent = user.name;
          select.appendChild(option);
        });

        // Activar el evento change para cargar servicios si hay un usuario seleccionado
        if (select.value) {
          select.dispatchEvent(new Event("change"));
        }
      } else {
        console.error("Error al cargar los usuarios:", data.message);
      }
    } catch (error) {
      console.error("Error de red:", error);
    }
  }

  async function loadServicesForUser(userId) {
    try {
      const noServicesMsg = document.getElementById("noServicesMessage");
      const container = document.getElementById("servicesContainer");

      if (!userId) {
        container.innerHTML = "";
        noServicesMsg.classList.remove("hidden");
        return;
      }
      const response = await fetch(`${baseUrl}user_admin/controllers/services_assign_controller.php?action=get_services&user_id=${userId}`, {
        method: "GET",
      });

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const { status, data } = await response.json();

      if (status !== "success") {
        throw new Error(data.message || "Error al cargar servicios");
      }

      if (data.length === 0) {
        noServicesMsg.classList.remove("hidden");
      } else {
        noServicesMsg.classList.add("hidden");
        renderServicesCards(data);
      }
    } catch (error) {
      console.error("Error al cargar servicios:", error);
      ModalManager.show("infoModal", { title: "Error", message: error.message });
    }
  }

  function renderServicesCards(servicesData) {
    const container = document.getElementById("servicesContainer");
    container.innerHTML = "";

    servicesData.forEach((service) => {
      const card = document.createElement("div");
      card.className = `service-card bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm ${!service.is_enabled ? "opacity-70" : ""}`;

      card.innerHTML = `
            <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <input type="checkbox" 
                           class="form-checkbox h-5 w-5 text-blue-600 service-checkbox"
                           data-service-id="${service.id}"
                           ${service.user_assignment?.is_active ? "checked" : ""}
                           ${!service.is_enabled ? "disabled" : ""}>
                    <span class="font-medium ${!service.is_enabled ? "text-gray-500" : "text-gray-800"}">
                        ${service.name}
                    </span>
                </div>
                <span class="text-xs px-2 py-1 rounded-full ${service.user_assignment?.is_active ? "bg-green-100 text-green-800" : "bg-gray-100 text-gray-800"}">
                    ${service.user_assignment?.is_active ? "Activo" : "Inactivo"}
                </span>
            </div>
            <div class="p-4">
                <h4 class="text-sm font-medium text-gray-500 mb-2">Días disponibles:</h4>
                <div class="flex flex-wrap gap-2">
                    ${generateDaysCheckboxes(service.available_days, service.id)}
                </div>
            </div>
        `;

      container.appendChild(card);
    });
  }

  function generateDaysCheckboxes(availableDays, serviceId) {
    const daysOfWeek = ["L", "M", "M", "J", "V", "S", "D"];

    return daysOfWeek
      .map((day, index) => {
        const dayId = index + 1;
        const dayData = availableDays[dayId] || {};

        // Verificar disponibilidad completa
        const isAvailable = dayData.company_available && dayData.service_available && dayData.user_working;

        const tooltipReasons = [];
        if (!dayData.company_available) tooltipReasons.push("no disponible para la compañía");
        if (!dayData.service_available) tooltipReasons.push("no ofrecido para este servicio");
        if (!dayData.user_working) tooltipReasons.push("fuera del horario laboral");

        const tooltipText = tooltipReasons.length > 0 ? `Día ${tooltipReasons.join(", ")}` : "";

        return `
        <div class="relative inline-block ${!isAvailable ? "opacity-50 cursor-not-allowed" : ""}" 
             ${!isAvailable ? `tabindex="0" title="${tooltipText}"` : ""}>
            <input type="checkbox" 
                   class="form-checkbox h-4 w-4 text-blue-600 day-checkbox" 
                   data-service-id="${serviceId}" 
                   value="${dayId}"
                   ${dayData.user_assigned ? "checked" : ""}
                   ${!isAvailable ? 'disabled aria-disabled="true"' : ""}>
            <label class="block text-center text-sm">${day}</label>
        </div>`;
      })
      .join("");
  }

  async function saveAssignments() {
    const userId = document.getElementById("userSelect").value;
    if (!userId) {
      ModalManager.show("infoModal", { title: "Error", message: "Por favor seleccione un usuario" });
      return;
    }

    // Recopilar datos del formulario
    const assignments = {};
    const serviceCheckboxes = document.querySelectorAll(".service-checkbox");

    serviceCheckboxes.forEach((checkbox) => {
      const serviceId = checkbox.dataset.serviceId;
      const isActive = checkbox.checked;

      if (isActive) {
        // Solo procesar servicios marcados
        const dayCheckboxes = document.querySelectorAll(`.day-checkbox[data-service-id="${serviceId}"]:checked:not(:disabled)`);

        const days = Array.from(dayCheckboxes)
          .map((day) => day.value)
          .join(",");
        assignments[serviceId] = {
          is_active: 1,
          available_days: days,
        };
      }
    });

    try {
      const response = await fetch(`${baseUrl}user_admin/controllers/services_assign_controller.php`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${localStorage.getItem("token")}`,
        },
        body: JSON.stringify({
          action: "save_assignments",
          user_id: userId,
          assignments: assignments,
        }),
      });

      const { status, message } = await response.json();

      if (status === "success") {
        // Recargar los datos para reflejar cambios
        ModalManager.show("infoModal", { title: "Éxito", message });
        loadServicesForUser(userId);
      } else {
        throw new Error(message || "Error al guardar");
      }
    } catch (error) {
      console.error("Error:", error);
      ModalManager.show("infoModal", { title: "Error", message: error.message });
    }
  }
}
