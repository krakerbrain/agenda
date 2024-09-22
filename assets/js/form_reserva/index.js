// document.addEventListener("DOMContentLoaded", async function () {
//   try {
//     const response = await fetch(`${baseUrl}reservas/controller/initialData.php`);
//     const data = await response.json();
//     // Process the data as needed
//     console.log(data);
//   } catch (error) {
//     console.error("Error fetching initial data:", error);
//   }
// });

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
    const time = document.getElementById("time").value;
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
    var modalBody = document.querySelector(".modal-body");
    modalBody.innerText = "Por favor, completa el formulario antes de continuar.";
    var modal = new bootstrap.Modal(document.getElementById("responseModal"));
    modal.show();
  }
}

document.getElementById("service").addEventListener("change", function (event) {
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

function getObservation(id) {
  var serviceSelect = document.getElementById(id);
  var observation = serviceSelect.options[serviceSelect.selectedIndex].getAttribute("data-observation");
  var observationField = document.getElementById(id + "Observation");
  var observationSpan = document.getElementById(id + "TextObservation");

  if (observation) {
    observationField.classList.remove("d-none");
    observationSpan.textContent = observation;
  } else {
    observationField.classList.add("d-none");
    observationSpan.textContent = "";
  }
}

async function getServiceCategory(serviceId) {
  try {
    let url = `${baseUrl}reservas/controller/reservaController.php`;
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
    calendar_days_available: calendarDaysAvailable,
    company_id: companyId,
  };

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
          dateFormat: "Y-m-d",
          minDate: "today",
          maxDate: new Date().fp_incr(calendarDaysAvailable), // Puedes ajustar este valor según necesites
          enable: [
            function (date) {
              return availableDates.includes(date.toISOString().split("T")[0]);
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
          let availableTimesOption = '<option value="">Selecciona una hora</option>';
          available_times.forEach((time) => {
            availableTimesOption += `<option value="${time.start} - ${time.end}">${time.start} - ${time.end}</option>`;
          });
          timeInput.innerHTML = availableTimesOption;
        } else {
          timeInput.innerHTML = '<option value="">No hay horas disponibles</option>';
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
    sendAppointment(formData);
  }
});

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
    var modalBody = document.querySelector(".modal-body");
    modalBody.innerText = message;
    var modal = new bootstrap.Modal(document.getElementById("responseModal"));
    modal.show();

    var acceptButton = document.getElementById("acceptButton");
    acceptButton.addEventListener("click", function () {
      // if (message === "Cita reservada exitosamente y correo enviado!") {
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
