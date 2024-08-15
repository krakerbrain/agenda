export function initConfiguraciones() {
  const form = document.getElementById("companyConfigForm");
  if (form) {
    form.addEventListener("submit", function (event) {
      event.preventDefault();
      const formData = new FormData(form);

      fetch(`${baseUrl}user_admin/controllers/configuraciones.php`, {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            alert("Configuración guardada correctamente.");
          } else {
            alert("Hubo un error al guardar la configuración.");
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          alert("Hubo un error al guardar la configuración.");
        });
    });
  }

  document.getElementById("addBlockedDate").addEventListener("click", function () {
    const container = document.getElementById("blockedDatesContainer");
    const newDateDiv = document.createElement("div");
    newDateDiv.className = "d-flex align-items-end mb-2";

    newDateDiv.innerHTML = `
            <input type='date' class='form-control' name='blocked_dates[]'>
            <button type='button' class='btn btn-danger btn-sm ms-2 remove-date'>Eliminar</button>
        `;

    container.appendChild(newDateDiv);

    newDateDiv.querySelector(".remove-date").addEventListener("click", function () {
      newDateDiv.remove();
    });
  });

  document.querySelectorAll(".remove-date").forEach((button) => {
    button.addEventListener("click", function () {
      button.parentElement.remove();
    });
  });

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
}
