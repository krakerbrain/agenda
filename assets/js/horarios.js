import { ModalManager } from "./config/ModalManager.js";
import { ScheduleRendererDesktop } from "./horarios/ScheduleRendererDesktop.js";
import { ScheduleRendererMobile } from "./horarios/ScheduleRendererMobile.js";
import { BreakTimeManager } from "./horarios/BreakTimeManager.js";

export function init() {
  const form = document.getElementById("workScheduleForm");
  let initialSchedules = [];

  function addScheduleToTable(horario) {
    const desktopRow = ScheduleRendererDesktop.addScheduleRow(horario);
    const mobileCard = ScheduleRendererMobile.addScheduleCard(horario);

    setupScheduleEventListeners(desktopRow, mobileCard, horario.day);

    if (horario.break_start && horario.break_end && horario.is_enabled) {
      addExistingBreakTime(desktopRow, mobileCard, horario.day, horario.break_start, horario.break_end);
    }
  }

  function setupScheduleEventListeners(desktopRow, mobileCard, day) {
    BreakTimeManager.setBaseUrl(baseUrl);
    // Eventos para desktop
    desktopRow.querySelector(".descanso").addEventListener("click", () => {
      BreakTimeManager.addNewBreakTime(desktopRow.querySelector(".descanso"), day);
    });

    desktopRow.querySelector(".form-checkbox").addEventListener("change", async (e) => {
      changeDayStatus(e, day);
    });

    // Eventos para mobile
    mobileCard.querySelector(".descanso").addEventListener("click", () => {
      BreakTimeManager.addNewBreakTime(mobileCard.querySelector(".descanso"), day);
    });

    mobileCard.querySelector(".form-checkbox").addEventListener("change", async (e) => {
      changeDayStatus(e, day);
    });

    // Botón copiar en todos
    const copyAllDesktop = desktopRow.querySelector(".copy-all");
    const copyAllMobile = mobileCard.querySelector(".copy-all");

    if (copyAllDesktop) copyAllDesktop.addEventListener("click", copiarEnTodos);
    if (copyAllMobile) copyAllMobile.addEventListener("click", copiarEnTodos);
  }

  function addExistingBreakTime(desktopRow, mobileCard, day, break_start, break_end) {
    const desktopBreak = ScheduleRendererDesktop.addBreakTimeElement(desktopRow, day, break_start, break_end);
    const mobileBreak = ScheduleRendererMobile.addBreakTimeElement(mobileCard, day, break_start, break_end);

    desktopBreak.querySelector(".remove-break").addEventListener("click", () => {
      BreakTimeManager.removeBreakTime(desktopBreak);
    });

    mobileBreak.querySelector(".remove-break").addEventListener("click", () => {
      BreakTimeManager.removeBreakTime(mobileBreak);
    });

    desktopRow.querySelector(".descanso").disabled = true;
    mobileCard.querySelector(".descanso").disabled = true;
  }

  function limpiarContenedoresHorario() {
    document.getElementById("scheduleTableBodyDesktop").innerHTML = "";
    document.getElementById("scheduleTableBodyMobile").innerHTML = "";
  }

  // evento chaange para userSelect
  if (document.getElementById("userSelect")) {
    document.getElementById("userSelect").addEventListener("change", async function () {
      const userId = this.value;
      await getHorarios(userId);
    });
  }
  async function getHorarios(user_id = null) {
    let userIdUrl = user_id !== null ? `?user_id=${user_id}` : "";

    const response = await fetch(`${baseUrl}user_admin/controllers/schedulesController.php${userIdUrl}`, {
      method: "GET",
    });
    const { success, data } = await response.json();

    if (success) {
      initialSchedules = JSON.parse(JSON.stringify(data));
      limpiarContenedoresHorario();

      if (data.length > 0) {
        data.forEach((horario) => {
          addScheduleToTable(horario);
        });
      }
    }
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
      descansoButton.classList.remove("disabled:opacity-50");
      descansoButton.classList.add("hover:bg-cyan-100");
    } else {
      workDay.querySelector("input[name='schedule[" + day + "][start]']").setAttribute("disabled", "");
      workDay.querySelector("input[name='schedule[" + day + "][end]']").setAttribute("disabled", "");
      descansoButton.setAttribute("disabled", "");
      descansoButton.classList.add("disabled:opacity-50");
      descansoButton.classList.remove("hover:bg-cyan-100");
      workDay.querySelector("input[name='schedule[" + day + "][is_enabled]']").value = 0;

      if (workDay.nextElementSibling && workDay.nextElementSibling.classList.contains("break-row")) {
        workDay.nextElementSibling.remove();
      }
    }
  }

  function isMobileView() {
    return window.innerWidth < 768; // o el breakpoint que uses
  }
  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    // Eliminar inputs del formato no visible
    if (isMobileView()) {
      // Estás en mobile: borra la tabla desktop
      document.getElementById("scheduleTableBodyDesktop").innerHTML = "";
    } else {
      // Estás en desktop: borra los cards mobile
      document.getElementById("scheduleTableBodyMobile").innerHTML = "";
    }
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
