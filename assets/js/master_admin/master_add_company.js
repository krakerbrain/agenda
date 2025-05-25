export function init() {
  document.getElementById("addCompanyForm").addEventListener("submit", async function (event) {
    event.preventDefault();
    // Mostrar spinner y deshabilitar botón
    displaySpinner("addCompany", true);
    const formData = new FormData(this);

    try {
      const response = await fetch(`${baseUrl}user_admin/controllers/add_company.php`, {
        method: "POST",
        body: formData,
      });
      const { success, company_id, error, debug } = await response.json();
      if (success) {
        document.getElementById("company_id").value = company_id;
        //limpiar formulario
        this.reset();
        alert("Empresa agregada exitosamente");
      } else {
        alert(error);
        console.error("Error:", debug);
      }
    } catch (debug) {
      console.error("Error:", debug);
    } finally {
      // Ocultar spinner y habilitar botón
      displaySpinner("addCompany", false);
    }
  });
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
        alert(error);
        this.reset();
      }
    } catch (error) {
      console.error("Error:", error);
    } finally {
      // Ocultar spinner y habilitar botón
      displaySpinner("addUser", false);
    }
  });

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
}
