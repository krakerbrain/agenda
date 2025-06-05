const hero = document.getElementById("home");
const title1 = document.getElementById("hero-title-1");
const title2 = document.getElementById("hero-title-2");
const subtitle = document.getElementById("hero-subtitle");
if (hero) {
  const slides = [
    {
      background: "url('assets/img/landing/hero_section_2.jpg')",
      title1: "Controla tu agenda",
      title2: "como un profesional",
      subtitle: "La herramienta de gestión de citas que necesitas para organizar tu tiempo de manera eficiente.",
    },
    {
      background: "url('assets/img/landing/hero_section_3.jpg')",
      title1: "Haz crecer tu negocio",
      title2: "gestionando tus citas",
      subtitle: "Tus clientes pueden reservar online sin llamadas ni mensajes.",
    },
    {
      background: "url('assets/img/landing/hero_section.jpg')",
      title1: "Tu agenda, tu control",
      title2: "sin complicaciones",
      subtitle: "Agendarium trabaja por ti mientras tú te concentras en tu servicio.",
    },
  ];

  let index = 0; // Esta ya está en CSS

  setInterval(() => {
    index = (index + 1) % slides.length;
    const current = slides[index];

    // Fade out texto
    [title1, title2, subtitle].forEach((el) => {
      el.classList.remove("fade-in");
      el.classList.add("fade-out");
    });

    // Imagen fade-in en ::after
    hero.style.setProperty("--next-bg", handlebackGroundGradient(current.background));
    hero.classList.add("image-fading");

    // Cambiar texto
    setTimeout(() => {
      title1.textContent = current.title1;
      title2.textContent = current.title2;
      subtitle.textContent = current.subtitle;

      [title1, title2, subtitle].forEach((el) => {
        el.classList.remove("fade-out");
        el.classList.add("fade-in");
      });
    }, 800);

    // Al terminar el fade, reemplazamos imagen base y ocultamos ::after
    setTimeout(() => {
      hero.style.backgroundImage = handlebackGroundGradient(current.background);
      hero.classList.remove("image-fading");
    }, 1800);
  }, 6000);

  function handlebackGroundGradient(background) {
    let percent = window.innerWidth < 768 ? "90%" : "70%";
    return `linear-gradient(to right, rgba(255, 255, 255) 0%, rgba(255, 255, 255, 0) ${percent}), ${background}`;
  }
}
const navbar = document.getElementById("navbar");

window.addEventListener("scroll", () => {
  if (window.scrollY > 10) {
    navbar.classList.add("bg-white", "shadow-md");
    navbar.classList.remove("bg-transparent");
  } else {
    navbar.classList.remove("bg-white", "shadow-md");
    navbar.classList.add("bg-transparent");
  }
});

const btnDemo = document.querySelectorAll(".btn-demo");
const modalContainer = document.getElementById("modal-container");

let modalOpen = false;
let modalVideo;

// Función que genera el HTML del modal con el video correspondiente
function getModalTemplate(videoSrc) {
  return `
        <div id="video-modal" class="fixed inset-0 bg-black opacity-50 z-50 flex items-center justify-center p-2 md:p-4">
            <div class="relative w-full h-full max-w-6xl">
                <div class="absolute inset-0 flex items-center justify-center">
                    <video controls class="w-full h-full object-contain" id="modal-video" playsinline>
                        <source src="${videoSrc}" type="video/mp4">
                        Tu navegador no soporta el video.
                    </video>
                </div>
                <button id="close-modal" class="absolute top-2 right-2 md:-top-3 md:-right-3 bg-white rounded-full w-10 h-10 flex items-center justify-center shadow-lg hover:bg-gray-100 z-50 border border-gray-200">
                    <span class="text-2xl font-bold text-gray-600 hover:text-black">×</span>
                </button>
            </div>
        </div>
    `;
}

// Abrir el modal y cargar el video correcto
btnDemo.forEach(function (btn) {
  btn.addEventListener("click", function () {
    if (!modalOpen) {
      const isMobile = window.innerWidth < 768;
      const videoSrc = isMobile ? `${baseUrl}assets/videos/Agendarium-Mobile.mp4` : `${baseUrl}assets/videos/Agendarium-Web.mp4`;

      modalContainer.innerHTML = getModalTemplate(videoSrc);
      modalOpen = true;

      modalVideo = document.getElementById("modal-video");
      const closeModal = document.getElementById("close-modal");

      closeModal.addEventListener("click", function (e) {
        e.stopPropagation();
        closeVideoModal();
      });

      modalVideo.currentTime = 0;
      modalVideo.play().catch((e) => console.log("Autoplay bloqueado"));

      setupFullscreenBehavior(); // Solo maneja padding para móviles
    }
  });
});
// Cerrar el modal y limpiar el estado
function closeVideoModal() {
  if (modalOpen && modalVideo) {
    if (document.fullscreenElement) {
      document.exitFullscreen().catch((e) => console.log(e));
    }

    modalVideo.pause();
    modalVideo.currentTime = 0;
    modalContainer.innerHTML = "";
    modalOpen = false;
  }
}

// Ajustes básicos para móviles (p-0)
function setupFullscreenBehavior() {
  const videoModal = document.getElementById("video-modal");
  if (!videoModal) return;

  function handleResize() {
    if (window.innerWidth < 768) {
      videoModal.classList.add("p-0");
    } else {
      videoModal.classList.remove("p-0");
    }
  }

  window.addEventListener("resize", handleResize);
  handleResize();
}

const mensaje = document.getElementById("success-message");
if (mensaje) {
  mensaje.classList.add("opacity-100");
  mensaje.classList.replace("bg-green-200", "bg-green-100");
  setTimeout(() => {
    mensaje.classList.remove("opacity-100");
    mensaje.classList.add("opacity-0");
  }, 4000); // dura visible 4 segundos
}
// carga dinamica de video
const video = document.getElementById("demoVideo");

if (video) {
  const isMobile = window.matchMedia("(max-width: 768px)").matches;
  if (isMobile) {
    video.innerHTML = `<source src="${baseUrl}assets/videos/Agendarium-Mobile.mp4" type="video/mp4">`;
  } else {
    video.innerHTML = `<source src="${baseUrl}assets/videos/Agendarium-Web.mp4" type="video/mp4">`;
  }

  // Recargar el video después de cambiar el source
  video.load();
}
