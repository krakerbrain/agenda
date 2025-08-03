import { ModalManager } from "./config/ModalManager.js";

export function init() {
  const form = document.getElementById("companyConfigForm");
  if (form) {
    form.addEventListener("submit", async function (event) {
      event.preventDefault();
      const formData = new FormData(form);

      const response = await fetch(`${baseUrl}user_admin/controllers/configuraciones_visual.php`, {
        method: "POST",
        body: formData,
      });

      const { success, errors } = await response.json();
      if (success) {
        // Usamos el modal genérico para mensajes de éxito
        ModalManager.show("infoModal", {
          title: "Éxito",
          message: "Configuración guardada correctamente.",
        });
      } else {
        if (errors) {
          const errorMessage = errors.join("\n");
          ModalManager.show("infoModal", {
            title: "Error",
            message: `Hubo un error al guardar la configuración:\n${errorMessage}`,
          });
        } else {
          ModalManager.show("infoModal", {
            title: "Error",
            message: "Hubo un error al guardar la configuración.",
          });
        }
      }
    });
  }

  // Resto de event listeners para colores (sin cambios)
  document.getElementById("background-color").addEventListener("input", function () {
    document.getElementById("example-card").style.backgroundColor = this.value;
  });

  document.getElementById("font-color").addEventListener("input", function () {
    document.getElementById("card-title").style.color = this.value;
    document.getElementById("card-text").style.color = this.value;
    document.getElementById("btn-primary-example").style.color = this.value;
    document.getElementById("btn-secondary-example").style.color = this.value;
  });

  document.getElementById("btn-primary-color").addEventListener("input", function () {
    document.getElementById("btn-primary-example").style.backgroundColor = this.value;
  });

  document.getElementById("btn-secondary-color").addEventListener("input", function () {
    document.getElementById("btn-secondary-example").style.backgroundColor = this.value;
  });

  // Copiar URL al portapapeles
  document.querySelector(".copyToClipboard").addEventListener("click", function () {
    const copyText = document.getElementById("urlToCopy");
    copyText.select();
    copyText.setSelectionRange(0, 99999);

    navigator.clipboard
      .writeText(copyText.value)
      .then(() => {
        ModalManager.show("infoModal", {
          title: "URL copiada",
          message: "URL copiada al portapapeles: " + copyText.value,
        });
      })
      .catch((error) => {
        console.error("Error:", error);
        ModalManager.show("infoModal", {
          title: "Error",
          message: "Hubo un error al copiar la URL al portapapeles.",
        });
      });
  });

  // Resetear colores (sin cambios)
  document.getElementById("resetColors").addEventListener("click", function () {
    document.getElementById("background-color").value = "#bebdff";
    document.getElementById("font-color").value = "#525252";
    document.getElementById("btn-primary-color").value = "#ffffff";
    document.getElementById("btn-secondary-color").value = "#9b80ff";

    document.getElementById("example-card").style.backgroundColor = "#bebdff";
    document.getElementById("card-title").style.color = "#525252";
    document.getElementById("card-text").style.color = "#525252";
    document.getElementById("btn-primary-example").style.backgroundColor = "#ffffff";
    document.getElementById("btn-primary-example").style.color = "#525252";
    document.getElementById("btn-secondary-example").style.backgroundColor = "#9b80ff";
    document.getElementById("btn-secondary-example").style.color = "#525252";
  });

  // Cambio entre modos de calendario (sin cambios)
  const radios = document.querySelectorAll('input[name="calendar_mode"]');
  radios.forEach((radio) => {
    radio.addEventListener("change", function () {
      if (this.value === "corrido") {
        document.getElementById("corridoInput").style.display = "block";
        document.getElementById("fijoInput").style.display = "none";
      } else {
        document.getElementById("corridoInput").style.display = "none";
        document.getElementById("fijoInput").style.display = "block";
      }
    });
  });

  // Configurar nuevo periodo
  document.getElementById("diasSeleccionados").textContent = document.getElementById("fixed_duration").value;

  document.getElementById("openNewPeriod").addEventListener("click", function () {
    // Abrir modal para configurar nuevo periodo
    ModalManager.show("newPeriodModal");
  });

  document.getElementById("confirmNewPeriod").addEventListener("click", async function () {
    const diasPeriodo = document.getElementById("fixed_duration").value;

    const response = await fetch(`${baseUrl}user_admin/controllers/configuraciones_calendario.php`, {
      method: "POST",
      body: JSON.stringify({
        action: "newPeriod",
        dias: diasPeriodo,
      }),
    });

    const { success, message } = await response.json();
    if (success) {
      ModalManager.hide("newPeriodModal");
      // Usamos el modal genérico para mostrar el mensaje de éxito
      ModalManager.show("infoModal", {
        title: "Éxito",
        message: message,
      });
      setTimeout(() => {
        location.reload(); // Recargar la página para reflejar los cambios
      }, 2000);
    }
  });

  // Manejo de apertura automática
  document.getElementById("auto_open").addEventListener("change", function (event) {
    if (event.target.checked) {
      event.preventDefault();
      // Mostramos el modal específico para confirmación de apertura automática
      ModalManager.show("autoOpenModal");
    }
  });

  document.getElementById("confirmAutoOpen").addEventListener("click", function () {
    const checkbox = document.getElementById("auto_open");
    checkbox.checked = true;
    ModalManager.hide("autoOpenModal");
  });

  document.getElementById("cancelAutoOpen").addEventListener("click", function () {
    const checkbox = document.getElementById("auto_open");
    checkbox.checked = false;
    ModalManager.hide("autoOpenModal");
  });

  // Recargar página al aceptar (sin cambios)
  // document.getElementById("modalAceptarBtn").addEventListener("click", function () {
  //   location.reload();
  // });

  // Toggle entre opciones de intervalos (sin cambios)
  const fixedIntervalsRadio = document.getElementById("fixedIntervals");
  const serviceDurationRadio = document.getElementById("serviceDuration");
  const fixedIntervalsOptions = document.getElementById("fixedIntervalsOptions");
  const serviceDurationNote = document.getElementById("serviceDurationNote");

  function toggleOptions() {
    if (serviceDurationRadio.checked) {
      fixedIntervalsOptions.style.display = "none";
      serviceDurationNote.style.display = "block";
    } else {
      fixedIntervalsOptions.style.display = "block";
      serviceDurationNote.style.display = "none";
    }
  }

  fixedIntervalsRadio.addEventListener("change", toggleOptions);
  serviceDurationRadio.addEventListener("change", toggleOptions);

  // Bloqueo por incidencias (sin cambios)
  const blockUsersSwitch = document.getElementById("blockUsersSwitch");
  const incidentsThresholdContainer = document.getElementById("incidentsThresholdContainer");

  blockUsersSwitch.addEventListener("change", function () {
    if (blockUsersSwitch.checked) {
      if (!document.getElementById("blockAfterIncidents")) {
        const inputHTML = `
        <input type="number" class="w-16 px-2 py-1 border border-gray-300 rounded-md inline-block" 
               id="blockAfterIncidents" name="incidents_threshold" 
               min="1" value="2">
        <label for="blockAfterIncidents" class="ml-1 text-gray-700">Incidencias</label>
      `;
        incidentsThresholdContainer.innerHTML = inputHTML;
      }
    } else {
      incidentsThresholdContainer.innerHTML = "";
    }
  });

  // Configurar listeners para cerrar los modales
  ModalManager.setupCloseListeners();
}
