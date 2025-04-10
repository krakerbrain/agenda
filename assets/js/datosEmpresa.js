export function initDatosEmpresa() {
  const form = document.getElementById("datosEmpresaForm");
  const formRedes = document.getElementById("social-form");

  async function getDatosEmpresa() {
    try {
      const response = await fetch(`${baseUrl}user_admin/controllers/datosEmpresa.php`, {
        method: "GET",
      });

      const { success, data, savedBanner } = await response.json();

      if (success) {
        const { name, phone, address, description, logo, selected_banner } = data[0];
        const logoUrl = logo !== "" ? logo : "assets/images/logo.png";
        const company_id = document.querySelector("#companyId").value;

        // Mantener siempre la imagen recortada guardada en la carpeta del usuario
        const savedCropUrl = `${baseUrl}assets/img/banners/user_${company_id}/${savedBanner}`;

        // Determinar la imagen a mostrar en la selección de banner
        let bannerUrl;

        if (!selected_banner) {
          // Si no hay banner seleccionado, mostrar banner vacío
          bannerUrl = `${baseUrl}assets/img/banners/banner_vacio.png`;
        } else if (selected_banner.startsWith("default_")) {
          // Si es un banner predeterminado, usar la ruta general
          bannerUrl = `${baseUrl}assets/img/banners/${selected_banner}`;
        } else {
          // Si es un banner personalizado, usar la carpeta del usuario
          bannerUrl = `${baseUrl}assets/img/banners/user_${company_id}/${selected_banner}`;
        }

        // Mostrar SIEMPRE la imagen recortada guardada
        document.querySelector("#saved-cropped-image").src = savedCropUrl;

        document.querySelector(".companyName").textContent = name;
        document.querySelector(".logoEmpresa").src = `${baseUrl}${logoUrl}`;
        document.querySelector("#phone").value = phone;
        document.querySelector("#address").value = address;
        document.querySelector("#description").textContent = description;
        document.querySelector("#companyName").value = name;
        document.querySelector("#logoUrl").value = logoUrl;
        document.querySelector("#banner-custom").value = savedBanner;

        // Marcar el radio correspondiente
        const bannerRadio = document.querySelector(`input[value="${selected_banner}"]`);
        if (bannerRadio) {
          bannerRadio.checked = true;
        } else {
          document.getElementById("banner-custom").checked = true;
        }
      }
    } catch (error) {
      console.error("Error al obtener los datos de la empresa:", error);
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

      const { success, message, error } = await response.json();

      if (success) {
        alert(message);
        getDatosEmpresa();
        loadSocials();
      } else {
        alert(error);
      }
    } catch (error) {
      console.error(error);
    }
  });

  document.getElementById("social-network").addEventListener("change", function () {
    const selectedNetwork = this.value;
    const socialUrlInput = document.getElementById("social-url");
    const phoneNumber = document.getElementById("phone").value;
    const formattedPhone = phoneNumber.startsWith("+") ? phoneNumber.substring(1) : phoneNumber;

    if (selectedNetwork === "8") {
      // Suponiendo que el valor para WhatsApp es 'whatsapp'
      const phone = formattedPhone != "" ? formattedPhone : "56912345678"; // Aquí usas el número actual del cliente
      socialUrlInput.value = `https://wa.me/${phone}`;
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
      tr.classList.add("body-table");
      let redPreferida = social.red_preferida == 1 ? "checked" : "";
      tr.innerHTML = `
        <td data-cell="red social" class="data">${social.nombre}</td>
        <td data-cell="url" class="data">${social.url}</td>
      <td data-cell="preferida" class="data text-md-center">
          <input class="form-check-input ms-1 ms-md-0" type="radio" name="redPreferida" 
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

  let cropper;

  document.getElementById("banner").addEventListener("change", function (e) {
    const file = e.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function (event) {
        const image = document.getElementById("image-to-crop");
        image.src = event.target.result;

        // Mostrar el contenedor de la imagen
        document.getElementById("image-container").style.display = "block";

        // Si ya existe una instancia de Cropper, destruirla antes de crear una nueva
        if (cropper) {
          cropper.destroy();
        }

        // Inicializar Cropper.js
        cropper = new Cropper(image, {
          aspectRatio: 600 / 150, // Relación de aspecto 4:1 (600x150)
          viewMode: 1, // Restringir el movimiento de la imagen dentro del contenedor
          autoCropArea: 1, // Área de recorte ocupa el 100% de la imagen inicialmente
          movable: true, // Permitir mover la imagen
          rotatable: false, // No permitir rotación
          scalable: false, // No permitir escalar la imagen
          zoomable: true, // Permitir hacer zoom
          minCropBoxWidth: 600, // Ancho mínimo del área de recorte
          minCropBoxHeight: 150, // Alto mínimo del área de recorte
          maxCropBoxWidth: 600, // Ancho máximo del área de recorte
          maxCropBoxHeight: 150, // Alto máximo del área de recorte
          crop(event) {
            // Obtener el canvas recortado con el tamaño deseado (600x150)
            const canvas = cropper.getCroppedCanvas({
              width: 600,
              height: 150,
            });

            // Mostrar la imagen recortada en la previsualización
            const croppedImage = document.getElementById("cropped-image");
            croppedImage.src = canvas.toDataURL();
            croppedImage.style.display = "block";
          },
        });

        // Mostrar el botón de guardar
        document.getElementById("save-banner").style.display = "block";
      };
      reader.readAsDataURL(file);
    }
  });

  document.getElementById("save-banner").addEventListener("click", async function () {
    if (cropper) {
      const previewCanvas = cropper.getCroppedCanvas({
        width: 600,
        height: 300,
        imageSmoothingEnabled: true,
        imageSmoothingQuality: "high",
      });

      const highResCanvas = cropper.getCroppedCanvas({
        width: 600,
        height: 150,
      });

      if (!highResCanvas) {
        alert("Error al obtener la imagen recortada.");
        return;
      }

      highResCanvas.toBlob(
        async (blob) => {
          const companyId = document.getElementById("companyId").value;
          const formData = new FormData();
          const fileName = `banner_user_prefered_${Date.now()}.png`;

          formData.append("banner", blob, fileName);
          formData.append("companyId", companyId);

          try {
            const response = await fetch(`${baseUrl}user_admin/controllers/uploadBanner.php`, {
              method: "POST",
              body: formData,
            });

            const data = await response.json();

            if (data.success) {
              const savedCroppedImage = document.getElementById("saved-cropped-image");
              savedCroppedImage.src = `${baseUrl}${data.imageUrl}`;
              document.getElementById("banner-custom").value = fileName;
              document.getElementById("banner-custom").checked = true;
            } else {
              alert("Error al guardar la imagen.");
            }
          } catch (error) {
            console.error("Error:", error);
          }
        },
        "image/png",
        1
      );

      resetCropper();
    }
  });

  function resetCropper() {
    if (cropper) {
      cropper.destroy();
      cropper = null;
    }

    document.getElementById("image-container").style.display = "none";
    document.getElementById("banner-preview").style.display = "none";
    document.getElementById("cropped-image").style.display = "none";
    document.getElementById("image-to-crop").src = "#";
    document.getElementById("cropped-image").src = "#";
    document.getElementById("banner").value = "";
    document.getElementById("save-banner").style.display = "none";
  }
}
