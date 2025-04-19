export function initServicesAssign() {
  // Cargar usuarios
  loadUsers();

  // Evento para cargar servicios cuando se selecciona un usuario
  document.getElementById("userSelect").addEventListener("change", function () {
    const userId = this.value;
    if (userId) {
      loadServicesForUser(userId);
    } else {
      document.getElementById("servicesTable").querySelector("tbody").innerHTML = "";
    }
  });

  // Evento para guardar asignaciones
  document.getElementById("saveAssignments").addEventListener("click", saveAssignments);

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

      // Usar daysStatus del backend en lugar del objeto generado
      renderServicesTable(data);
    } catch (error) {
      console.error("Error al cargar servicios:", error);
      handleInfoModal("infoAppointment", "Error", error.message);
    }
  }

  function renderServicesTable(servicesData) {
    const tbody = document.getElementById("servicesTable").querySelector("tbody");
    tbody.innerHTML = "";

    servicesData.forEach((service) => {
      const row = document.createElement("tr");

      // Checkbox principal del servicio
      const assignCell = document.createElement("td");
      const assignCheckbox = document.createElement("input");
      assignCheckbox.type = "checkbox";
      assignCheckbox.className = "form-check-input service-checkbox";
      assignCheckbox.dataset.serviceId = service.id;
      assignCheckbox.checked = service.user_assignment?.is_active || false;
      assignCheckbox.disabled = !service.is_enabled;
      assignCell.appendChild(assignCheckbox);

      // Nombre del servicio
      const nameCell = document.createElement("td");
      nameCell.textContent = service.name;
      if (!service.is_enabled) {
        nameCell.classList.add("text-muted");
      }

      // Días disponibles
      const daysCell = document.createElement("td");
      const daysContainer = document.createElement("div");
      daysContainer.className = "days-container d-flex gap-1";

      // Generar checkboxes de días
      daysContainer.innerHTML = generateDaysCheckboxes(service.available_days, service.id);
      daysCell.appendChild(daysContainer);

      row.append(assignCell, nameCell, daysCell);
      tbody.appendChild(row);
    });

    // Inicializar tooltips para los días deshabilitados
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    [...tooltipTriggerList].map((tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl));
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
        <div class="day position-relative d-inline-block ${!isAvailable ? "disabled-day" : ""}" 
             ${!isAvailable ? `tabindex="0" data-bs-toggle="tooltip" title="${tooltipText}"` : ""}>
            <input type="checkbox" 
                   class="form-check-input day-checkbox" 
                   data-service-id="${serviceId}" 
                   value="${dayId}"
                   ${dayData.user_assigned ? "checked" : ""}
                   ${!isAvailable ? 'disabled aria-disabled="true"' : ""}>
            <label class="d-block text-center mt-1">${day}</label>
        </div>`;
      })
      .join("");
  }

  async function saveAssignments() {
    const userId = document.getElementById("userSelect").value;
    console.log(userId);
    if (!userId) {
      showAlert("Por favor seleccione un usuario", "error");
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
        handleInfoModal("infoAppointment", "Éxito", message);
        loadServicesForUser(userId);
      } else {
        throw new Error(message || "Error al guardar");
      }
    } catch (error) {
      console.error("Error:", error);
    }
  }

  function handleInfoModal(id, title = null, message = null) {
    let titulo = document.getElementById(id + "Label");
    let mensaje = document.getElementById(id + "Message");
    titulo.textContent = title;
    mensaje.textContent = message;
    const modal = new bootstrap.Modal(document.getElementById(id));
    modal.show();
  }
}
