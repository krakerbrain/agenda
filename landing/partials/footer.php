<footer class="bg-darkbg text-white py-8">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row justify-between items-center">
            <div class="mb-4 md:mb-0">
                <a href="#" class="text-2xl font-bold text-white">Agendarium</a>
                <p class="text-gray-400 mt-1">Gestión de citas simplificada</p>
            </div>
            <div class="flex space-x-6">
                <a href="#" class="text-gray-400 hover:text-white transition-colors">Términos</a>
                <a href="#" class="text-gray-400 hover:text-white transition-colors">Privacidad</a>
                <a href="#" class="text-gray-400 hover:text-white transition-colors">Contacto</a>
            </div>
        </div>
        <div class="border-t border-gray-700 mt-6 pt-6 text-center text-gray-400 text-sm">
            &copy; <?php echo date('Y'); ?> Agendarium. Todos los derechos reservados.
        </div>
    </div>
</footer>
<script>
const baseUrl = "<?php echo $baseUrl; ?>";
const hero = document.getElementById('home');
const title1 = document.getElementById('hero-title-1');
const title2 = document.getElementById('hero-title-2');
const subtitle = document.getElementById('hero-subtitle');

const slides = [{
        background: "url('assets/img/landing/hero_section_2.jpg')",
        title1: "Controla tu agenda",
        title2: "como un profesional",
        subtitle: "La herramienta de gestión de citas que necesitas para organizar tu tiempo de manera eficiente."
    },
    {
        background: "url('assets/img/landing/hero_section_3.jpg')",
        title1: "Haz crecer tu negocio",
        title2: "gestionando tus citas",
        subtitle: "Tus clientes pueden reservar online sin llamadas ni mensajes."
    },
    {
        background: "url('assets/img/landing/hero_section.png')",
        title1: "Tu agenda, tu control",
        title2: "sin complicaciones",
        subtitle: "Agendarium trabaja por ti mientras tú te concentras en tu servicio."
    }
];

let index = 0; // Esta ya está en CSS

setInterval(() => {
    index = (index + 1) % slides.length;
    const current = slides[index];

    // Fade out texto
    [title1, title2, subtitle].forEach(el => {
        el.classList.remove('fade-in');
        el.classList.add('fade-out');
    });

    // Imagen fade-in en ::after
    hero.style.setProperty('--next-bg', current.background);
    hero.classList.add('image-fading');

    // Cambiar texto
    setTimeout(() => {
        title1.textContent = current.title1;
        title2.textContent = current.title2;
        subtitle.textContent = current.subtitle;

        [title1, title2, subtitle].forEach(el => {
            el.classList.remove('fade-out');
            el.classList.add('fade-in');
        });
    }, 800);

    // Al terminar el fade, reemplazamos imagen base y ocultamos ::after
    setTimeout(() => {
        hero.style.backgroundImage = current.background;
        hero.classList.remove('image-fading');
    }, 1800);
}, 6000);

const navbar = document.getElementById('navbar');

window.addEventListener('scroll', () => {
    if (window.scrollY > 10) {
        navbar.classList.add('bg-white', 'shadow-md');
        navbar.classList.remove('bg-transparent');
    } else {
        navbar.classList.remove('bg-white', 'shadow-md');
        navbar.classList.add('bg-transparent');
    }
});

const btnDemo = document.getElementById('btn-demo');
const modal = document.getElementById('video-modal');
const closeBtn = document.getElementById('close-modal');
const video = document.getElementById('modal-video');

btnDemo.addEventListener('click', function(e) {
    e.preventDefault();
    modal.classList.remove('hidden');
    video.currentTime = 0;
    setTimeout(() => {
        video.play();
    }, 100);
});

closeBtn.addEventListener('click', function() {
    modal.classList.add('hidden');
    video.pause();
});

// Cerrar al hacer clic fuera del video
modal.addEventListener('click', function(e) {
    if (e.target === modal) {
        modal.classList.add('hidden');
        video.pause();
    }
});
</script>