function validarPaso(step) {
  // Validación del paso actual antes de avanzar
  if (step === 2) {
    const service = document.getElementById("service").value;
    const categoryContainer = document.getElementById("categoryContainer");
    if (!categoryContainer.classList.contains("d-none")) {
      const category = document.getElementById("category").value;
      return category !== "";
    }
    return service !== "";
  } else if (step === 3) {
    const user_id = document.getElementById("selected_user_id").value;
    const date = document.getElementById("date-" + user_id).value;
    const time = document.getElementById("selected_time").value;
    return date !== "" && time !== "";
  }
  return true; // Paso 3 no necesita validación adicional
}

function showStep(step) {
  if (validarPaso(step)) {
    document.querySelectorAll(".step").forEach(function (element) {
      element.classList.add("d-none");
    });
    document.getElementById("step" + step).classList.remove("d-none");
  } else {
    // Mostrar mensaje de error
    handleModal("Formulario incompleto", "Por favor, completa el formulario antes de continuar.");
  }
}

document.getElementById("service").addEventListener("change", function (event) {
  updateServiceDuration();
  getObservation("service");
  getServiceCategory(event.target.value);
  // getAvailableDays();
  getProvidersForService(event.target.value); // Nueva función para obtener proveedores
});

document.getElementById("category").addEventListener("change", function () {
  getObservation("category");
});

// document.getElementById("date").addEventListener("change", function () {
//   fetchAvailableTimes();
// });

function getProvidersForService(serviceId) {
  const BASE_URL = `${baseUrl}reservas/controller/`;
  const url = BASE_URL + "get_service_providers.php"; // Necesitarás crear este endpoint
  const companyId = document.getElementById("company_id").value;

  fetch(url, {
    method: "POST",
    body: JSON.stringify({ service_id: serviceId, company_id: companyId }),
    headers: {
      "Content-Type": "application/json",
    },
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        const providers = data.providers;
        renderProviderDateInputs(providers);
      } else {
        console.error("Error:", data.message);
      }
    })
    .catch((error) => console.error("Error:", error));
}

function renderProviderDateInputs(providers) {
  const container = document.getElementById("providers-dates-container");
  const providers_count = document.getElementById("providers_count").value;

  container.innerHTML = "";
  let providerHTML = "";
  providers.forEach((provider) => {
    if (providers_count > 1) {
      providerHTML = `
      <div class="col-12 mb-4 provider-section provider-${provider.id}" data-provider-name="${provider.name}">
        <div class="card shadow-sm">
          <div class="row g-0">
            <!-- Foto del proveedor con overlay para info -->
            <div class="col-md-4 bg-light position-relative">
              <!-- Versión desktop (cuadrada) -->
              <div class="ratio ratio-1x1 d-none d-md-block photo-container">
                <img src="${baseUrl}${provider.url_pic || "assets/img/empty_user.png"}" 
                     class="img-fluid w-100 h-100 p-2" 
                     style="object-fit: cover;"
                     alt="${provider.name}">
                <div class="photo-overlay provider-info-trigger" data-provider-id="${provider.id}">
                  <i class="fas fa-info-circle overlay-icon"></i>
                </div>
              </div>
              
              <!-- Versión móvil (circular pequeña) -->
              <div class="d-md-none p-2 d-flex align-items-center">
                <div class="position-relative">
                  <img src="${baseUrl}${provider.url_pic || "assets/img/empty_user.png"}" 
                       class="rounded-circle me-3" 
                       style="width: 50px; height: 50px; object-fit: cover;"
                       alt="${provider.name}">
                  <i class="fas fa-info-circle mobile-icon provider-info-trigger" data-provider-id="${provider.id}"></i>
                </div>
                <div class="d-flex align-items-center">
                  <h6 class="card-title mb-0 me-2">${provider.name}</h6>
                  <i class="fas fa-info-circle text-primary small provider-info-trigger" data-provider-id="${provider.id}"></i>
                </div>
              </div>
            </div>
            
            <!-- Contenido -->
            <div class="col-md-8">
              <div class="card-body p-2">
                <!-- Título solo visible en desktop -->
                <div class="d-none d-md-block">
                  <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="d-flex align-items-center">
                      <h6 class="card-title me-2">${provider.name}</h6>
                      <i class="fas fa-info-circle text-primary small provider-info-trigger" "></i>
                    </div>
                  </div>
                </div>
                
                <!-- Selector de fecha -->
                <div class="mb-3">
                  <input type="text" 
                        id="date-${provider.id}" 
                        name="date-${provider.id}" 
                        class="form-control provider-date-input" 
                        placeholder="Selecciona la fecha" 
                        required>
                </div>
                
                <!-- Horarios disponibles -->
                <div class="mt-3 pt-1 border-top">
                  <label for="time-buttons" class="time-btns-label-${provider.id} form-label d-none">Selecciona tu hora preferida:</label>
                  <div class="time-buttons d-flex flex-wrap gap-2" id="time-buttons-${provider.id}"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    `;
    } else {
      providerHTML = `
      <div class="provider-${provider.id}" data-provider-name="${provider.name}">
      <div class="mb-3">
                        <input type="text" 
                              id="date-${provider.id}" 
                              name="date-${provider.id}" 
                              class="form-control provider-date-input" 
                              placeholder="Selecciona la fecha" 
                              required>
                      </div>
        <div class="mb-3">
          <!-- Contenedor para los botones de hora -->
          <label for="time-buttons" class="time-btns-label-${provider.id} form-label d-none">Selecciona tu hora preferida:</label>
          <div id="time-buttons-${provider.id}" class="time-buttons">
        </div>
      </div>
      </div>`;
    }
    container.insertAdjacentHTML("beforeend", providerHTML);
    getAvailableDays(provider.id, `date-${provider.id}`, providers_count);
  });

  // Agregar event listeners después de crear todos los cards
  document.querySelectorAll(".provider-info-trigger").forEach((trigger) => {
    trigger.addEventListener("click", function () {
      const providerId = this.getAttribute("data-provider-id");
      const provider = providers.find((p) => p.id == providerId);
      if (provider) {
        showProviderInfoModal(provider);
      }
    });
  });
}

