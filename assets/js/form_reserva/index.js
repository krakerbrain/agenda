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
    const date = document.getElementById("date").value;
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
    var modalBody = document.querySelector("#responseModalBody");
    modalBody.innerText = "Por favor, completa el formulario antes de continuar.";
    var modal = new bootstrap.Modal(document.getElementById("responseModal"));
    modal.show();
  }
}

document.getElementById("service").addEventListener("change", function (event) {
  updateServiceDuration();
  getObservation("service");
  getServiceCategory(event.target.value);
  getAvailableDays();
});

document.getElementById("category").addEventListener("change", function () {
  getObservation("category");
});

document.getElementById("date").addEventListener("change", function () {
  fetchAvailableTimes();
});

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

function getAvailableDays() {
  const BASE_URL = `${baseUrl}reservas/controller/`;
  const calendarDaysAvailable = company_days_available;
  const serviceId = document.getElementById("service").value;
  const companyId = document.getElementById("company_id").value;
  const url = BASE_URL + "get_days_availability.php";

  const data = {
    service_id: serviceId,
    // calendar_days_available: calendarDaysAvailable,
    company_id: companyId,
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

        let message; // Variable para el mensaje
        let modalTitle = document.querySelector(".modal-title");
        if (clickedDate > maxDate) {
          // Si la fecha es mayor a la fecha máxima permitida
          modalTitle.textContent = "Fecha deshabilitada";
          message = `Lo sentimos, esta fecha aún no ha sido habilitada para reservas.`;
        } else {
          // Si es una fecha anterior pero está deshabilitada
          modalTitle.textContent = "Fecha ocupada";
          message = `Lo sentimos, este día no está disponible para reservas.`;
        }

        // Cerrar el calendario antes de mostrar el modal
        instance.close();
        // Mostrar el mensaje en el modal
        const modalBody = document.querySelector(".modal-body");
        modalBody.innerText = message;
        const modal = new bootstrap.Modal(document.getElementById("responseModal"));
        modal.show();
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

        flatpickr("#date", {
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
      } else {
        console.error("Error:", data.message);
      }
    })
    .catch((error) => console.error("Error:", error));
}

async function fetchAvailableTimes() {
  const BASE_URL = `${baseUrl}reservas/controller/`;
  const timeInput = document.getElementById("time");
  const companyID = document.getElementById("company_id").value;

  const date = document.getElementById("date").value;
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
        }),
      });

      const { success, available_times, message } = await response.json();

      timeInput.innerHTML = ""; // Clear previous options

      if (success) {
        if (available_times.length > 0) {
          let availableTimesButtons = "";

          available_times.forEach((time, index) => {
            if (index === 0) {
              document.getElementById("selected_time").value = time;
            }
            //si solo hay uno agregar al boton la  clase selected_time
            availableTimesButtons += `<button type="button" class="btn btn-outline-dark btn-light mb-2 me-2 available-time ${index === 0 ? "selected-time" : ""}" data-time="${time}">${time}</button>`;
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
  const dateRaw = document.getElementById("date").value;
  const date = formatDate(dateRaw);
  const time = document.getElementById("selected_time").value;
  const name = document.getElementById("name").value;
  const phone = document.getElementById("phone").value;
  const mail = document.getElementById("mail").value;

  // Crear el contenido del modal
  const confirmationContent = `
       <p style="margin-bottom: 0.5rem;">Hola <strong>${name}</strong>,</p>
        <p style="margin-bottom: 0.5rem;">Estos son los detalles de tu reserva:</p>
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
}

function formatDate(dateString) {
  const date = new Date(dateString);
  return new Intl.DateTimeFormat("es-ES", {
    weekday: "long",
    day: "numeric",
    month: "long",
    year: "numeric",
  }).format(date);
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

    const { message } = await response.json();
    // Aquí puedes seguir con la lógica anterior si el JSON es válido
    var modalConfirm = document.querySelector("#responseModalBody");
    modalConfirm.innerText = message;
    var modal = new bootstrap.Modal(document.getElementById("responseModal"));
    modal.show();

    var acceptButton = document.getElementById("acceptButton");
    acceptButton.addEventListener("click", function () {
      if (response.ok) {
        location.reload();
      }
    });
  } catch (error) {
    console.error("Error:", error);
  } finally {
    // Ocultar spinner y habilitar botón después de que la solicitud se complete
    spinner.classList.add("d-none");
    buttonText.textContent = "Reservar";
    reservarBtn.disabled = false;
  }
}
