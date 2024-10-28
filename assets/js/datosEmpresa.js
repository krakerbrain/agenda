export function initDatosEmpresa() {
  const form = document.getElementById("datosEmpresaForm");
  const formRedes = document.getElementById("social-form");

  async function getDatosEmpresa() {
    try {
      const response = await fetch(`${baseUrl}user_admin/controllers/datosEmpresa.php`, {
        method: "GET",
      });
      const { success, data } = await response.json();

      if (success) {
        const { name, phone, address, description, logo } = data[0];
        const logoUrl = logo != "" ? logo : "assets/images/logo.png";

        document.querySelector(".companyName").textContent = name;
        document.querySelector(".logoEmpresa").src = `${baseUrl}${logoUrl}`;
        document.querySelector("#phone").value = phone;
        document.querySelector("#address").value = address;
        document.querySelector("#description").textContent = description;
        document.querySelector("#companyName").value = name;
        document.querySelector("#logoUrl").value = logoUrl;
      }
    } catch (error) {
      console.error(error);
    }
  }

  async function loadSocials() {
    try {
      const response = await fetch(`${baseUrl}user_admin/controllers/redesSociales.php`, {
        method: "GET",
      });
      const { success, data } = await response.json();

      if (success) {
        getSocials(data);
      }
    } catch (error) {
      console.error(error);
    }
  }

  getDatosEmpresa();
  loadSocials();

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    const formData = new FormData(form);
    // formData.append("action", "add_social"); // Indicamos que es una nueva red social
    try {
      const response = await fetch(`${baseUrl}user_admin/controllers/datosEmpresa.php`, {
        method: "POST",
        body: formData,
      });
      const { success, message } = await response.json();

      if (success) {
        alert(message);
        location.reload();
      }
    } catch (error) {
      console.error(error);
    }
  });

  document.getElementById("social-network").addEventListener("change", function () {
    const selectedNetwork = this.value;
    const socialUrlInput = document.getElementById("social-url");
    if (selectedNetwork === "8") {
      // Suponiendo que el valor para WhatsApp es 'whatsapp'
      const phoneNumber = "5211234567890"; // Aquí usas el número actual del cliente
      socialUrlInput.value = `https://wa.me/${phoneNumber}`;
    } else {
      socialUrlInput.value = ""; // Limpia el campo si no es WhatsApp
    }
  });

  formRedes.addEventListener("submit", async (e) => {
    e.preventDefault();
    const formData = new FormData(formRedes);
    try {
      const response = await fetch(`${baseUrl}user_admin/controllers/redesSociales.php`, {
        method: "POST",
        body: formData,
      });

      const { success, message } = await response.json();

      if (success) {
        alert(message);
        formRedes.reset();
        loadSocials();
      }
    } catch (error) {
      console.error(error);
    }
  });

  function getSocials(data) {
    const socialsTable = document.getElementById("social-networks");

    socialsTable.innerHTML = "";
    data.forEach((social) => {
      const tr = document.createElement("tr");
      let redPreferida = social.red_preferida == 1 ? "checked" : "";
      tr.innerHTML = `
        <td>${social.nombre}</td>
        <td>${social.url}</td>
      <td class="text-center">
          <input class="form-check-input" type="radio" name="redPreferida" 
          id="redPreferida-${social.id}" value="${social.id}" ${redPreferida} disabled>
      </td>
        <td>
          <button class="btn btn-danger remove-social" data-id="${social.id}" data-preferred="${social.red_preferida}">Eliminar</button>
        </td>
      `;
      socialsTable.appendChild(tr);
    });
    if (data.length > 0) {
      document.querySelectorAll(".remove-social").forEach((button) => {
        button.addEventListener("click", (e) => {
          const socialId = e.target.dataset.id;
          const preferred = e.target.dataset.preferred;
          deleteSocial(socialId, preferred);
        });
      });
    }

    // Deshabilitar los radios hasta que el lápiz sea presionado
    disableRadioInputs(true);
  }

  // Función para habilitar/deshabilitar los radios
  function disableRadioInputs(disabled) {
    document.querySelectorAll('input[name="redPreferida"]').forEach((radio) => {
      radio.disabled = disabled;
    });
  }

  // Botón de editar (lápiz)
  document.getElementById("edit-preferred").addEventListener("click", () => {
    // Habilitar los radios para permitir selección
    disableRadioInputs(false);

    // Añadir event listener a los radios cuando se habiliten
    document.querySelectorAll('input[name="redPreferida"]').forEach((radio) => {
      radio.addEventListener("change", async (e) => {
        const selectedId = e.target.value; // ID de la red social seleccionada como preferida

        try {
          const data = {
            id: selectedId,
            preferida: true,
            action: "set_preferred",
          };
          // Enviar una solicitud para actualizar la red preferida
          const response = await fetch(`${baseUrl}user_admin/controllers/redesSociales.php`, {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
            },
            body: JSON.stringify(data),
          });
          const { success, message } = await response.json();

          if (success) {
            alert(message);
            // Actualizar el DOM con la nueva red preferida
            updatePreferredSocialInDOM(selectedId);
            disableRadioInputs(true); // Deshabilitar los radios de nuevo
          }
        } catch (error) {
          console.error(error);
        }
      });
    });
  });

  async function deleteSocial(socialId, isPreferred) {
    try {
      const response = await fetch(`${baseUrl}user_admin/controllers/redesSociales.php`, {
        method: "DELETE",
        body: JSON.stringify({ id: socialId }), // Es mejor enviar datos JSON en vez de un string plano
        headers: {
          "Content-Type": "application/json",
        },
      });
      const { success, message } = await response.json();

      if (success) {
        // Contar las redes sociales restantes en la tabla
        const remainingSocials = document.querySelectorAll("#social-networks tr").length;
        if (isPreferred == 1 && remainingSocials > 1) {
          modalSeleccionRedFavorita();
        } else {
          alert(message);
          loadSocials();
        }
      }
    } catch (error) {
      console.error(error);
    }
  }

  // Función que llena el modal con las redes sociales
  async function modalSeleccionRedFavorita() {
    try {
      // Obtener las redes sociales mediante fetch
      const response = await fetch(`${baseUrl}user_admin/controllers/redesSociales.php`, {
        method: "GET",
      });

      const { success, data } = await response.json();

      if (success) {
        getSocialsModal(data);

        // Mostrar el modal
        const modal = new bootstrap.Modal(document.getElementById("preferedSocial"));
        modal.show();
      }
    } catch (error) {
      console.error("Error al obtener las redes sociales:", error);
    }
  }

  // Función para crear el select de redes sociales en el modal
  function getSocialsModal(data) {
    const modalBody = document.querySelector("#preferedSocial .modal-body");

    // Limpiar contenido anterior
    modalBody.innerHTML = "";

    // Crear un select dinámico
    const select = document.createElement("select");
    select.classList.add("form-select");

    data.forEach((redSocial) => {
      const option = document.createElement("option");
      option.value = redSocial.id;
      option.textContent = redSocial.nombre;
      select.appendChild(option);
    });
    modalBody.appendChild(select);

    // Añadir listener al botón de aceptar
    document.getElementById("acceptButton").addEventListener("click", () => {
      const selectedId = select.value; // Obtener el valor seleccionado del select
      actualizarRedPreferida(selectedId); // Actualizar la red preferida
    });
  }

  // Función para enviar la selección de red preferida al backend
  async function actualizarRedPreferida(selectedId) {
    try {
      const data = {
        id: selectedId,
        preferida: true,
        action: "set_preferred",
      };

      // Enviar solicitud POST para actualizar la red preferida
      const response = await fetch(`${baseUrl}user_admin/controllers/redesSociales.php`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(data),
      });

      const { success, message } = await response.json();

      if (success) {
        // alert(message);
        loadSocials();
      } else {
        alert("Error al actualizar la red social preferida");
      }
    } catch (error) {
      console.error("Error en la solicitud:", error);
    }
  }

  // Función para actualizar el DOM y reflejar la red preferida
  function updatePreferredSocialInDOM(preferredId) {
    document.querySelectorAll(".remove-social").forEach((button) => {
      const socialId = button.dataset.id;

      // Si el id coincide con el preferido, actualizar el dataset a "1"
      if (socialId === preferredId) {
        button.dataset.preferred = "1";
      } else {
        // El resto de redes no son preferidas, poner dataset a "0"
        button.dataset.preferred = "0";
      }
    });
  }

  const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
  const popoverList = [...popoverTriggerList].map((popoverTriggerEl) => new bootstrap.Popover(popoverTriggerEl));
}
