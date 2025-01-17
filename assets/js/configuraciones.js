export function initConfiguraciones() {
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
        alert("Configuración guardada correctamente.");
        location.reload();
      } else {
        // Aquí manejamos los errores
        if (errors) {
          // Unimos los errores en un string separado por saltos de línea
          const errorMessage = errors.join("\n");
          alert(`Hubo un error al guardar la configuración:\n${errorMessage}`);
        } else {
          alert("Hubo un error al guardar la configuración.");
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
        // Opción: muestra una alerta o un mensaje para confirmar la copia
        alert("URL copiada al portapapeles: " + copyText.value);
      })
      .catch((error) => {
        console.error("Error:", error);
        alert("Hubo un error al copiar la URL al portapapeles.");
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
      alert(message);
      location.reload();
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
}
