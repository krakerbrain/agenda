export function initBloqueoHoras() {
  fetchBlockedDays();

  document.getElementById("all-day").addEventListener("change", function () {
    const hourRange = document.getElementById("hour-range");
    if (this.checked) {
      hourRange.style.display = "none";
      document.getElementById("start-hour").required = false;
      document.getElementById("end-hour").required = false;
    } else {
      hourRange.style.display = "flex";
      document.getElementById("start-hour").required = true;
      document.getElementById("end-hour").required = true;
    }
  });

  document.querySelector("#block-date-form button").addEventListener("click", async function (e) {
    e.preventDefault();

    const blockDate = document.getElementById("block-date").value;
    const allDay = document.getElementById("all-day").checked;
    const startHour = document.getElementById("start-hour").value;
    const endHour = document.getElementById("end-hour").value;

    if (!blockDate) {
      alert("Por favor, selecciona una fecha.");
      return;
    }

    const requestData = {
      date: blockDate,
      all_day: allDay,
      start_hour: allDay ? null : startHour,
      end_hour: allDay ? null : endHour,
    };

    try {
      const response = await fetch(`${baseUrl}user_admin/controllers/blockHourController.php`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(requestData),
      });

      const result = await response.json();

      if (result.success) {
        alert("Fecha bloqueada correctamente.");
        fetchBlockedDays();
      } else {
        alert(result.message);
      }
    } catch (error) {
      console.error("Error:", error);
    }
  });

  async function fetchBlockedDays() {
    const response = await fetch(`${baseUrl}user_admin/controllers/getBlockedHours.php`);
    const result = await response.json();

    if (result.success) {
      updateBlockedDatesTable(result.data);
    } else {
      alert(result.message || "Error al obtener los días bloqueados.");
    }
  }

  function updateBlockedDatesTable(blockedDays) {
    const tbody = document.getElementById("blocked-dates-list");
    tbody.innerHTML = ""; // Limpiar la tabla

    if (blockedDays.length === 0) {
      tbody.innerHTML = `
          <tr>
              <td colspan="4" class="text-center">No hay fechas bloqueadas.</td>
          </tr>
      `;
      return;
    }

    blockedDays.forEach((day) => {
      const row = `
          <tr>
              <td>${day.date}</td>
              <td>${day.start_time || "Todo el día"}</td>
              <td>${day.end_time || "Todo el día"}</td>
              <td>
                  <button class="btn btn-danger btn-sm" onclick="removeBlockedDate('${day.date}')">Eliminar</button>
              </td>
          </tr>
      `;
      tbody.innerHTML += row;
    });
  }
}
