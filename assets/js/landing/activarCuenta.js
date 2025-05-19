document.querySelector("form").addEventListener("submit", async function (e) {
  e.preventDefault();

  const formData = new FormData(this);
  const modal = document.getElementById("activationModal");
  const title = document.getElementById("modalTitle");
  const message = document.getElementById("modalMessage");
  const loginBtn = document.getElementById("goToLoginBtn");

  try {
    const response = await fetch(`${baseUrl}landing/controller/procesar_activacion.php`, {
      method: "POST",
      body: formData,
    });

    const text = await response.text();

    // Configurar el contenido del modal
    if (text.includes("✅")) {
      title.innerText = "Cuenta activada";
      message.innerText = "Tu contraseña ha sido creada exitosamente.";
      loginBtn.classList.remove("hidden");
      loginBtn.onclick = () => (window.location.href = `${baseUrl}login/index.php`);
    } else {
      title.innerText = "Error";
      message.innerText = text;
      loginBtn.classList.add("hidden");
    }

    modal.classList.remove("hidden");
    modal.classList.add("flex");
  } catch (error) {
    title.innerText = "Error";
    message.innerText = "Ocurrió un error al procesar la activación.";
    modal.classList.remove("hidden");
    modal.classList.add("flex");
  }
});

function closeActivationModal() {
  const modal = document.getElementById("activationModal");
  modal.classList.add("hidden");
}
