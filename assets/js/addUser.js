export function init() {
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
    const usersContainer = document.getElementById("usersContainer");
    usersContainer.innerHTML = "";

    users.forEach((user) => {
      const userPhoto = user.url_pic || "assets/img/empty_user.png";

      usersContainer.innerHTML += `
        <div class="col-12 mb-4">
            <div class="card shadow-sm">
                <div class="row g-0">
                    <!-- Foto -->
                    <div class="col-md-2 d-flex align-items-center justify-content-center p-3 bg-light">
                        <img src="${baseUrl}${userPhoto}" class="img-fluid rounded-circle" style="width: 80px; height: 80px; object-fit: cover;" alt="User photo">
                    </div>
                    
                    <!-- Datos -->
                    <div class="col-md-10">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="card-title mb-1">${user.name}</h5>
                                    <p class="text-muted small mb-1">
                                        <i class="fas fa-envelope me-2"></i>${user.email}
                                    </p>
                                    <p class="text-muted small">
                                        <i class="fas fa-user-tag me-2"></i>${user.role_type}
                                    </p>
                                </div>
                                <div>
                                    <button class="btn btn-sm btn-outline-primary edit-user me-2" data-id="${user.id}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger remove-user" data-id="${user.id}">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </div>
                            
                            ${
                              user.description
                                ? `
                            <div class="mt-2 pt-2 border-top">
                                <p class="card-text">${user.description}</p>
                            </div>
                            `
                                : ""
                            }
                        </div>
                    </div>
                </div>
            </div>
        </div>
        `;
    });

    // Event listeners para botones
    document.querySelectorAll(".remove-user").forEach((btn) => {
      btn.addEventListener("click", (e) => {
        deleteUser(e.target.closest("button").dataset.id);
      });
    });

    document.querySelectorAll(".edit-user").forEach((btn) => {
      btn.addEventListener("click", (e) => {
        const userId = e.target.closest("button").dataset.id;
        loadUserForEdit(userId);
      });
    });
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

  async function loadUserForEdit(userId) {
    try {
      const response = await fetch(`${baseUrl}user_admin/controllers/users.php?id=${userId}&action=getUserForEdit`, {
        method: "GET",
      });

      const { success, data } = await response.json();

      if (success) {
        const user = data;
        const userForm = document.getElementById("addUserForm");
        const profileImg = user.url_pic || "assets/img/empty_user.png";
        userForm.querySelector("#profilePreview").src = `${baseUrl}${profileImg}`;
        userForm.querySelector("#username").value = user.name;
        userForm.querySelector("#correo").value = user.email;
        document.querySelector("#passwordGroup").classList.add("d-none");
        document.querySelector("#confirmPasswordGroup").classList.add("d-none");
        userForm.querySelector("#descripcion").value = user.description || "";
        userForm.querySelector("#user_id").value = user.id;
        userForm.querySelector("#role_id").value = user.role_id;
        document.querySelector("#roleGroup").classList.remove("d-none");
        const roleSelect = document.getElementById("role_id");
        if (roleSelect) {
          for (let i = 0; i < roleSelect.options.length; i++) {
            if (roleSelect.options[i].value == user.role_id) {
              roleSelect.selectedIndex = i;
              break;
            }
          }
        }
        if (user.role_id == 2) {
          document.querySelector("#roleGroup").classList.add("d-none");
        }
        // Cambiar el texto del botón
        document.getElementById("addUser").querySelector(".button-text-spinner").textContent = "Actualizar Usuario";

        // Mostrar botón de cancelar
        document.getElementById("cancelEdit").classList.remove("d-none");

        // Desplazar al formulario
        document.getElementById("addUserForm").scrollIntoView({ behavior: "smooth" });
      }
    } catch (error) {
      console.error("Error:", error);
      alert("Error al cargar usuario para edición");
    }
  }

  document.getElementById("cancelEdit").addEventListener("click", function () {
    resetUserForm();
  });

  function resetUserForm() {
    document.getElementById("addUserForm").reset();
    document.getElementById("user_id").value = "";
    document.querySelector("#profilePreview").src = `${baseUrl}assets/img/empty_user.png`;
    document.querySelector("#passwordGroup").classList.remove("d-none");
    document.querySelector("#confirmPasswordGroup").classList.remove("d-none");
    document.getElementById("addUser").querySelector(".button-text-spinner").textContent = "Agregar Usuario";
    document.getElementById("cancelEdit").classList.add("d-none");
    // También puedes limpiar la previsualización de la foto si es necesario
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

    try {
      const formData = new FormData(this);
      const userId = document.getElementById("user_id").value;
      const controller = userId ? `updateUser.php` : `add_user_controller.php`;
      const response = await fetch(`${baseUrl}user_admin/controllers/${controller}`, {
        method: "POST",
        body: formData,
      });

      const { success, title, message, errors } = await response.json();

      if (success) {
        // Éxito: limpiar formulario y mostrar mensaje
        resetUserForm();
        // alert(data.message || "Usuario agregado exitosamente");
        handleInfoModal("Éxito" || title, message || "Usuario agregado exitosamente");
        // crear setitmeout para que se reinicie la página después de 2 segundos
        setTimeout(() => {
          location.reload();
        }, 2000);
      } else {
        console.log(errors || message);
        let errorMessage = message || "Ocurrió un error";
        if (errors && typeof errors === "object") {
          // Convertir objeto de errores a string legible
          errorMessage += ":\n - " + Object.values(errors).join("\n - ");
        }
        // Manejar errores
        handleInfoModal("Error" || title, errorMessage || "Error al agregar usuario");
      }
    } catch (error) {
      console.error("Error:", error.message);
      handleInfoModal("Error" || title, error.message || "Error de conexión. Por favor, inténtelo más tarde.");
    } finally {
      // Ocultar spinner y habilitar botón
      displaySpinner("addUser", false);
      document.getElementById("roleAbout").classList.add("d-none");
    }
  });

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
    const buttonText = button.querySelector(".button-text-spinner");
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

  document.getElementById("profile_picture").addEventListener("change", function (event) {
    const file = event.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function (e) {
        document.getElementById("profilePreview").src = e.target.result;
      };
      reader.readAsDataURL(file);
    }
  });
  const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
  const popoverList = [...popoverTriggerList].map((popoverTriggerEl) => new bootstrap.Popover(popoverTriggerEl));
}
function handleInfoModal(title = null, message = null) {
  let titulo = document.getElementById("infoModalLabel");
  let mensaje = document.getElementById("infoModalMessage");
  titulo.textContent = title;
  mensaje.textContent = message;
  const modal = new bootstrap.Modal(document.getElementById("infoModal"));
  modal.show();
}
