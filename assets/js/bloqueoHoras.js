export function initBloqueoHoras() {
  fetchBlockedDays();
  const hourRange = document.getElementById("hour-range");

  document.getElementById("all-day").addEventListener("change", function () {
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
    const user_id = e.target.form.user_id.value;

    if (!blockDate) {
      handleModal("Por favor, seleccione una fecha.");
      return;
    }

    const requestData = {
      date: blockDate,
      all_day: allDay,
      start_hour: allDay ? null : startHour,
      end_hour: allDay ? null : endHour,
      user_id: user_id,
    };

    try {
      const response = await fetch(`${baseUrl}user_admin/controllers/blockHourController.php`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(requestData),
      });

      const result = await response.json();

      if (result.success) {
        handleModal("Fecha bloqueada correctamente.");
        // Limpiar formulario
        hourRange.style.display = "none";
        document.getElementById("block-date-form").reset();
        fetchBlockedDays(user_id);
      } else {
        handleModal(result.message);
      }
    } catch (error) {
      console.error("Error:", error);
    }
  });

  async function fetchBlockedDays(user_id = null) {
    let userIdUrl = user_id !== null ? `?user_id=${user_id}` : "";
    const response = await fetch(`${baseUrl}user_admin/controllers/getDeleteBlockedHours.php${userIdUrl}`);
    const { success, data } = await response.json();

    if (success) {
      updateBlockedDatesTable(data);
    } else {
      handleModal(result.message || "Error al obtener los días bloqueados.");
    }
  }

  if (document.getElementById("userSelect")) {
    document.getElementById("userSelect").addEventListener("change", async function () {
      const userId = this.value;
      await fetchBlockedDays(userId);
    });
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
              <td class="text-center text-md-start">
                  <button class="btn btn-danger btn-sm deleteBlockedDay" data-token="${day.token}" title="Eliminar fecha bloqueada"> <i class="fas fa-trash-alt"></i></button>
              </td>
          </tr>
      `;
      tbody.innerHTML += row;
    });
    document.querySelectorAll(".deleteBlockedDay").forEach((button) => {
      button.addEventListener("click", function (event) {
        const token = this.getAttribute("data-token");
        deleteBlockedDay(token);
      });
    });
  }

  async function deleteBlockedDay(token) {
    const requestData = {
      token,
    };

    try {
      const response = await fetch(`${baseUrl}user_admin/controllers/getDeleteBlockedHours.php`, {
        method: "DELETE",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(requestData),
      });

      const result = await response.json();

      if (result.success) {
        handleModal("Fecha desbloqueada correctamente.");
        let user_id = document.querySelector("#userSelect").value;
        fetchBlockedDays(user_id);
      } else {
        handleModal(result.message);
      }
    } catch (error) {
      console.error("Error:", error);
    }
  }

  function handleModal(message) {
    const modal = new bootstrap.Modal(document.getElementById("modalErrorBlockHour"));
    const modalBody = document.getElementById("responseMessage");
    modalBody.innerHTML = message;
    modal.show();
  }

  const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
  const popoverList = [...popoverTriggerList].map((popoverTriggerEl) => new bootstrap.Popover(popoverTriggerEl));
}
