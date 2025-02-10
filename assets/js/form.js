document.addEventListener("DOMContentLoaded", function () {
  const dateInput = document.getElementById("date");
  const serviceInput = document.getElementById("service");
  const timeInput = document.getElementById("time");
  const scheduleModeInput = document.getElementById("schedule_mode");
  const companyID = document.getElementById("company_id").value;

  function fetchAvailableTimes() {
    const date = dateInput.value;
    const serviceId = serviceInput.value;
    const scheduleMode = scheduleModeInput.value === "blocks" ? "../get_available_hours_blocks.php" : "../get_available_hours_free.php";

    if (date && serviceId) {
      fetch(scheduleMode, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ date: date, service_id: serviceId, company_id: companyID }),
      })
        .then((response) => response.json())
        .then((data) => {
          console.log(data);
          timeInput.innerHTML = ""; // Clear previous options
          if (data.success) {
            if (data.available_times.length > 0) {
              timeInput.innerHTML = '<option value="">Selecciona una hora</option>';
              data.available_times.forEach((time) => {
                const option = document.createElement("option");
                option.value = `${time.start} - ${time.end}`;
                option.textContent = `${time.start} - ${time.end}`;
                timeInput.appendChild(option);
              });
            } else {
              timeInput.innerHTML = '<option value="">No hay horas disponibles</option>';
            }
          } else {
            alert(data.message);
          }
        })
        .catch((error) => console.error("Error:", error));
    }
  }

  dateInput.addEventListener("change", fetchAvailableTimes);
  serviceInput.addEventListener("change", fetchAvailableTimes);

  document.getElementById("appointmentForm").addEventListener("submit", function (event) {
    event.preventDefault();
    const name = document.getElementById("name").value;
    const phone = document.getElementById("phone").value;
    const mail = document.getElementById("mail").value;
    const date = dateInput.value;
    const time = timeInput.value;
    const scheduleMode = document.getElementById("schedule_mode").value;
    const serviceId = serviceInput.value;
    const company_id = document.getElementById("company_id").value;

    if (!name || !phone || !mail || !date || !time || !serviceId) {
      document.getElementById("response").innerText = "Por favor, completa todos los campos.";
      return;
    }

    function formatTime(date) {
      return date.toTimeString().split(" ")[0].substring(0, 5);
    }

    if (scheduleMode === "blocks") {
      const [startTime, endTime] = time.split(" - ");

      const appointment = {
        name: name,
        phone: phone,
        mail: mail,
        date: date,
        start_time: formatTime(new Date(`${date}T${startTime}`)),
        end_time: formatTime(new Date(`${date}T${endTime}`)),
        id_service: serviceId,
        company_id: company_id,
      };

      sendAppointment(appointment);
    } else {
      fetch("get_service_duration.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ service_id: serviceId }),
      })
        .then((response) => response.json())
        .then((data) => {
          const duration = data.duration; // Duración en horas
          const startTime = new Date(`${date}T${time}`);
          const endTime = new Date(startTime);
          endTime.setHours(endTime.getHours() + duration);

          const appointment = {
            name: name,
            phone: phone,
            mail: mail,
            date: date,
            start_time: formatTime(startTime),
            end_time: formatTime(endTime),
            id_service: serviceId,
            company_id: company_id,
          };

          sendAppointment(appointment);
        })
        .catch((error) => {
          document.getElementById("response").innerText = "Error al obtener la duración del servicio.";
          console.error("Error:", error);
        });
    }
  });

  function sendAppointment(appointment) {
    fetch("../../process.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(appointment),
    })
      .then((response) => {
        if (!response.ok) {
          return response.json().then((error) => {
            throw new Error(error.message);
          });
        }
        return response.json();
      })
      .then((data) => {
        document.getElementById("response").innerText = data.message;
        if (data.message === "Cita reservada exitosamente!") {
          document.getElementById("appointmentForm").reset();
        }
      })
      .catch((error) => {
        document.getElementById("response").innerText = "Error al reservar la cita.";
        console.error("Error:", error);
      });
  }
});
