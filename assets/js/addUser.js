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
      <div class="w-full">
        <div class="bg-white shadow rounded-lg overflow-hidden">
          <div class="flex flex-col sm:flex-row">
<div class="flex-shrink-0 basis-28 sm:basis-32 flex justify-center items-center bg-gray-100 p-4">
  <img src="${baseUrl}${userPhoto}" alt="User photo"
    class="w-20 h-20 rounded-full object-cover border" />
</div>


            <!-- Datos -->
            <div class="flex-1 p-4">
              <div class="flex sm:flex-row justify-between sm:items-start">
                <div>
                  <h5 class="text-lg font-semibold text-gray-800">${user.name}</h5>
                  <p class="text-sm text-gray-500 mt-1">
                    <i class="fas fa-envelope mr-2 text-gray-400"></i>${user.email}
                  </p>
                  <p class="text-sm text-gray-500 mt-1">
                    <i class="fas fa-user-tag mr-2 text-gray-400"></i>${user.role_type}
                  </p>
                </div>
                <div class="mt-3 sm:mt-0 flex gap-2">
                  <button class="text-blue-600 hover:text-blue-800 transition edit-user" title="Editar" data-id="${user.id}">
                    <i class="fas fa-edit text-lg"></i>
                  </button>
                  <button class="text-red-600 hover:text-red-800 transition remove-user" title="Eliminar" data-id="${user.id}">
                    <i class="fas fa-trash-alt text-lg"></i>
                  </button>
                </div>
              </div>

              ${
                user.description
                  ? `<div class="mt-4 border-t pt-3">
                      <p class="text-sm text-gray-700">${user.description}</p>
                    </div>`
                  : ""
              }
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

  // Cachear elementos del DOM que se usan frecuentemente
  const userForm = document.getElementById("addUserForm");
  const profilePreview = userForm.querySelector("#profilePreview");
  const usernameInput = userForm.querySelector("#username");
  const emailInput = userForm.querySelector("#correo");
  const descriptionInput = userForm.querySelector("#descripcion");
  const userIdInput = userForm.querySelector("#user_id");
  const roleSelect = document.getElementById("role_id");
  const passwordGroup = document.querySelector(".passwordGroup");
  const passwordConfirmGroup = document.querySelector(".passwordConfirmGroup");
  const roleGroup = document.querySelector("#roleGroup");
  const cancelEditBtn = document.getElementById("cancelEdit");
  const submitButtonText = document.querySelector(".button-text-spinner");
  // Función para cargar usuario
  async function loadUserForEdit(userId) {
    try {
      const response = await fetch(`${baseUrl}user_admin/controllers/users.php?id=${userId}&action=getUserForEdit`, {
        method: "GET",
      });

      const { success, data: user } = await response.json();

      if (!success) throw new Error("No se pudo cargar el usuario");

      // Actualizar UI con los datos del usuario
      updateUserForm(user);

      // Cambiar texto del botón y mostrar cancelar
      submitButtonText.textContent = "Actualizar Usuario";
      cancelEditBtn.classList.remove("hidden");

      // Desplazar al formulario
      userForm.scrollIntoView({ behavior: "smooth" });
    } catch (error) {
      console.error("Error al cargar usuario:", error);
      alert("Error al cargar usuario para edición");
    }
  }

  // Función para actualizar el formulario con datos del usuario
  function updateUserForm(user) {
    profilePreview.src = `${baseUrl}${user.url_pic || "assets/img/empty_user.png"}`;
    usernameInput.value = user.name;
    emailInput.value = user.email;
    descriptionInput.value = user.description || "";
    userIdInput.value = user.id;

    // Manejar roles
    if (roleSelect) {
      roleSelect.value = user.role_id;
      roleGroup.classList.toggle("hidden", user.role_id == 2);
    }

    // Ocultar campos de contraseña
    passwordGroup.classList.add("hidden");
    passwordConfirmGroup.classList.add("hidden");
  }

  // Función para resetear el formulario
  function resetUserForm() {
    userForm.reset();
    userIdInput.value = "";
    profilePreview.src = `${baseUrl}assets/img/empty_user.png`;
    passwordGroup.classList.remove("hidden");
    passwordConfirmGroup.classList.remove("hidden");
    submitButtonText.textContent = "Agregar Usuario";
    cancelEditBtn.classList.add("hidden");
  }

  // Event listener
  cancelEditBtn.addEventListener("click", resetUserForm);

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
  // const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
  // const popoverList = [...popoverTriggerList].map((popoverTriggerEl) => new bootstrap.Popover(popoverTriggerEl));
}
function handleInfoModal(title = null, message = null) {
  let titulo = document.getElementById("infoModalLabel");
  let mensaje = document.getElementById("infoModalMessage");
  titulo.textContent = title;
  mensaje.textContent = message;
  // const modal = new bootstrap.Modal(document.getElementById("infoModal"));
  modal.show();
}
