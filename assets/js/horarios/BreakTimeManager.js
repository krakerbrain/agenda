import { ScheduleRendererDesktop } from "./ScheduleRendererDesktop.js";
import { ScheduleRendererMobile } from "./ScheduleRendererMobile.js";
export class BreakTimeManager {
  static baseUrl = "";

  static setBaseUrl(url) {
    this.baseUrl = url;
  }
  static addNewBreakTime(button, day) {
    const parent = button.closest(".work-day");
    let breakElement;

    if (parent.tagName === "DIV") {
      // Mobile
      breakElement = ScheduleRendererMobile.addBreakTimeElement(parent, day, "", "");
    } else {
      // Desktop
      breakElement = ScheduleRendererDesktop.addBreakTimeElement(parent, day, "", "");
    }

    button.disabled = true;

    breakElement.querySelector(".remove-break").addEventListener("click", () => {
      BreakTimeManager.removeBreakTime(breakElement);
    });
  }

  static async removeBreakTime(breakElement) {
    const isMobile = breakElement.tagName === "DIV";
    let scheduleContainer;
    let scheduleId;
    let descansoBtn;

    if (isMobile) {
      scheduleContainer = breakElement.closest(".work-day");

      if (scheduleContainer) {
        descansoBtn = scheduleContainer.querySelector(".descanso");
        const idInput = scheduleContainer.querySelector("input[name*='schedule_id']");
        scheduleId = idInput ? idInput.value : null;

        // Opcional: eliminar inputs de break antes o después de la BD
        const inputs = breakElement.querySelectorAll("input[name*='break_start'], input[name*='break_end']");
        inputs.forEach((input) => input.remove());
      }
    } else {
      const breakInputs = breakElement.querySelectorAll("input[name*='break_start'], input[name*='break_end']");
      breakInputs.forEach((input) => input.remove());

      const tr = breakElement.previousElementSibling;
      if (tr) {
        descansoBtn = tr.querySelector(".descanso");
        const idInput = tr.querySelector("input[name*='schedule_id']");
        scheduleId = idInput ? idInput.value : null;
      }
    }

    if (scheduleId) {
      // Esperar que la BD confirme eliminación
      const result = await this.removeBreakTimeFromDB(scheduleId);

      if (result.success) {
        // Sólo si eliminó correctamente en BD, eliminar visualmente y habilitar botón
        breakElement.remove();
        if (descansoBtn) descansoBtn.disabled = false;
        // Mostrar mensaje o feedback si quieres, usando ModalManager o similar
      } else {
        // Mostrar error, no eliminar visualmente
        ModalManager.show("saveSchedules");
        document.getElementById("responseMessage").innerHTML = `<p class="text-red-600">${result.message || "Error al eliminar descanso"}</p>`;
      }
    } else {
      // No hay ID, quizá solo eliminar visualmente sin afectar BD (depende del flujo)
      breakElement.remove();
      if (descansoBtn) descansoBtn.disabled = false;
    }
  }

  static async removeBreakTimeFromDB(scheduleId) {
    try {
      const response = await fetch(`${this.baseUrl}user_admin/controllers/schedulesController.php`, {
        method: "POST",
        body: JSON.stringify({ action: "remove_break", scheduleId }),
        headers: {
          "Content-Type": "application/json",
        },
      });

      const data = await response.json();
      return data; // { success, message }
    } catch (error) {
      console.error("Error eliminando break en BD:", error);
      return { success: false, message: "Error de red o inesperado" };
    }
  }
}
