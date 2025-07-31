export class ScheduleRendererDesktop {
  static addScheduleRow(horario) {
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
          <input type="time" class="w-full border rounded px-2 py-1 disabled:opacity-50" name="schedule[${day}][start]" value="${work_start}" ${disabled}>
          <input type="time" class="w-full border rounded px-2 py-1 disabled:opacity-50" name="schedule[${day}][end]" value="${work_end}" ${disabled}>
        </div>
      </td>
      <td class="px-3 py-2 space-x-2">
        <button type="button"
                class="descanso bg-cyan-50 ${is_enabled === 1 ? "hover:bg-cyan-100" : "disabled:opacity-50"} text-cyan-700 border border-cyan-200 rounded px-3 py-1 text-xs font-medium"
                ${disabled}>
          + Descanso
        </button>
        ${day === "Lunes" ? "<button type='button' class='text-cyan-600 hover:text-cyan-800 text-xs underline copy-all ml-2 cursor-pointer'>Copiar en todos</button>" : ""}
      </td>
      <input type="hidden" name="schedule[${day}][schedule_id]" value="${schedule_id}">
      <input type="hidden" name="schedule[${day}][day_id]" value="${day_id}">
    `;

    tableBody.appendChild(tr);
    return tr;
  }

  static addBreakTimeElement(parent, day, break_start, break_end) {
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
        <button type='button' class='remove-break bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 rounded px-3 py-1 text-xs font-medium cursor-pointer'>Eliminar descanso</button>
      </td>
    `;

    parent.querySelector(".descanso").classList.add("disabled:opacity-50");
    parent.querySelector(".descanso").classList.remove("hover:bg-cyan-100");

    parent.parentNode.insertBefore(breakRow, parent.nextSibling);
    return breakRow;
  }
}
