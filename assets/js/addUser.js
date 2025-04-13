export function initAddUser() {
  async function loadUsers() {
    try {
      const response = await fetch(`${baseUrl}user_admin/controllers/users.php`, {
        method: "GET",
      });

      const { success, data } = await response.json();

      if (success) {
        getUsers(data);
        get_role_select();
      }
    } catch (error) {
      console.error(error);
    }
  }

  loadUsers();

  function getUsers(users) {
    const usersTable = document.getElementById("usersTable");
    usersTable.innerHTML = "";
    users.forEach((user) => {
      usersTable.innerHTML += `
        <tr>
          <td>${user.name}</td>
          <td>${user.email}</td>
          <td>${user.role_type}</td>
          <td>
            <button class="btn btn-danger remove-user" data-id="${user.id}">Eliminar</button>
          </td>
        </tr>
      `;
    });
    if (users.length > 0) {
      document.querySelectorAll(".remove-user").forEach((button) => {
        button.addEventListener("click", (e) => {
          const userId = e.target.dataset.id;
          deleteUser(userId);
        });
      });
    }
  }

  async function get_role_select() {
    try {
      const response = await fetch(`${baseUrl}user_admin/controllers/users.php?action=getRoles`, {
        method: "GET",
      });

      const { success, data } = await response.json();

      if (success) {
        const roleSelect = document.querySelector("#role_id");
        let select = "<option selected value>Seleccione rol del usuario</option>";
        data.forEach((role) => {
          select += `<option value="${role.id}" data-about="${role.about_role}">${role.type}</option>`;
        });
        roleSelect.innerHTML = select;
      }
    } catch (error) {
      console.error(error);
    }
  }

  async function deleteUser(id) {
    try {
      const response = await fetch(`${baseUrl}user_admin/controllers/users.php`, {
        method: "DELETE",
        body: JSON.stringify({ id }),
      });

      const { success, message } = await response.json();

      if (success) {
        alert(message);
        loadUsers();
      }
    } catch (error) {
      console.error(error);
    }
  }

  document.getElementById("addUserForm").addEventListener("submit", async function (event) {
    event.preventDefault();

    // Mostrar spinner y deshabilitar botón
    displaySpinner("addUser", true);

    // Limpiar error previo
    document.querySelector("#addUserForm .error").innerHTML = "";
    document.querySelector("#addUserForm .error").classList.add("d-none");

    try {
      const formData = new FormData(this);
      const response = await fetch(`${baseUrl}login/registra_usuario.php`, {
        method: "POST",
        body: formData,
      });

      const data = await response.json();

      if (data.success) {
        // Éxito: limpiar formulario y mostrar mensaje
        this.reset();
        alert(data.message || "Usuario agregado exitosamente");
        loadUsers();
      } else {
        // Mostrar error (usa data.error o data.message según lo que devuelva tu backend)
        const errorMessage = data.error || data.message || "Error al registrar usuario";
        document.querySelector("#addUserForm .error").innerHTML = `<span>${errorMessage}</span>`;
        document.querySelector("#addUserForm .error").classList.remove("d-none");
      }
    } catch (error) {
      console.error("Error:", error);
      document.querySelector("#addUserForm .error").innerHTML = "<span>Error de conexión con el servidor</span>";
      document.querySelector("#addUserForm .error").classList.remove("d-none");
    } finally {
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
