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
    const { schedule_id, day_id, day, work_start, work_end, break_start, break_end, is_enabled } = horario;

    const tableBody = document.getElementById("scheduleTableBody");
    const tr = document.createElement("tr");
    tr.classList.add("work-day");
    const copiaTodo = day === "Lunes" ? "<button type='button' class='btn btn-link copy-all'>Copiar en todos</button>" : "";
    const checked = is_enabled === 1 ? "checked" : "";
    const disabled = is_enabled === 1 ? "" : "disabled";

    tr.innerHTML = `
      <tr class='work-day'>
      <input type='hidden' name='schedule[${day}][schedule_id]' value='${schedule_id}'>
        <td data-cell="día" class="data">${day}
          <input type='hidden' name='schedule[${day}][day_id]' value='${day_id}'>
        </td>
        <td data-cell="estado" class="data">
          <div class='form-check form-switch'>
            <input class='form-check-input' type='checkbox' ${checked}>
            <input type='hidden' name='schedule[${day}][is_enabled]' value='${is_enabled}'>
          </div>
        </td>
        <td data-cell="Inicio Jornada" class="data">
          <input type='time' class='form-control' name='schedule[${day}][start]' value='${work_start}' ${disabled}>
        </td>
        <td data-cell="Fin Jornada" class="data">
          <input type='time' class='form-control' name='schedule[${day}][end]' value='${work_end}'${disabled}>
        </td>
        <td>
          <button type='button' name='schedule[${day}]' class='btn btn-outline-primary btn-sm descanso' ${disabled}>+ Descanso</button>
        </td>
        <td data-cell="">
          ${copiaTodo}
        </td>
      </tr>
    `;

    tableBody.appendChild(tr);

    tr.querySelector(".descanso").addEventListener("click", () => {
      addNewBreakTime(tr.querySelector(".descanso"), day);
    });

    tr.querySelector(".form-check-input").addEventListener("change", async (e) => {
      changeDayStatus(e, day);
    });

    // Si hay un horario de descanso, agregar la fila del descanso
    if (break_start && break_end && is_enabled) {
      addBreakTimeElement(tr, day, break_start, break_end);
    }
    document.querySelector(".copy-all").addEventListener("click", copiarEnTodos);
  }
  function addNewBreakTime(button, day) {
    const tr = button.closest(".work-day");
    const breakRow = document.createElement("tr");
    breakRow.classList.add("break-row");

    breakRow.innerHTML = `
      <td colspan="2">Hora de descanso</td>
      <td class='break-time'>
        <input type='time' class='form-control' name='schedule[${day}][break_start]' value='' required>
      </td>
      <td>
        <input type='time' class='form-control' name='schedule[${day}][break_end]' value='' required>
      </td>
      <td>
        <button type='button' class='btn btn-outline-danger btn-sm remove-break'>Eliminar</button>
      </td>
      <td></td>
    `;

    button.disabled = true;
    tr.parentNode.insertBefore(breakRow, tr.nextSibling);

    breakRow.querySelector(".remove-break").addEventListener("click", () => {
      removeBreakTime(breakRow);
    });
  }

  function addBreakTimeElement(tr, day, break_start, break_end) {
    const breakRow = document.createElement("tr");
    breakRow.classList.add("break-row");

    breakRow.innerHTML = `
      <td colspan="2">Hora de descanso</td>
      <td data-cell="inicio descanso" class='break-time data'>
        <input type='time' class='form-control' name='schedule[${day}][break_start]' value='${break_start}' required>
      </td>
      <td data-cell="fin descanso" class="data">
        <input type='time' class='form-control' name='schedule[${day}][break_end]' value='${break_end}' required>
      </td>
      <td>
        <button type='button' class='btn btn-outline-danger btn-sm remove-break'>Eliminar</button>
      </td>
      <td></td>
    `;

    tr.parentNode.insertBefore(breakRow, tr.nextSibling);

    breakRow.querySelector(".remove-break").addEventListener("click", () => {
      removeBreakTime(breakRow);
    });

    tr.querySelector(".descanso").disabled = true;
  }

  function removeBreakTime(breakRow) {
    const tr = breakRow.previousElementSibling;
    const scheduleId = tr.querySelector("input[name*='schedule_id']").value;
    breakRow.remove();
    tr.querySelector(".descanso").disabled = false;

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

        if (success) {
          alert(message);
          getHorarios();
        }
      } catch (error) {
        console.error(error);
      }
    }

    removeBreakTimeFromDB(scheduleId);
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
    try {
      const response = await fetch(`${baseUrl}user_admin/controllers/schedulesController.php`, {
        method: "POST",
        body: formData,
      });

      const { success, message } = await response.json();

      if (success) {
        alert(message);
        getHorarios();
      }
    } catch (error) {
      console.error(error);
    }
  });

  async function copiarEnTodos() {
    const formData = new FormData(form);
    formData.append("copy_from_monday", true);
    try {
      const response = await fetch(`${baseUrl}user_admin/controllers/schedulesController.php`, {
        method: "POST",
        body: formData,
      });

      const { success, message } = await response.json();

      if (success) {
        alert(message);
        getHorarios();
      } else {
        alert("Error al copiar los horarios: " + message);
      }
    } catch (error) {
      console.error(error);
    }
  }

  getHorarios();
}
