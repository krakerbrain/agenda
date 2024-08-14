export function initCorreos() {
  const formReserva = document.getElementById("reservaForm");
  const formConfirmacion = document.getElementById("confirmacionForm");

  async function getEmailTemplates() {
    const response = await fetch(`${baseUrl}user_admin/controllers/correosController.php`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        action: "getTemplates",
      }),
    });

    const { success, data } = await response.json();

    if (success && data.length > 0) {
      formReserva.reset();
      formConfirmacion.reset();
      document.getElementById("reservaNotas").innerHTML = "";
      document.getElementById("confirmacionNotas").innerHTML = "";

      data.forEach((template) => {
        if (template.template_name === "Reserva") {
          cargarNotas(JSON.parse(template.notas), "reserva");
        } else if (template.template_name === "Confirmación") {
          cargarNotas(JSON.parse(template.notas), "confirmacion");
        }
      });
    }
  }

  function cargarNotas(notas, tipo) {
    notas.forEach((nota, index) => {
      const notaIndex = index + 1;
      const notaDiv = document.createElement("div");
      notaDiv.classList.add("mb-3");
      notaDiv.innerHTML = `
      <div class="d-flex justify-content-between mb-1"> 
        <label for="${tipo}Nota${notaIndex}" class="form-label">Nota ${notaIndex}:</label>
        <button type="button" class="btn btn-danger btn-sm eliminarNota">
          <i class="fas fa-x"></i>
        </button>
      </div>
      <textarea class="form-control" id="${tipo}Nota${notaIndex}" name="notas[]" rows="3">${nota}</textarea>
      `;
      document.getElementById(`${tipo}Notas`).appendChild(notaDiv);

      // Añadir evento para eliminar la nota
      notaDiv.querySelector(".eliminarNota").addEventListener("click", function () {
        notaDiv.remove();
        enviarFormulario(tipo);
      });
    });
  }

  function enviarFormulario(tipo) {
    const form = tipo === "reserva" ? formReserva : formConfirmacion;

    const notas = Array.from(form.querySelectorAll('textarea[name="notas[]"]')).map((nota) => nota.value);

    fetch(`${baseUrl}user_admin/controllers/correosController.php`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        template_name: tipo === "reserva" ? "Reserva" : "Confirmación",
        notas,
        action: "saveTemplate",
      }),
    })
      .then((response) => response.json())
      .then(({ success }) => {
        if (success) {
          alert(`Notas del correo de ${tipo} guardadas exitosamente`);
        } else {
          alert(`Error al guardar las notas del correo de ${tipo}`);
        }
      })
      .catch((error) => {
        alert(`Error al realizar la solicitud: ${error}`);
      });
  }

  getEmailTemplates();

  formReserva.addEventListener("submit", async (event) => {
    event.preventDefault();
    enviarFormulario("reserva");
  });

  formConfirmacion.addEventListener("submit", async (event) => {
    event.preventDefault();
    enviarFormulario("confirmacion");
  });

  document.getElementById("agregarNota").addEventListener("click", function () {
    agregarNota("reserva");
  });

  document.getElementById("agregarNotaConfirmacion").addEventListener("click", function () {
    agregarNota("confirmacion");
  });

  function agregarNota(tipo) {
    const notaIndex = document.querySelectorAll(`#${tipo}Notas .form-label`).length + 1;
    const notaDiv = document.createElement("div");
    notaDiv.classList.add("mb-3");
    notaDiv.innerHTML = `
      <div class="d-flex justify-content-between mb-1"> 
        <label for="${tipo}Nota${notaIndex}" class="form-label">Nota ${notaIndex}:</label>
        <button type="button" class="btn btn-danger btn-sm eliminarNota">
          <i class="fas fa-x"></i>
        </button>
      </div>
      <textarea class="form-control" id="${tipo}Nota${notaIndex}" name="notas[]" rows="3" placeholder="Escribe la nota ${notaIndex}"></textarea>
    `;
    document.getElementById(`${tipo}Notas`).appendChild(notaDiv);

    // Añadir evento para eliminar la nota
    notaDiv.querySelector(".eliminarNota").addEventListener("click", function () {
      notaDiv.remove();
      enviarFormulario(tipo);
    });
  }
}
