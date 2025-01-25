export function initHorarios() {
  const form = document.getElementById("workScheduleForm");
  const tableBody = document.getElementById("scheduleTableBody");

  let initialSchedules = [];

  async function getHorarios() {
    const response = await fetch(`${baseUrl}user_admin/controllers/schedulesController.php`, {
      method: "GET",
    });
    const { success, data } = await response.json();

    if (success) {
      // Guardar una copia de los datos iniciales
      initialSchedules = JSON.parse(JSON.stringify(data)); // Copia profunda para evitar referencias
      // Limpiar el cuerpo de la tabla
      tableBody.innerHTML = "";

      if (data.length > 0) {
        data.forEach((horario) => {
          addScheduleToTable(horario);
        });
      }
    }
  }

  function getCurrentSchedules() {
    const rows = Array.from(tableBody.querySelectorAll("tr.work-day")); // Filas de la tabla
    return rows.map((row) => {
      const day = row.querySelector("td[data-cell='día']").textContent.trim();
      const schedule_id = row.querySelector("input[name^='schedule[" + day + "][schedule_id]']").value;
      const day_id = row.querySelector("input[name^='schedule[" + day + "][day_id]']").value;
      const is_enabled = row.querySelector("input[name^='schedule[" + day + "][is_enabled]']").checked ? 1 : 0;
      const work_start = row.querySelector("input[name^='schedule[" + day + "][start]']").value;
      const work_end = row.querySelector("input[name^='schedule[" + day + "][end]']").value;

      const break_start = row.querySelector("input[name^='schedule[" + day + "][break_start]']") ? row.querySelector("input[name^='schedule[" + day + "][break_start]']").value : null;
      const break_end = row.querySelector("input[name^='schedule[" + day + "][break_end]']") ? row.querySelector("input[name^='schedule[" + day + "][break_end]']").value : null;

      return {
        schedule_id,
        day_id,
        day,
        is_enabled,
        work_start,
        work_end,
        break_start,
        break_end,
      };
    });
  }

  const beforeUnloadHandler = (event) => {
    const currentSchedules = getCurrentSchedules();
    if (hasChanges(currentSchedules)) {
      event.preventDefault();
    }
  };

  form.addEventListener("input", () => {
    const currentSchedules = getCurrentSchedules();
    showSaveAlert(hasChanges(currentSchedules));
  });

  function showSaveAlert(haveChanges) {
    if (haveChanges) {
      document.getElementById("unsavedChangesAlert").classList.remove("d-none");
    } else {
      document.getElementById("unsavedChangesAlert").classList.add("d-none");
    }
  }

  // Registrar el evento solo para esta página
  window.addEventListener("beforeunload", beforeUnloadHandler);

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
    const { schedule_id, day_id, day, work_start, work_end, break_start, break_end, is_enabled } = horario;

    const tableBody = document.getElementById("scheduleTableBody");
    const tr = document.createElement("tr");
    tr.classList.add("work-day");
    tr.classList.add("body-table");
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
    breakRow.classList.add("body-table");

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
    breakRow.classList.add("body-table");

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
        const modal = new bootstrap.Modal(document.getElementById("saveSchedules"));
        modal.show();
        showSaveAlert(false);
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
  // Desregistrar el evento al salir de la página
  return () => {
    window.removeEventListener("beforeunload", beforeUnloadHandler);
  };
}