function updateServiceDuration() {
  const serviceSelect = document.getElementById("service");
  const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
  const duration = selectedOption.getAttribute("data-duration");

  document.getElementById("service_duration").value = duration || ""; // Evita valores nulos
}

function getObservation(id) {
  var serviceSelect = document.getElementById(id);
  var observation = serviceSelect.options[serviceSelect.selectedIndex].getAttribute("data-observation");
  var duration = parseInt(serviceSelect.options[serviceSelect.selectedIndex].getAttribute("data-duration"), 10);
  var observationField = document.getElementById(id + "Observation");
  var observationSpan = document.getElementById(id + "TextObservation");

  if (observation || duration) {
    observationField.classList.remove("d-none");

    // Construir el mensaje con observación
    var message = observation ? observation + "<br>" : "";

    // Calcular duración en horas y minutos
    if (duration) {
      var hours = Math.floor(duration / 60);
      var minutes = duration % 60;
      var durationText = "<p class='mt-1'><strong>Duración aproximada:</strong> ";

      if (hours > 0) {
        durationText += hours + (hours === 1 ? " hora" : " horas");
      }

      if (minutes > 0) {
        durationText += (hours > 0 ? " y " : "") + minutes + " minutos";
      }

      durationText += "</p>";
      message += durationText;
    }

    observationSpan.innerHTML = message; // Usamos innerHTML para interpretar el formato
  } else {
    observationField.classList.add("d-none");
    observationSpan.innerHTML = "";
  }
}

async function getServiceCategory(serviceId) {
  try {
    let url = `${baseUrl}reservas/controller/getCategories.php`;
    let data = {
      service_id: serviceId,
    };

    const response = await fetch(url, {
      method: "POST",
      body: JSON.stringify(data),
      headers: {
        "Content-Type": "application/json",
      },
    });

    const { success, categories } = await response.json();

    let select = document.getElementById("category");
    if (success) {
      document.getElementById("categoryContainer").classList.remove("d-none");
      select.disabled = false;
      let categoryOption = '<option value="" selected>Selecciona una categoría</option>';
      categories.forEach(function (category) {
        categoryOption += `<option value="${category.id}" data-observation="${category.category_description}">${category.category_name}</option>`;
      });
      select.innerHTML = categoryOption;
    } else {
      document.getElementById("categoryContainer").classList.add("d-none");
      document.getElementById("categoryObservation").classList.add("d-none");
      select.disabled = true;
    }
  } catch (error) {
    console.error("Error:", error);
  }
}

