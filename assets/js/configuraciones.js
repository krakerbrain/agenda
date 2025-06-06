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
        handleModal("modalReloadConfig", "Configuración guardada correctamente.");
      } else {
        // Aquí manejamos los errores
        if (errors) {
          // Unimos los errores en un string separado por saltos de línea
          const errorMessage = errors.join("\n");
          handleModal("modalErrorConfig", `Hubo un error al guardar la configuración:\n${errorMessage}`);
        } else {
          handleModal("modalErrorConfig", "Hubo un error al guardar la configuración.");
        }
      }
    });
  }

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

  document.querySelector(".copyToClipboard").addEventListener("click", function () {
    // Selecciona el input que contiene la URL
    var copyText = document.getElementById("urlToCopy");

    // Selecciona el texto dentro del input
    copyText.select();
    copyText.setSelectionRange(0, 99999); // Para dispositivos móviles

    // Copia el texto al portapapeles
    navigator.clipboard
      .writeText(copyText.value)
      .then(() => {
        // Opción: muestra una handleModala o un mensaje para confirmar la copia
        handleModal("modalErrorConfig", "URL copiada al portapapeles: " + copyText.value);
      })
      .catch((error) => {
        console.error("Error:", error);
        handleModal("modalErrorConfig", "Hubo un error al copiar la URL al portapapeles.");
      });
  });

  const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
  const popoverList = [...popoverTriggerList].map((popoverTriggerEl) => new bootstrap.Popover(popoverTriggerEl));

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

  // modal de agregar nuevo period
  document.getElementById("diasSeleccionados").textContent = document.getElementById("fixed_duration").value;
  const newPeriodModal = new bootstrap.Modal(document.getElementById("newPeriodModal"));
  document.getElementById("confirmNewPeriod").addEventListener("click", async function () {
    const diasPEriodo = document.getElementById("fixed_duration").value;

    const response = await fetch(`${baseUrl}user_admin/controllers/configuraciones_calendario.php`, {
      method: "POST",
      body: JSON.stringify({
        action: "newPeriod",
        dias: diasPEriodo,
      }),
    });

    const { success, message } = await response.json();
    if (success) {
      newPeriodModal.hide();
      handleModal("modalReloadConfig", message);
    }
  });

  document.getElementById("auto_open").addEventListener("change", function (event) {
    // Verifica si el checkbox fue marcado
    if (event.target.checked) {
      // Prevenir que el checkbox se marque inmediatamente
      event.preventDefault();
      // Mostrar el modal de confirmación
      const modal = new bootstrap.Modal(document.getElementById("autoOpenModal"));
      modal.show();
    }
  });

  // Manejar el clic en el botón de confirmar
  document.getElementById("confirmAutoOpen").addEventListener("click", function () {
    const checkbox = document.getElementById("auto_open");
    // Marca el checkbox
    checkbox.checked = true;
    // Cierra el modal
    bootstrap.Modal.getInstance(document.getElementById("autoOpenModal")).hide();
  });

  // Manejar el clic en el botón de cancelar
  document.getElementById("cancelAutoOpen").addEventListener("click", function () {
    const checkbox = document.getElementById("auto_open");
    // Desmarca el checkbox si se cancela
    checkbox.checked = false;
  });

  function handleModal(modal, message) {
    const modalSelected = new bootstrap.Modal(document.getElementById(modal));
    const modalBody = document.getElementById(modal + "responseMessage");
    modalBody.innerHTML = message;
    modalSelected.show();
  }

  document.getElementById("modalAceptarBtn").addEventListener("click", function () {
    const modal = new bootstrap.Modal(document.getElementById("modalErrorConfig"));
    location.reload();
  });

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

  // const blockUsersSwitch = document.getElementById("blockUsersSwitch");
  // const incidentsThresholdContainer = document.getElementById("incidentsThresholdContainer");

  // blockUsersSwitch.addEventListener("change", function () {
  //   if (this.checked) {
  //     incidentsThresholdContainer.style.display = "inline-block";
  //   } else {
  //     incidentsThresholdContainer.style.display = "none";
  //   }
  // });

  const blockUsersSwitch = document.getElementById("blockUsersSwitch");
  const incidentsThresholdContainer = document.getElementById("incidentsThresholdContainer");

  blockUsersSwitch.addEventListener("change", function () {
    // Si el checkbox está marcado, mostramos el input
    if (blockUsersSwitch.checked) {
      // Verifica si el input ya existe, si no, lo creamos
      if (!document.getElementById("blockAfterIncidents")) {
        const inputHTML = `
        <input type="number" class="form-control form-control-sm d-inline-block" 
               style="width: 60px;" id="blockAfterIncidents" name="incidents_threshold" 
               min="1" value="2">
        <label for="blockAfterIncidents" class="ms-1">Incidencias</label>
      `;
        incidentsThresholdContainer.innerHTML = inputHTML;
      }
    } else {
      // Si el checkbox no está marcado, eliminamos el input
      incidentsThresholdContainer.innerHTML = "";
    }
  });
}
