export function initAddUser() {
  document.getElementById("addUserForm").addEventListener("submit", async function (event) {
    event.preventDefault();
    // Mostrar spinner y deshabilitar botón
    displaySpinner("addUser", true);

    const formData = new FormData(this);
    try {
      const response = await fetch(`${baseUrl}login/registra_usuario.php`, {
        method: "POST",
        body: formData,
      });
      const { success, error } = await response.json();

      if (success) {
        //limpiar formulario
        this.reset();
        alert("Usuario agregado exitosamente");
      } else {
        document.querySelector("#addUserForm .error").innerHTML = `<span>${error}</span>`;
        document.querySelector("#addUserForm .error").classList.remove("d-none");

        this.reset();
      }
    } catch (error) {
      console.error("Error:", error);
    } finally {
      removeError();
      // Ocultar spinner y habilitar botón
      displaySpinner("addUser", false);
      document.getElementById("roleAbout").classList.add("d-none");
    }
  });
  function removeError() {
    setTimeout(() => {
      document.querySelector("#addUserForm .error").classList.add("d-none");
    }, 5000);
  }

  document.getElementById("role_id").addEventListener("change", function () {
    getAboutRole();
  });

  function getAboutRole() {
    var roleSelect = document.getElementById("role_id");
    var about = roleSelect.options[roleSelect.selectedIndex].getAttribute("data-about");
    var aboutField = document.getElementById("roleAbout");
    var aboutSpan = document.getElementById("roleAboutText");

    if (about) {
      aboutField.classList.remove("d-none");
      aboutSpan.textContent = about;
    } else {
      aboutField.classList.add("d-none");
      aboutSpan.textContent = "";
    }
  }
  document.getElementById("seePass").addEventListener("click", function () {
    verpass(1);
  });
  document.getElementById("seeConfirm").addEventListener("click", function () {
    verpass(2);
  });

  function verpass(param) {
    var pass1 = document.getElementById("password");
    var pass2 = document.getElementById("password2");
    if (param == 1) {
      pass1.type = pass1.type == "password" ? "text" : "password";
    } else {
      pass2.type = pass2.type == "password" ? "text" : "password";
    }
  }

  function displaySpinner(id, show) {
    const button = document.getElementById(id);
    const spinner = button.querySelector(".spinner-border");
    const buttonText = button.querySelector(".button-text");
    const textBtn = id === "addCompany" ? "Agregar Empresa" : "Agregar Usuario";
    if (!show) {
      spinner.classList.add("d-none");
      buttonText.textContent = textBtn;
      button.disabled = false;
    } else {
      spinner.classList.remove("d-none");
      buttonText.textContent = "Procesando...";
      button.disabled = true;
    }
  }

  const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
  const popoverList = [...popoverTriggerList].map((popoverTriggerEl) => new bootstrap.Popover(popoverTriggerEl));
}
