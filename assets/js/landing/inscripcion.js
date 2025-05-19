document.querySelector("#companyForm").addEventListener("submit", async function (e) {
  e.preventDefault();

  const formData = new FormData(this);

  try {
    const response = await fetch(`${baseUrl}inscripcion/controller/procesar_inscripcion.php`, {
      method: "POST",
      body: formData,
    });

    const { success, message } = await response.json();

    if (success) {
      openModal(message || "Te hemos enviado un correo de activación.");
      // Si prefieres redirigir, comenta la línea de openModal y descomenta la siguiente:
      // window.location.href = baseUrl + "gracias";
    } else {
      openModal(message || "Hubo un error en el proceso.");
    }
  } catch (error) {
    console.error("Error:", error);
    openModal("Error inesperado. Intenta nuevamente.");
  }
});

function openModal(message) {
  const modal = document.getElementById("responseModal");
  document.getElementById("responseMessage").innerText = message || "Operación realizada";
  modal.classList.remove("hidden");
  modal.classList.add("flex");
  modal.classList.remove("opacity-0", "pointer-events-none");
  modal.classList.add("opacity-100");
}

function closeModal() {
  const modal = document.getElementById("responseModal");
  modal.classList.add("opacity-0", "pointer-events-none");
  modal.classList.remove("opacity-100");

  // Esperar el tiempo de la transición (300ms) para ocultar completamente
  setTimeout(() => {
    modal.classList.remove("flex");
    modal.classList.add("hidden");
  }, 300);
}
