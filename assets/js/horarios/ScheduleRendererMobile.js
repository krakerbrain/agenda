export class ScheduleRendererMobile {
  static addScheduleCard(horario) {
    const { schedule_id, day_id, day, work_start, work_end, break_start, break_end, is_enabled } = horario;
    const tableBody = document.getElementById("scheduleTableBodyMobile");

    const card = document.createElement("div");
    card.classList.add("p-4", "bg-white", "rounded-lg", "shadow", "space-y-3", "work-day", "border", "border-gray-300", "mb-4");

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
          <input type="time" class="w-full border rounded px-2 py-1 disabled:opacity-50" name="schedule[${day}][start]" value="${work_start}" ${disabled}>
          <input type="time" class="w-full border rounded px-2 py-1 disabled:opacity-50" name="schedule[${day}][end]" value="${work_end}" ${disabled}>
        </div>
      </div>

      <div class="flex justify-between items-center">
        <button type="button"
                class="descanso bg-cyan-50 ${is_enabled === 1 ? "hover:bg-cyan-100" : "disabled:opacity-50"} text-cyan-700 border border-cyan-200 rounded px-3 py-1 text-xs font-medium"
                ${disabled}>
          + Descanso
        </button>
        ${day === "Lunes" ? "<button type='button' class='text-cyan-600 hover:text-cyan-800 text-xs underline copy-all'>Copiar en todos</button>" : ""}
      </div>

      <input type="hidden" name="schedule[${day}][schedule_id]" value="${schedule_id}">
      <input type="hidden" name="schedule[${day}][day_id]" value="${day_id}">
    `;

    tableBody.appendChild(card);
    return card;
  }

  static addBreakTimeElement(parent, day, break_start, break_end) {
    const breakSection = document.createElement("div");
    breakSection.classList.add("break-row", "space-y-2", "pt-2");

    breakSection.innerHTML = `
      <div class="text-xs text-gray-500">Hora de descanso</div>
      <div class="flex space-x-2">
        <input type='time' class='border rounded px-2 py-1 w-full' name='schedule[${day}][break_start]' value='${break_start}' required>
        <input type='time' class='border rounded px-2 py-1 w-full' name='schedule[${day}][break_end]' value='${break_end}' required>
      </div>
      <div class="mt-3">
        <button type='button' class='remove-break bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 rounded px-3 py-1 text-xs font-medium'>Eliminar descanso</button>
      </div>
    `;

    parent.querySelector(".descanso").classList.add("disabled:opacity-50");
    parent.querySelector(".descanso").classList.remove("hover:bg-cyan-100");

    parent.appendChild(breakSection);
    return breakSection;
  }
}
