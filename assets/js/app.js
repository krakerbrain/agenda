let showMoreInfo = false;

document.querySelectorAll(".nav-item").forEach((item) => {
  item.addEventListener("click", function () {
    // Remover la clase 'active' de todos los elementos
    document.querySelectorAll(".nav-item").forEach((el) => el.classList.remove("active"));
    // Agregar la clase 'active' al elemento que se clickeó
    this.classList.add("active");
    if (showMoreInfo) {
      hideInfo();
    }
  });
});
// Scroll horizontal con GSAP
const sections = document.querySelectorAll(".section");
gsap.registerPlugin(ScrollTrigger, ScrollToPlugin);

let panelsSection = document.querySelector(".sections-container"),
  panels = document.querySelectorAll(".section"),
  tween;

// Definir el flag para controlar el scroll
let scrollAllowed = true;

document.querySelectorAll(".scroll-nav").forEach((anchor) => {
  anchor.addEventListener("click", function (e) {
    e.preventDefault();
    let targetElem = document.querySelector(e.currentTarget.getAttribute("href"));
    // Calcula el total del scroll disponible y el movimiento total
    let totalScroll = tween.scrollTrigger.end - tween.scrollTrigger.start,
      totalMovement = (panels.length - 1) * targetElem.offsetWidth;
    // Calcula la nueva posición del scroll basada en la posición del elemento objetivo
    let y = Math.round(tween.scrollTrigger.start + (targetElem.offsetLeft / totalMovement) * totalScroll);

    // Realiza el desplazamiento
    gsap.to(window, {
      // Se usa `y` como en el código de GSAP
      scrollTo: {
        y: y,
        autoKill: false,
      },
      duration: 1,
    });
  });
});

tween = gsap.to(".sections-container", {
  xPercent: -100 * (sections.length - 1),
  ease: "none",
  scrollTrigger: {
    trigger: ".sections-container",
    pin: true,
    scrub: 1,
    snap: {
      snapTo: 1 / (sections.length - 1),
      inertia: false,
      duration: {
        min: 0.1,
        max: 0.1,
      },
    },
    end: () => "+=" + document.querySelector(".section").offsetWidth * sections.length,
    onUpdate: updateNavbarActiveState, // Ejecutar cada vez que haya una actualización
    onScrubComplete: updateNavbarActiveState,
  },
});

// Verificar el estado de visibilidad de las secciones y activar la clase 'active'
function updateNavbarActiveState() {
  sections.forEach((section, index) => {
    if (ScrollTrigger.isInViewport(section, 0.3, true)) {
      // Si al menos el 50% de la sección está visible en el viewport
      document.querySelectorAll(".nav-item").forEach((el) => el.classList.remove("active"));
      document.querySelectorAll(".nav-item")[index].classList.add("active");
    }
  });
}

document.getElementById("show-more-info").addEventListener("click", function () {
  // Giro para mostrar la información adicional
  gsap.to(".flip-card", {
    duration: 0.4,
    rotationY: 180,
  });
  // Activar el scroll horizontal
  scrollAllowed = false;
  showMoreInfo = true;
  document.querySelector(".flip-container").classList.add("flip-container-background");
});

document.getElementById("show-less-info").addEventListener("click", hideInfo);

function hideInfo() {
  document.querySelector(".flip-container").classList.remove("flip-container-background");
  // Giro para volver a la vista original
  gsap.to(".flip-card", {
    duration: 0.4,
    rotationY: 0,
  });
  // Activar el scroll horizontal
  scrollAllowed = true;
  showMoreInfo = false;
}

const modal = document.querySelector("#inscriptionModal");

// Controlar el scroll horizontal basado en el flag
window.addEventListener(
  "wheel",
  function (e) {
    const isModalOpen = modal && modal.classList.contains("show");
    if (!scrollAllowed && !isModalOpen) {
      e.preventDefault();
    }
  },
  {
    passive: false,
  }
);

window.addEventListener(
  "touchmove",
  function (e) {
    const isModalOpen = modal && modal.classList.contains("show");
    if (!scrollAllowed && !isModalOpen) {
      e.preventDefault();
    }
  },
  {
    passive: false,
  }
);

// Llamar la función inicialmente en caso de que una sección ya esté visible
updateNavbarActiveState();

document.querySelector("#companyForm").addEventListener("submit", async function (e) {
  e.preventDefault(); // Evitar que el formulario se envíe de manera tradicional
  debugger;
  const formData = new FormData(this); // Recoger los datos del formulario

  try {
    const response = await fetch(`${baseUrl}inscripcion/controller/procesar_inscripcion.php`, {
      method: "POST",
      body: formData,
    });

    const { success, message, error, debug } = await response.json(); // Convertir la respuesta a JSON

    if (success) {
      // Obtener el modal de inscripción
      let inscriptionModalEl = document.getElementById("inscriptionModal");
      let inscriptionModal = bootstrap.Modal.getInstance(inscriptionModalEl); // Obtener la instancia actual del modal

      if (inscriptionModal) {
        // Si la instancia existe (es decir, el modal está abierto), ocultarlo
        inscriptionModal.hide();
      }

      // Mostrar el mensaje en el modal de respuesta
      const responseMessage = document.getElementById("responseMessage");
      responseMessage.innerText = message;

      // Mostrar el modal de respuesta
      const responseModal = new bootstrap.Modal(document.getElementById("responseModal"));
      responseModal.show();
    } else {
      // Mostrar el mensaje de error en el modal de respuesta
      const responseMessage = document.getElementById("responseMessage");
      responseMessage.innerText = message == null ? error : message;

      // Mostrar el modal de respuesta
      const responseModal = new bootstrap.Modal(document.getElementById("responseModal"));
      responseModal.show();
    }
  } catch (error) {
    console.error("Error:", error);
    const responseMessage = document.getElementById("responseMessage");
    responseMessage.innerText = "Hubo un error al procesar la solicitud.";

    const responseModal = new bootstrap.Modal(document.getElementById("responseModal"));
    responseModal.show();
  }
});
