document.addEventListener("DOMContentLoaded", function () {
  const driver = window.driver.js.driver;

  // Configuración del tour principal (horarios)
  currentDriver = driver({
    showProgress: true,
    allowClose: false,
    steps: [
      {
        element: ".company-card",
        popover: {
          title: "Formulario de Reserva",
          description:
            "Luego de configurar el horario y los servicios podrás configurar (desde tu menú de Administrador) los estilos de tu formulario. Podrás personalizarlo agregando un banner, tu logo, y los datos de tu negocio y redes sociales.",
          side: "top",
          align: "end",
        },
      },
      {
        element: "#appointmentForm",
        popover: {
          title: "Escoge el servicio",
          description:
            "Tu cliente podrá ver la lista de servicios que ofreces y la descripción de cada uno. En este caso podemos ver el servicio que configuramos 'Corte de cabello'. Seleccionalo y haz click en siguiente",
          side: "top",
          align: "end",
        },
      },
      {
        element: "#appointmentForm",
        popover: {
          title: "Escoge la categoría",
          description:
            "Al seleccionar el servicio, se muestra la descripción que configuramos y si configuraste una categoría se mostrará el selector de la misma. Selecciona la categoría y haz click en siguiente.",
          side: "top",
          align: "start",
        },
      },
    ],
  });
  currentDriver.drive();
});
