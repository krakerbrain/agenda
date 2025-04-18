export function initServicesAssign() {
  // Inicializar tooltips de Bootstrap
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });

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

      const data = await response.json();

      if (data.status !== "success") {
        throw new Error(data.message || "Error al cargar servicios");
      }

      // Usar daysStatus del backend en lugar del objeto generado
      renderServicesTable(
        data.services,
        data.assignedServices || {},
        data.daysStatus || {} // Usamos los días del backend
      );
    } catch (error) {
      console.error("Error al cargar servicios:", error);
      handleInfoModal("infoAppointment", "Error", error.message);
    }
  }

  function renderServicesTable(services, assignedServices, daysStatus) {
    const tbody = document.getElementById("servicesTable").querySelector("tbody");
    tbody.innerHTML = "";

    services.forEach((service) => {
      const row = document.createElement("tr");
      const isAssigned = assignedServices && assignedServices[service.id];

      // Columna de checkbox para asignar servicio
      const assignCell = document.createElement("td");
      const assignCheckbox = document.createElement("input");
      assignCheckbox.type = "checkbox";
      assignCheckbox.className = "form-check-input service-checkbox";
      assignCheckbox.dataset.serviceId = service.id;
      assignCheckbox.checked = isAssigned ? true : false;
      assignCell.appendChild(assignCheckbox);

      // Columna de nombre de servicio
      const nameCell = document.createElement("td");
      nameCell.textContent = service.name;

      // Columna de días disponibles
      const daysCell = document.createElement("td");
      const daysContainer = document.createElement("div");
      daysContainer.className = "days-container";

      // Generar checkboxes de días (similar a tu función)
      daysContainer.innerHTML = generateDaysCheckboxes(daysStatus, service.id, isAssigned ? assignedServices[service.id].days : {});

      daysCell.appendChild(daysContainer);

      // Construir fila
      row.appendChild(assignCell);
      row.appendChild(nameCell);
      row.appendChild(daysCell);

      tbody.appendChild(row);
    });
  }

  function generateDaysCheckboxes(daysStatus, serviceId, assignedDays = {}) {
    const daysOfWeek = ["L", "M", "M", "J", "V", "S", "D"];

    return daysOfWeek
      .map((day, index) => {
        const dayId = index + 1;
        const { enabled = true } = daysStatus[dayId] || {};

        // Forzar desmarcado si el día está deshabilitado, independientemente de assignedDays
        const shouldBeChecked = enabled && (assignedDays[dayId] || false);

        const disabledClass = !enabled ? "disabled-day" : "";
        const tooltipAttributes = !enabled ? `tabindex="0" data-bs-toggle="tooltip" title="Día no disponible. Habilitarlo en Horarios"` : "";

        return `
            <div class="day align-items-center d-flex flex-column text-center ${disabledClass}" ${tooltipAttributes}>
             <input type="checkbox" class="form-check-input day-checkbox" 
                    data-service-id="${serviceId}" 
                    name="available_service_day[${serviceId}][]" 
                    value="${dayId}" 
                    ${shouldBeChecked ? "checked" : ""} 
                    ${!enabled ? "disabled" : ""}>
              <label class="mt-1">${day}</label>
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
