const form = document.getElementById("eventRegistrationForm");
if (form) {
  form.addEventListener("submit", async function (event) {
    event.preventDefault();

    const formData = new FormData(form);

    try {
      const response = await fetch(`${baseUrl}eventos/controller/inscripcion.php`, {
        method: "POST",
        body: formData,
      });

      const result = await response.json();

      if (result.success) {
        // Muestra un mensaje de éxito al usuario
        alert("Inscripción exitosa.");
        // Limpia el formulario o realiza alguna acción adicional si es necesario
        location.reload();
      } else {
        // Muestra un mensaje de error al usuario
        alert(`Error: ${result.message}`);
      }
    } catch (error) {
      // Manejo de errores en la solicitud
      console.error("Error al enviar el formulario:", error);
      alert("Ocurrió un error inesperado. Intenta nuevamente.");
    }
  });

  const registerButtons = document.querySelectorAll(".open-registration-form");

  // Añadir el evento de clic a cada botón
  registerButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const eventId = this.getAttribute("data-event-id");
      const eventName = this.closest(".card").querySelector(".card-title").textContent; // Obtener el nombre del curso

      // Mostrar el formulario de inscripción
      document.getElementById("registrationFormContainer").style.display = "block";
      document.getElementById("selected_event_id").value = eventId; // Establecer el evento seleccionado en el campo oculto
      document.querySelector(".curso").textContent = eventName; // Mostrar el nombre del curso

      // Ocultar los eventos
      document.querySelector(".events-container").style.display = "none";

      // Desplegar el formulario, si ya está visible no hará nada
      window.scrollTo({
        top: document.getElementById("registrationFormContainer").offsetTop,
        behavior: "smooth",
      });
    });
  });
}