function getAvailableDays(user_id, dateDomId, providers_count = null) {
  const BASE_URL = `${baseUrl}reservas/controller/`;
  const calendarDaysAvailable = company_days_available;
  const serviceId = document.getElementById("service").value;
  const companyId = document.getElementById("company_id").value;
  const url = BASE_URL + "get_days_availability.php";

  const data = {
    service_id: serviceId,
    // calendar_days_available: calendarDaysAvailable,
    company_id: companyId,
    provider: user_id,
  };

  // Función anidada para registrar eventos en fechas deshabilitadas
  function registerDisabledDateClickEvents(instance) {
    const calendarDaysAvailable = company_days_available; // Número de días que el calendario permite seleccionar
    const maxDate = new Date().fp_incr(calendarDaysAvailable); // Fecha máxima permitida sumando los días disponibles

    // Selecciona solo los elementos con la clase .flatpickr-disabled dentro de .dayContainer
    document.querySelectorAll(".dayContainer .flatpickr-disabled").forEach((element) => {
      element.addEventListener("click", function () {
        // Obtenemos la fecha del aria-label (ejemplo: "October 9, 2024")
        const dateString = element.getAttribute("aria-label");
        const clickedDate = new Date(dateString); // Convertimos el string a objeto Date

        if (clickedDate > maxDate) {
          // Si la fecha es mayor a la fecha máxima permitida
          handleModal("Fecha deshabilitada", "Lo sentimos, esta fecha aún no ha sido habilitada para reservas.");
        } else {
          // Si es una fecha anterior pero está deshabilitada
          handleModal("Fecha ocupada", "Lo sentimos, este día no está disponible para reservas.");
        }
        // Cerrar el calendario antes de mostrar el modal
        instance.close();
      });
    });
  }

  fetch(url, {
    method: "POST",
    body: JSON.stringify(data),
    headers: {
      "Content-Type": "application/json",
    },
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        const availableDates = data.available_days;

        flatpickr(`#${dateDomId}`, {
          locale: "es",
          enableTime: false,
          altInput: true,
          dateFormat: "Y-m-d",
          altFormat: "d-m-Y",
          minDate: "today",
          maxDate: data.calendar_mode == "fijo" ? availableDates[availableDates.length - 1] : new Date().fp_incr(calendarDaysAvailable),
          enable: [
            function (date) {
              return availableDates.includes(date.toISOString().split("T")[0]);
            },
          ],
          onReady: [
            function (selectedDates, dateStr, instance) {
              registerDisabledDateClickEvents(instance);
            },
          ],
          onValueUpdate: [
            function (selectedDates, dateStr, instance) {
              registerDisabledDateClickEvents(instance);
            },
          ],
          onMonthChange: [
            function (selectedDates, dateStr, instance) {
              registerDisabledDateClickEvents(instance);
            },
          ],
        });

        document.getElementById(dateDomId).addEventListener("change", function () {
          fetchAvailableTimes(user_id, this.value); // Pasamos user_id y la fecha seleccionada
        });
      } else {
        console.error("Error:", data.message);
      }
    })
    .catch((error) => console.error("Error:", error));
}

