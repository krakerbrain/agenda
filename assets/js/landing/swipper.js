// landing.js
document.addEventListener("DOMContentLoaded", function () {
  initSwiper();
  setupSmoothScroll();
  setupActiveSectionDetection();
});

function initSwiper() {
  const howItWorksSlider = new Swiper(".howItWorks-slider", {
    effect: "fade",
    speed: 800,
    pagination: {
      el: ".swiper-pagination",
      clickable: true,
    },
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
  });
}

function setupSmoothScroll() {
  document.querySelectorAll('a[href*="#"]').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      e.preventDefault();
      const href = this.getAttribute("href");
      // Extrae solo el #id (ej: "index.html#how" -> "#how")
      const targetId = href.includes("#") ? "#" + href.split("#")[1] : href;
      const target = document.querySelector(targetId);
      if (!target) return;

      const navbarHeight = document.querySelector("nav").offsetHeight;
      const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - navbarHeight;

      // Desactivar scroll suave temporalmente
      document.documentElement.style.scrollBehavior = "auto";

      // Scroll preciso
      window.scrollTo({
        top: targetPosition,
        behavior: "instant",
      });

      // Aplicar animación
      target.classList.add("smooth-section");

      // Limpiar animación después de completar
      setTimeout(() => {
        target.classList.remove("smooth-section");
        document.documentElement.style.scrollBehavior = "smooth";
      }, 800);

      // Actualizar navegación activa
      document.querySelectorAll('[href^="#"]').forEach((link) => {
        link.classList.remove("active", "text-[#1B637F]", "border-[#1B637F]");
      });
      this.classList.add("active", "text-[#1B637F]", "border-[#1B637F]");
    });
  });
}

function setupActiveSectionDetection() {
  window.addEventListener("scroll", () => {
    const sections = document.querySelectorAll("section");
    const navHeight = document.querySelector("nav").offsetHeight;
    let current = "";

    sections.forEach((section) => {
      const sectionTop = section.offsetTop - navHeight - 100;
      if (window.scrollY >= sectionTop) {
        current = "#" + section.id;
      }
    });

    document.querySelectorAll('[href^="#"]').forEach((link) => {
      link.classList.remove("active", "text-[#1B637F]", "border-[#1B637F]");
      if (link.getAttribute("href") === current) {
        link.classList.add("active", "text-[#1B637F]", "border-[#1B637F]");
      }
    });
  });
}
