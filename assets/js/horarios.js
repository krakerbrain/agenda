export function initHorarios() {
  const form = document.getElementById("workScheduleForm");
  const tableBody = document.getElementById("scheduleTableBody");

  async function getHorarios() {
    const response = await fetch(`${baseUrl}user_admin/controllers/schedulesController.php`, {
      method: "GET",
    });
    const { success, data } = await response.json();

    if (success) {
      // Limpiar el cuerpo de la tabla
      tableBody.innerHTML = "";

      if (data.length > 0) {
        data.forEach((horario) => {
          addScheduleToTable(horario);
        });
      }
    }
  }

  function addScheduleToTable(horario) {
    const { schedule_id, day, work_start, work_end, break_start, break_end, is_enabled } = horario;

    const tableBody = document.getElementById("scheduleTableBody");
    const tr = document.createElement("tr");
    const copiaTodo = day === "Lunes" ? "<button type='button' class='btn btn-link copy-all'>Copiar en todos</button>" : "";
    const checked = is_enabled === 1 ? "checked" : "";
    const disabled = is_enabled === 1 ? "" : "disabled";

    tr.innerHTML = `
      <tr class='work-day'>
        <td>${day}</td>
        <td>
          <div class='form-check form-switch'>
            <input class='form-check-input' type='checkbox' name='schedule[${day}]'  ${checked}>
          </div>
        </td>
        <td>
          <input type='time' class='form-control' name='schedule[${day}][start]' value='${work_start}' ${disabled}>
        </td>
        <td>
          <input type='time' class='form-control' name='schedule[${day}][end]' value='${work_end}'${disabled}>
        </td>
        <td>
          <button type='button' name='schedule[${day}]' class='btn btn-outline-primary btn-sm descanso' ${disabled}>+ Descanso</button>
        </td>
        <td>
          ${copiaTodo}
        </td>
      </tr>
    `;
    debugger;
    tableBody.appendChild(tr);

    tr.querySelector(".descanso").addEventListener("click", () => {
      addBreakTime(tr.querySelector(".descanso"), day);
    });
  }

  getHorarios();

  function addBreakTime(button, day) {
    const tr = button.closest(".work-day");
    const breakRow = document.createElement("tr");
    const breakCell = document.createElement("td");
    breakCell.setAttribute("colspan", "6");
    breakCell.innerHTML = `
      <div class='break-time'>
        <input type='time' class='form-control' name='schedule[${day}][break_start]' value='' required>
        <input type='time' class='form-control' name='schedule[${day}][break_end]' value='' required>
      </div>
    `;
    breakRow.appendChild(breakCell);
    debugger;
    tr.insertAdjacentElement("afterend", breakRow);
  }
}
