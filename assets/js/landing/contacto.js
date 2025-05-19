document.getElementById("contactForm").addEventListener("submit", async function (e) {
  e.preventDefault(); // evitar recarga

  const btnText = document.getElementById("btnText");
  const spinner = document.getElementById("spinner");
  const submitBtn = document.getElementById("submitBtn");

  // Mostrar spinner y desactivar botón
  btnText.textContent = "Enviando...";
  spinner.classList.remove("hidden");
  submitBtn.disabled = true;

  // Crear FormData desde el formulario
  const formData = new FormData(this);

  try {
    // Enviar datos al PHP
    const response = await fetch(`${baseUrl}landing/controller/procesar_contacto.php`, {
      method: "POST",
      body: formData,
    });

    // Leer respuesta (asumiendo JSON)

    const data = await response.json();

    if (data.success) {
      showAlert(data.message, "success");
      this.reset();
    } else {
      showAlert(data.message || "Ocurrió un error inesperado", "error");
    }
  } catch (error) {
    showAlert("Error en la conexión. Intenta de nuevo.", "error");
  } finally {
    // Ocultar spinner y activar botón
    btnText.textContent = "Enviar mensaje";
    spinner.classList.add("hidden");
    submitBtn.disabled = false;
  }
});

function showAlert(message, type = "success") {
  const container = document.getElementById("alert-container");
  container.innerHTML = "";

  const alertDiv = document.createElement("div");
  alertDiv.className = type === "success" ? "p-3 rounded mb-6 text-green-800 bg-green-200" : "p-3 rounded mb-6 text-red-800 bg-red-100";

  alertDiv.textContent = message;

  container.appendChild(alertDiv);

  // Opcional: que desaparezca después de 5 segundos
  setTimeout(() => {
    alertDiv.remove();
  }, 5000);
}
