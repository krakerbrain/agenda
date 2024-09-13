document.querySelectorAll(".nav-item").forEach((item) => {
  item.addEventListener("click", function () {
    // Remover la clase 'active' de todos los elementos
    document.querySelectorAll(".nav-item").forEach((el) => el.classList.remove("active"));
    // Agregar la clase 'active' al elemento que se clickeó
    this.classList.add("active");
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
}

// Controlar el scroll horizontal basado en el flag
window.addEventListener(
  "wheel",
  function (e) {
    if (!scrollAllowed) {
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
    if (!scrollAllowed) {
      e.preventDefault();
    }
  },
  {
    passive: false,
  }
);

// Llamar la función inicialmente en caso de que una sección ya esté visible
updateNavbarActiveState();