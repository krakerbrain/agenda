import { ModalManager } from "./config/ModalManager.js";
export function init() {
  const form = document.getElementById("workScheduleForm");
  // const tableBody = document.getElementById("scheduleTableBody");
  // obtener value de usario selected

  let initialSchedules = [];

  async function getHorarios(user_id = null) {
    let userIdUrl = user_id !== null ? `?user_id=${user_id}` : "";

    const response = await fetch(`${baseUrl}user_admin/controllers/schedulesController.php${userIdUrl}`, {
      method: "GET",
    });
    const { success, data } = await response.json();

    if (success) {
      // Guardar una copia de los datos iniciales
      initialSchedules = JSON.parse(JSON.stringify(data)); // Copia profunda para evitar referencias
      // Limpiar el cuerpo de la tabla
      // tableBody.innerHTML = "";
      limpiarContenedoresHorario();

      if (data.length > 0) {
        data.forEach((horario) => {
          addScheduleToTable(horario);
        });
      }
    }
  }

  function limpiarContenedoresHorario() {
    const containers = document.querySelectorAll(".schedule-body");
    containers.forEach((container) => {
      container.innerHTML = "";
    });
  }

  // evento chaange para userSelect
  if (document.getElementById("userSelect")) {
    document.getElementById("userSelect").addEventListener("change", async function () {
      const userId = this.value;
      await getHorarios(userId);
    });
  }

  function getCurrentSchedules() {
    // Buscar el contenedor visible
    const scheduleBodies = document.querySelectorAll(".schedule-body");
    let visibleContainer = null;

    scheduleBodies.forEach((container) => {
      const style = window.getComputedStyle(container);
      if (style.display !== "none") {
        visibleContainer = container;
      }
    });

    if (!visibleContainer) return [];

    const rows = Array.from(visibleContainer.querySelectorAll(".work-day"));

    return rows.map((row) => {
      const isMobile = row.tagName === "DIV";
      const dayElement = isMobile
        ? row.querySelector("span") // Primer span es el día en mobile
        : row.querySelector("td[data-cell='día']"); // En desktop

      const day = dayElement?.textContent.trim() || "";

      const getInputValue = (namePart) => {
        const input = row.querySelector(`input[name^="schedule[${day}][${namePart}]"]`);
        return input ? input.value : null;
      };

      const getChecked = (namePart) => {
        const input = row.querySelector(`input[name^="schedule[${day}][${namePart}]"]`);
        return input && input.checked ? 1 : 0;
      };

      return {
        schedule_id: getInputValue("schedule_id"),
        day_id: getInputValue("day_id"),
        day,
        is_enabled: getChecked("is_enabled"),
        work_start: getInputValue("start"),
        work_end: getInputValue("end"),
        break_start: getInputValue("break_start"),
        break_end: getInputValue("break_end"),
      };
    });
  }

  form.addEventListener("input", () => {
    const currentSchedules = getCurrentSchedules();
    showSaveAlert(hasChanges(currentSchedules));
  });

  function showSaveAlert(haveChanges) {
    const alertBox = document.getElementById("unsavedChangesAlert");

    if (haveChanges) {
      alertBox.classList.remove("hidden");
    } else {
      alertBox.classList.add("hidden");
    }
  }

  function hasChanges(currentSchedules) {
    // Si las longitudes no coinciden, hay cambios
    if (currentSchedules.length !== initialSchedules.length) return true;

    // Comparar cada elemento
    return currentSchedules.some((current, index) => {
      const initial = initialSchedules[index];
      return (
        current.work_start !== initial.work_start ||
        current.work_end !== initial.work_end ||
        current.break_start !== initial.break_start ||
        current.break_end !== initial.break_end ||
        current.is_enabled !== initial.is_enabled
      );
    });
  }

  function addScheduleToTable(horario) {
    addScheduleCardMobile(horario);
    addScheduleRowDesktop(horario);
  }

  function addScheduleRowDesktop(horario) {
    const { schedule_id, day_id, day, work_start, work_end, break_start, break_end, is_enabled } = horario;
    const tableBody = document.getElementById("scheduleTableBodyDesktop");

    const tr = document.createElement("tr");
    tr.classList.add("body-table-config", "work-day");

    const checked = is_enabled === 1 ? "checked" : "";
    const disabled = is_enabled === 1 ? "" : "disabled";

    tr.innerHTML = `
    <td class="px-3 py-2">
      <input class="form-checkbox h-4 w-4 text-cyan-600" type="checkbox" ${checked}>
      <input type="hidden" name="schedule[${day}][is_enabled]" value="${is_enabled}">
    </td>
    <td class="px-3 py-2 font-medium text-gray-800">${day}</td>
    <td class="px-3 py-2">
      <div class="flex space-x-2">
        <input type="time" class="w-full border rounded px-2 py-1" name="schedule[${day}][start]" value="${work_start}" ${disabled}>
        <input type="time" class="w-full border rounded px-2 py-1" name="schedule[${day}][end]" value="${work_end}" ${disabled}>
      </div>
    </td>
    <td class="px-3 py-2 space-x-2">
      <button type="button"
              class="descanso bg-cyan-50 hover:bg-cyan-100 text-cyan-700 border border-cyan-200 rounded px-3 py-1 text-xs font-medium"
              ${disabled}>
        + Descanso
      </button>
      ${day === "Lunes" ? "<button type='button' class='text-cyan-600 hover:text-cyan-800 text-xs underline copy-all ml-2'>Copiar en todos</button>" : ""}
    </td>
    <input type="hidden" name="schedule[${day}][schedule_id]" value="${schedule_id}">
    <input type="hidden" name="schedule[${day}][day_id]" value="${day_id}">
  `;

    tableBody.appendChild(tr);

    tr.querySelector(".descanso").addEventListener("click", () => {
      addNewBreakTime(tr.querySelector(".descanso"), day);
    });

    tr.querySelector(".form-checkbox").addEventListener("change", async (e) => {
      changeDayStatus(e, day);
    });

    if (break_start && break_end && is_enabled) {
      addBreakTimeElement(tr, day, break_start, break_end);
    }

    if (tr.querySelector(".copy-all")) {
      tr.querySelector(".copy-all").addEventListener("click", copiarEnTodos);
    }
  }

  function addScheduleCardMobile(horario) {
    const { schedule_id, day_id, day, work_start, work_end, break_start, break_end, is_enabled } = horario;
    const tableBody = document.getElementById("scheduleTableBodyMobile");

    const card = document.createElement("div");
    card.classList.add("p-4", "bg-white", "rounded-lg", "shadow", "space-y-3", "work-day");

    const checked = is_enabled === 1 ? "checked" : "";
    const disabled = is_enabled === 1 ? "" : "disabled";

    card.innerHTML = `
    <div class="flex items-center justify-between">
      <span class="text-base font-semibold text-gray-800">${day}</span>
      <label class="flex items-center space-x-2">
        <span class="text-xs text-gray-500">Estado:</span>
        <input class="form-checkbox h-4 w-4 text-cyan-600" type="checkbox" ${checked}>
        <input type="hidden" name="schedule[${day}][is_enabled]" value="${is_enabled}">
      </label>
    </div>

    <div>
      <span class="block text-xs text-gray-500 mb-1">Jornada</span>
      <div class="flex space-x-2">
        <input type="time" class="w-full border rounded px-2 py-1" name="schedule[${day}][start]" value="${work_start}" ${disabled}>
        <input type="time" class="w-full border rounded px-2 py-1" name="schedule[${day}][end]" value="${work_end}" ${disabled}>
      </div>
    </div>

    <div class="flex justify-between items-center">
      <button type="button"
              class="descanso bg-cyan-50 hover:bg-cyan-100 text-cyan-700 border border-cyan-200 rounded px-3 py-1 text-xs font-medium"
              ${disabled}>
        + Descanso
      </button>
      ${day === "Lunes" ? "<button type='button' class='text-cyan-600 hover:text-cyan-800 text-xs underline copy-all'>Copiar en todos</button>" : ""}
    </div>

    <input type="hidden" name="schedule[${day}][schedule_id]" value="${schedule_id}">
    <input type="hidden" name="schedule[${day}][day_id]" value="${day_id}">
  `;

    tableBody.appendChild(card);

    card.querySelector(".descanso").addEventListener("click", () => {
      addNewBreakTime(card.querySelector(".descanso"), day);
    });

    card.querySelector(".form-checkbox").addEventListener("change", async (e) => {
      changeDayStatus(e, day);
    });

    if (break_start && break_end && is_enabled) {
      addBreakTimeElement(card, day, break_start, break_end);
    }

    if (card.querySelector(".copy-all")) {
      card.querySelector(".copy-all").addEventListener("click", copiarEnTodos);
    }
  }

  function addNewBreakTime(button, day) {
    const parent = button.closest(".work-day");

    // Si es mobile (card)
    if (parent.tagName === "DIV") {
      const breakSection = document.createElement("div");
      breakSection.classList.add("break-row", "space-y-2", "pt-2");

      breakSection.innerHTML = `
      <div class="text-xs text-gray-500">Hora de descanso</div>
      <div class="flex space-x-2">
        <input type='time' class='border rounded px-2 py-1 w-full' name='schedule[${day}][break_start]' required>
        <input type='time' class='border rounded px-2 py-1 w-full' name='schedule[${day}][break_end]' required>
      </div>
      <div>
        <button type='button' class='remove-break bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 rounded px-3 py-1 text-xs font-medium'>Eliminar</button>
      </div>
    `;

      button.disabled = true;
      parent.appendChild(breakSection);

      breakSection.querySelector(".remove-break").addEventListener("click", () => {
        removeBreakTime(breakSection);
      });
    } else {
      // Es escritorio (fila de tabla)
      const breakRow = document.createElement("tr");
      breakRow.classList.add("break-row", "body-table-config");

      breakRow.innerHTML = `
      <td colspan="2" class="py-2 px-2 text-xs text-gray-500">Hora de descanso</td>
      <td class='break-time py-2 px-3'>
        <div class='flex space-x-2'>
          <input type='time' class='border rounded px-2 py-1 w-full' name='schedule[${day}][break_start]' required>
          <input type='time' class='border rounded px-2 py-1 w-full' name='schedule[${day}][break_end]' required>
        </div>
      </td>
      </td>
      <td class='py-2 px-2'>
        <button type='button' class='remove-break bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 rounded px-3 py-1 text-xs font-medium'>Eliminar</button>
      </td>
    `;

      button.disabled = true;
      parent.parentNode.insertBefore(breakRow, parent.nextSibling);

      breakRow.querySelector(".remove-break").addEventListener("click", () => {
        removeBreakTime(breakRow);
      });
    }
  }

  function addBreakTimeElement(parent, day, break_start, break_end) {
    // Si es mobile (card)
    if (parent.tagName === "DIV") {
      const breakSection = document.createElement("div");
      breakSection.classList.add("break-row", "space-y-2", "pt-2");

      breakSection.innerHTML = `
      <div class="text-xs text-gray-500">Hora de descanso</div>
      <div class="flex space-x-2">
        <input type='time' class='border rounded px-2 py-1 w-full' name='schedule[${day}][break_start]' value='${break_start}' required>
        <input type='time' class='border rounded px-2 py-1 w-full' name='schedule[${day}][break_end]' value='${break_end}' required>
      </div>
      <div>
        <button type='button' class='remove-break bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 rounded px-3 py-1 text-xs font-medium'>Eliminar</button>
      </div>
    `;

      parent.querySelector(".descanso").disabled = true;
      parent.appendChild(breakSection);

      breakSection.querySelector(".remove-break").addEventListener("click", () => {
        removeBreakTime(breakSection);
      });
    } else {
      // Es escritorio (tabla)
      const breakRow = document.createElement("tr");
      breakRow.classList.add("break-row", "body-table-config");

      breakRow.innerHTML = `
      <td colspan="2" class="py-2 px-2 text-xs text-gray-500">Hora de descanso</td>
      <td class='break-time py-2 px-3'>
        <div class='flex space-x-2'>
          <input type='time' class='border rounded px-2 py-1 w-full' name='schedule[${day}][break_start]' value='${break_start}' required>
          <input type='time' class='border rounded px-2 py-1 w-full' name='schedule[${day}][break_end]' value='${break_end}' required>
        </div>
      </td>
      <td class='py-2 px-2'>
        <button type='button' class='remove-break bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 rounded px-3 py-1 text-xs font-medium'>Eliminar</button>
      </td>
    `;

      parent.querySelector(".descanso").disabled = true;
      parent.parentNode.insertBefore(breakRow, parent.nextSibling);

      breakRow.querySelector(".remove-break").addEventListener("click", () => {
        removeBreakTime(breakRow);
      });
    }
  }

  function removeBreakTime(breakElement) {
    const isMobile = breakElement.tagName === "DIV";
    let scheduleContainer;
    let scheduleId;
    let descansoBtn;

    if (isMobile) {
      // Mobile: inputs están dentro del breakElement
      scheduleContainer = breakElement.closest(".work-day");

      if (scheduleContainer) {
        descansoBtn = scheduleContainer.querySelector(".descanso");
        const idInput = scheduleContainer.querySelector("input[name*='schedule_id']");
        scheduleId = idInput ? idInput.value : null;

        // Eliminar valores de inputs antes de eliminar el bloque
        const inputs = breakElement.querySelectorAll("input[name*='break_start'], input[name*='break_end']");
        inputs.forEach((input) => input.remove());
      }
    } else {
      // Desktop: inputs están en el mismo breakRow
      const breakInputs = breakElement.querySelectorAll("input[name*='break_start'], input[name*='break_end']");
      breakInputs.forEach((input) => input.remove());

      // Obtener el tr anterior (main row)
      const tr = breakElement.previousElementSibling;
      if (tr) {
        descansoBtn = tr.querySelector(".descanso");
        const idInput = tr.querySelector("input[name*='schedule_id']");
        scheduleId = idInput ? idInput.value : null;
      }
    }

    // Eliminar visualmente el bloque
    breakElement.remove();

    // Habilitar nuevamente el botón "+ Descanso"
    if (descansoBtn) descansoBtn.disabled = false;

    // Eliminar en la base de datos solo si hay id
    if (scheduleId) {
      removeBreakTimeFromDB(scheduleId);
    }
  }

  async function removeBreakTimeFromDB(scheduleId) {
    try {
      const response = await fetch(`${baseUrl}user_admin/controllers/schedulesController.php`, {
        method: "POST",
        body: JSON.stringify({ action: "remove_break", scheduleId }),
        headers: {
          "Content-Type": "application/json",
        },
      });

      const { success, message } = await response.json();

      document.getElementById("responseMessage").innerHTML = `<p class="${success ? "" : "text-red-600"}">${message}</p>`;
      ModalManager.show("saveSchedules");
    } catch (error) {
      document.getElementById("responseMessage").innerHTML = `<p class='text-red-600'>Ocurrió un error inesperado</p>`;
      ModalManager.show("saveSchedules");
      console.error(error);
    }
  }

  function changeDayStatus(e, day) {
    const workDay = e.target.closest(".work-day");
    const descansoButton = workDay.querySelector(".descanso");

    if (e.target.checked) {
      workDay.querySelectorAll("input").forEach((input) => {
        input.removeAttribute("disabled");
        if (input.name === `schedule[${day}][is_enabled]`) {
          input.value = 1;
        }
      });
      descansoButton.removeAttribute("disabled");
    } else {
      workDay.querySelector("input[name='schedule[" + day + "][start]']").setAttribute("disabled", "");
      workDay.querySelector("input[name='schedule[" + day + "][end]']").setAttribute("disabled", "");
      descansoButton.setAttribute("disabled", "");
      workDay.querySelector("input[name='schedule[" + day + "][is_enabled]']").value = 0;

      if (workDay.nextElementSibling && workDay.nextElementSibling.classList.contains("break-row")) {
        workDay.nextElementSibling.remove();
      }
    }
  }

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    const formData = new FormData(form);
    let userId = document.getElementById("userSelect").value;

    try {
      const response = await fetch(`${baseUrl}user_admin/controllers/schedulesController.php?user_id=${userId}`, {
        method: "POST",
        body: formData,
      });

      const { success, message } = await response.json();

      if (success) {
        document.getElementById("responseMessage").innerHTML = `<p>Horario guardado exitosamente</p>`;
        ModalManager.show("saveSchedules");
        showSaveAlert(false);
        getHorarios(userId);
      } else {
        document.getElementById("responseMessage").innerHTML = `<p class='text-red-600'>${message || "Error al guardar horarios"}</p>`;
        ModalManager.show("saveSchedules");
      }
    } catch (error) {
      document.getElementById("responseMessage").innerHTML = `<p class='text-red-600'>Ocurrió un error inesperado</p>`;
      ModalManager.show("saveSchedules");
      console.error(error);
    }
  });

  async function copiarEnTodos() {
    const formData = new FormData(form);
    formData.append("copy_from_monday", true);
    let userId = document.getElementById("userSelect").value;

    try {
      const response = await fetch(`${baseUrl}user_admin/controllers/schedulesController.php?user_id=${userId}`, {
        method: "POST",
        body: formData,
      });

      const { success, message } = await response.json();

      if (success) {
        document.getElementById("responseMessage").innerHTML = `<p>${message}</p>`;
        ModalManager.show("saveSchedules");
        getHorarios(userId);
      } else {
        document.getElementById("responseMessage").innerHTML = `<p class='text-red-600'>${message || "Error al copiar los horarios"}</p>`;
        ModalManager.show("saveSchedules");
      }
    } catch (error) {
      document.getElementById("responseMessage").innerHTML = `<p class='text-red-600'>Ocurrió un error inesperado</p>`;
      ModalManager.show("saveSchedules");
      console.error(error);
    }
  }

  // --- Cierre de modal con ModalManager ---
  function setupModalCloseListeners() {
    document.querySelectorAll(".close-modal").forEach((button) => {
      button.addEventListener("click", function () {
        const modal = this.closest(".fixed.inset-0");
        if (modal) {
          ModalManager.hide(modal.id);
        }
      });
    });
    document.querySelectorAll(".fixed.inset-0").forEach((modal) => {
      modal.addEventListener("click", function (e) {
        if (e.target === modal) {
          ModalManager.hide(modal.id);
        }
      });
    });
  }

  setupModalCloseListeners();

  getHorarios();
}
