document.addEventListener("DOMContentLoaded", function () {
  const driver = window.driver.js.driver;
  let currentDriver;

  // Función para limpiar completamente el driver
  function resetDriver() {
    if (currentDriver) {
      currentDriver.destroy();
      // Limpiar cualquier elemento creado dinámicamente
      document.querySelector(".driver-popover")?.remove();
      document.querySelector(".driver-overlay")?.remove();
    }
  }

  function endScheduleTour() {
    currentDriver = driver({
      showProgress: true,
      animate: true,
      steps: [
        {
          element: "#workScheduleForm",
          popover: {
            side: "top",
            title: "Horario Listo",
            description: "Ahora todos los días estan configurados. Si más adelante quieres cambiar alguno, puedes hacerlo desde aquí.",
          },
        },
        {
          element: ".goToServicesBtn",
          popover: {
            title: "Guardar y continuar",
            description: "HAZ CLICK AQUI para guardar y pasar a configurar los servicios",
            position: "top",
            onNextClick: () => {
              resetDriver();
              document.getElementById("workScheduleContainer").classList.add("d-none");
              document.getElementById("servicesContainer").classList.remove("d-none");

              // Pequeño delay para asegurar que el DOM está listo
              setTimeout(() => {
                configureServicesTour();
              }, 300);
            },
            onDeselected: () => {
              // Limpieza adicional si es necesario
              console.log("Saliendo del botón guardar");
            },
          },
        },
      ],
    });
    currentDriver.drive();
  }

  // Configuración del tour de servicios
  function configureServicesTour() {
    resetDriver();

    currentDriver = driver({
      showProgress: true,
      animate: true,
      steps: [
        {
          element: "#servicesContainer",
          popover: {
            title: "Configuración de Servicios",
            description: "Ahora se configuran los servicios que ofrece el negocio.",
            side: "top",
            align: "end",
          },
        },
        {
          element: ".service-switch",
          popover: {
            title: "Habilitar/Deshabilitar servicio",
            description: "Habilita o deshabilita un servicio.",
            position: "right",
          },
        },
        {
          element: ".service-name",
          popover: {
            title: "Nombre del servicio",
            description: "Escribe el nombre del servicio. En este ejemplo el servicio es 'Corte de cabello'.",
            position: "right",
          },
        },
        {
          element: ".service-duration",
          popover: {
            title: "Duración del servicio",
            description: "Establece la duración del servicio. En este ejemplo la duración es de 1 hora y 30 minutos.",
            position: "right",
          },
        },
        {
          element: ".service-observations",
          popover: {
            title: "Descripción del servicio",
            description: "Escribe una breve descripción del servicio. En este ejemplo la descripción es 'Corte de cabello para adultos'.",
            position: "right",
          },
        },
        {
          element: ".add-category",
          popover: {
            title: "Agregar categoría",
            description: "Algunos servicios requieren categorías. Por ejemplo, 'Corte Básico' o 'Corte Premium'. HAZ CLICK AQUI para agregar una categoría.",
            position: "right",
          },
        },
        {
          element: ".category-item",
          popover: {
            title: "Categoría agregada",
            description: "Para este ejemplo se ha agregado la Categoría 'Corte Básico', y también la categoría puede llevar una descripción.",
            position: "right",
          },
        },
        {
          element: ".days-container",
          popover: {
            title: "Días de disponibilidad",
            description: "Puedes elegir que días estarán disponibles los servicios. Para este ejemplo seleccionamos todos los días menos el domingo.",
            position: "right",
          },
        },
        {
          element: ".goToBookingForm",
          popover: {
            title: "Guardar y continuar",
            description:
              "Esta es la configuración básica, más adelante podrás agregar los estilos para el formulario de reservas. Pero en este momento ya podrías ver lo que tus clientes verían al reservar.",
            position: "top",
            onNextClick: () => {
              resetDriver();
              document.getElementById("workScheduleContainer").classList.add("d-none");
              document.getElementById("servicesContainer").classList.remove("d-none");

              // Pequeño delay para asegurar que el DOM está listo
              setTimeout(() => {
                // ir a una url específica
                window.location.href = "http://localhost/agenda/reservas/empresa-demo-agendar?isdemo=1";
              }, 300);
            },
            onDeselected: () => {
              // Limpieza adicional si es necesario
              console.log("Saliendo del botón guardar");
            },
          },
        },
      ],
    });
    currentDriver.drive();
  }

  // Función para iniciar el tour de servicios

  // Configuración del tour principal (horarios)
  currentDriver = driver({
    showProgress: true,
    allowClose: false,
    steps: [
      {
        popover: {
          title: "Bienvenido a la Guía de Configuración de Agendarium",
          description: "Aquí podrás familiarizarte con la configuración de la agenda.",
        },
      },
      {
        element: ".form-check-input",
        popover: {
          title: "Activar/Desactivar día",
          description: "Primero activa o desactiva los días de trabajo.",
          position: "right",
        },
      },
      {
        element: ".work-start",
        popover: {
          title: "Hora de inicio",
          description: "Establecemos la hora de inicio de la jornada. En este ejemplo la hora de apertura es a las 9:00.",
          position: "top",
        },
      },
      {
        element: ".work-end",
        popover: {
          title: "Hora de fin",
          description: "Establecemos la hora de fin de la jornada. En este ejemplo la hora de cierre es a las 20:00.",
          position: "top",
        },
      },
      {
        element: ".break-time",
        popover: {
          title: "Agregar descanso",
          description: "Configura periodos de descanso. En este ejemplo se ha añadido un descanso de 1 hora desde las 13:00 a las 14:00 .",
          position: "left",
        },
      },
      {
        element: ".copy-all",
        popover: {
          title: "Copiar horario",
          description: "HAZ CLICK AQUI para copiar este horario a todos los días.",
          position: "left",
        },
      },
    ],
  });

  document.querySelector(".goToServicesBtn").addEventListener("click", function (e) {
    e.preventDefault();
    resetDriver();
    document.getElementById("workScheduleContainer").classList.add("d-none");
    document.getElementById("servicesContainer").classList.remove("d-none");
    setTimeout(configureServicesTour, 300);
  });
  // Iniciar el tour principal
  currentDriver.drive();

  // Opcional: Botón para reiniciar el tour
  const restartBtn = document.createElement("button");
  restartBtn.textContent = "Reiniciar Guía";
  restartBtn.className = "btn btn-info position-fixed";
  restartBtn.style.bottom = "20px";
  restartBtn.style.right = "20px";
  restartBtn.style.zIndex = "1000";
  restartBtn.onclick = () => {
    document.getElementById("servicesContainer").classList.add("d-none");
    document.getElementById("workScheduleContainer").classList.remove("d-none");
    setTimeout(() => mainDriverObj.drive(), 300);
  };
  document.body.appendChild(restartBtn);
});