async function fetchAvailableTimes(user_id, date) {
  const BASE_URL = `${baseUrl}reservas/controller/`;
  const timeInput = document.getElementById("time-buttons-" + user_id);
  const companyID = document.getElementById("company_id").value;

  // const date = document.getElementById("date").value;
  const serviceId = document.getElementById("service").value;
  const scheduleMode = document.getElementById("schedule_mode").value === "blocks" ? "get_available_hours_blocks.php" : "get_available_hours_free.php";

  if (date && serviceId) {
    try {
      const response = await fetch(BASE_URL + scheduleMode, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          date: date,
          service_id: serviceId,
          company_id: companyID,
          provider: user_id,
        }),
      });

      const { success, available_times, message } = await response.json();

      timeInput.innerHTML = ""; // Clear previous options

      if (success) {
        if (available_times.length > 0) {
          document.querySelector(".time-btns-label-" + user_id).classList.remove("d-none");
          let availableTimesButtons = "";
          // const autoSelectedFlag = document.getElementById("auto_time_selected");

          available_times.forEach((time, index) => {
            // Solo marcar como selected-time si es el primer elemento Y no se ha marcado antes
            // const shouldMark = index === 0 && autoSelectedFlag.value === "0";
            // if (shouldMark) {
            //   document.getElementById("selected_time").value = time;
            //   autoSelectedFlag.value = "1";
            // }
            //si solo hay uno agregar al boton la  clase selected_time
            availableTimesButtons += `<button type="button" class="btn btn-outline-dark btn-light mb-2 me-1 available-time" data-time="${time}" data-user-id="${user_id}">${time}</button>`;
          });

          // Insert the buttons into the DOM
          timeInput.innerHTML = availableTimesButtons;

          // Add an event listener for button selection
          const timeButtons = document.querySelectorAll(".available-time");
          timeButtons.forEach((button) => {
            button.addEventListener("click", () => {
              // Mark the clicked button as selected and update the form value
              document.querySelectorAll(".available-time").forEach((btn) => btn.classList.remove("selected-time"));
              button.classList.add("selected-time");
              // Update hidden input field with selected time value (for form submission)
              document.getElementById("selected_time").value = button.getAttribute("data-time");
              document.getElementById("selected_user_id").value = button.getAttribute("data-user-id");
              document.getElementById("selected_date").value = document.querySelector("#date-" + user_id).value;
            });
          });
        } else {
          timeInput.innerHTML = "<p class='text-dark'>No hay horas disponibles este día. Selecciona otro</p>";
        }
      } else {
        alert(message);
      }
    } catch (error) {
      console.error("Error:", error);
    }
  }
}

document.getElementById("appointmentForm").addEventListener("submit", function (event) {
  event.preventDefault();
  document.querySelectorAll(".customer-field").forEach((field) => {
    field.disabled = false;
  });
  const form = document.querySelector("#appointmentForm"); // Selecciona un formulario del DOM
  const formData = new FormData(form);
  const scheduleMode = document.getElementById("schedule_mode").value;

  if (scheduleMode === "blocks") {
    showConfirmationModal(formData);
  }
});

function showConfirmationModal(formData) {
  // Extraer los datos del formulario
  const service = document.getElementById("service").selectedOptions[0].text;
  const user_id = document.getElementById("selected_user_id").value;
  const userName = document.querySelector(".provider-" + user_id).getAttribute("data-provider-name") || "";
  const dateRaw = document.getElementById("date-" + user_id).value;
  const date = formatDate(dateRaw);
  const time = document.getElementById("selected_time").value;
  const name = document.getElementById("name").value;
  const phone = document.getElementById("phone").value;
  const mail = document.getElementById("mail").value;
  const providers_count = document.getElementById("providers_count").value;

  // Crear el contenido del modal
  const confirmationContent = `
       <p style="margin-bottom: 0.5rem;">Hola <strong>${name}</strong>,</p>
        <p style="margin-bottom: 0.5rem;">Estos son los detalles de tu reserva ${providers_count > 1 ? "con:" : ":"}</p>
        ${providers_count > 1 ? `<p style="margin-bottom: 0.5rem;">${userName}</p>` : ""}
        <p style="margin-bottom: 0.5rem;"><strong>Servicio:</strong> ${service}</p>
        <p style="margin-bottom: 0.5rem;"><strong>Fecha:</strong> ${date}</p>
        <p style="margin-bottom: 0.5rem;"><strong>Hora:</strong> ${time}</p>
        <p style="margin-bottom: 0.5rem;"><strong>Teléfono:</strong> ${phone}</p>
        <p style="margin-bottom: 0.5rem;"><strong>Correo:</strong> ${mail}</p>
        <p style="margin-bottom: 0.5rem;">¿Son correctos estos datos?</p>
  `;

  document.getElementById("confirmationModalBody").innerHTML = confirmationContent;

  // Mostrar el modal de confirmación
  const confirmationModal = new bootstrap.Modal(document.getElementById("confirmationModal"));
  confirmationModal.show();

  // Manejar la confirmación
  document.getElementById("confirmReservation").onclick = function () {
    confirmationModal.hide(); // Ocultar el modal de confirmación
    sendAppointment(formData); // Enviar la reserva
  };
  document.getElementById("cancelReservation").onclick = function () {
    document.querySelectorAll(".customer-field").forEach((field) => {
      field.disabled = true;
    });
  };
}

function showProviderInfoModal(provider) {
  // Asignar valores básicos
  document.getElementById("providerModalName").textContent = provider.name;
  document.getElementById("providerModalImage").src = `${baseUrl}${provider.url_pic || "assets/img/empty_user.png"}`;
  document.getElementById("providerModalImage").alt = provider.name;

  // Asignar descripción (puedes usar datos reales o ficticios)
  const description = provider.description || "Profesional altamente calificado con amplia experiencia en el sector.";
  document.getElementById("providerModalDescription").textContent = description;

  // Asignar servicios (puedes usar datos reales o ficticios)
  const services = provider.services || ["Servicio estándar", "Asesoría básica", "Garantía de 30 días"];
  const servicesList = document.getElementById("providerModalServices");
  servicesList.innerHTML = services.map((service) => `<li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>${service}</li>`).join("");

  // Mostrar el modal
  const modal = new bootstrap.Modal(document.getElementById("providerModal"));
  modal.show();
}

function formatDate(dateString) {
  return new Intl.DateTimeFormat("es-ES", {
    weekday: "long",
    day: "numeric",
    month: "long",
    year: "numeric",
  }).format(new Date(dateString + "T00:00:00"));
}

async function sendAppointment(formData) {
  const BASE_URL = `${baseUrl}reservas/controller/`;

  const reservarBtn = document.getElementById("reservarBtn");
  const spinner = reservarBtn.querySelector(".spinner-border");
  const buttonText = reservarBtn.querySelector(".button-text");

  // Mostrar spinner y deshabilitar botón
  spinner.classList.remove("d-none");
  buttonText.textContent = "Procesando...";
  reservarBtn.disabled = true;

  try {
    const response = await fetch(BASE_URL + "appointment.php", {
      method: "POST",
      body: formData,
    });

    // Verificar si la respuesta es exitosa
    if (!response.ok) {
      // Si la respuesta no es exitosa, intentar leer el cuerpo de la respuesta como JSON
      const errorResponse = await response.json();
      throw new Error(errorResponse.message || "Error en la solicitud: " + response.statusText);
    }

    // Si la respuesta es exitosa, procesar el JSON
    const { message, success } = await response.json();

    if (success) {
      handleModal("Reserva exitosa", message);

      // Configurar el botón de aceptar
      const acceptButton = document.getElementById("acceptButton");
      acceptButton.addEventListener("click", function () {
        const authenticated = document.getElementById("authenticated").value === "true";
        if (authenticated) {
          // Guardar estado en sessionStorage
          sessionStorage.setItem("lastPage", "datesList");
          sessionStorage.setItem("status", "unconfirmed");

          // Redirigir a las configuraciones
          window.location.href = `${baseUrl}user_admin/index.php`;
        } else {
          // Recargar la página para usuarios no autenticados
          location.reload();
        }
      });
    } else {
      handleModal("Error", message);
    }
  } catch (error) {
    console.error("Error:", error);
    handleModal("Error", error.message || "Ocurrió un error al procesar la reserva. Por favor, inténtalo de nuevo.", true);
    // Redirigir al usuario después de mostrar el mensaje de error
  } finally {
    // Ocultar spinner y habilitar botón después de que la solicitud se complete
    spinner.classList.add("d-none");
    buttonText.textContent = "Reservar";
    reservarBtn.disabled = false;
  }
}
function handleModal(title, message, reload = false) {
  const modal = new bootstrap.Modal(document.getElementById("responseModal")); // Obtener el modal
  const modalTitle = document.getElementById("responseModalLabel"); // Obtener el elemento con el ID "responseModalTitle"
  const modalBody = document.getElementById("responseModalBody"); // Obtener el elemento con el ID "responseModalBody"
  modalTitle.innerText = title; // Establecer el título del modal
  modalBody.innerText = message; // Establecer el contenido del modal
  modal.show(); // Mostrar el modal

  // if (reload) {
  //   document.getElementById("acceptButton").addEventListener("click", function () {
  //     location.reload(); // Recargar la página
  //   });
  // }
}

const editCheckBox = document.querySelector("#editCustomer");
if (editCheckBox) {
  editCheckBox.addEventListener("change", function () {
    const customerFields = document.querySelectorAll(".customer-field");
    // document.querySelector("#customer_id").value = ""; // Limpiar el campo oculto
    customerFields.forEach((field) => {
      field.disabled = !editCheckBox.checked;
      // desabilitar el checkbox cuando se presione la primera vez
      editCheckBox.disabled = true;
    });
  });
}

const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
[...popoverTriggerList].map((popoverTriggerEl) => new bootstrap.Popover(popoverTriggerEl));
