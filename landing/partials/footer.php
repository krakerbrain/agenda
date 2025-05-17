<footer class="bg-[#FBC136] text-[#1C4175] py-10">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row justify-between items-center">

            <!-- Logo y descripción -->
            <div class="flex items-center space-x-3 mb-6 md:mb-0">
                <img src="<?php echo $baseUrl; ?>assets/img/landing/logo/Isotipo-Agendarium.svg" alt="Logo Agendarium"
                    class="h-10 w-auto">
                <div>
                    <span class="text-2xl font-bold">Agendarium</span>
                    <p class="text-sm text-[#1C4175]/80">Gestión de citas simplificada</p>
                </div>
            </div>

            <!-- Enlaces rápidos -->
            <div class="flex space-x-6 mb-6 md:mb-0">
                <a href="#" class="text-[#1C4175] hover:underline text-sm">Términos</a>
                <a href="#" class="text-[#1C4175] hover:underline text-sm">Privacidad</a>
                <a href="<?php echo $baseUrl; ?>contacto.php"
                    class="text-[#1C4175] hover:underline text-sm">Contacto</a>
            </div>

            <!-- Redes sociales -->
            <div class="flex space-x-5">
                <a href="https://instagram.com/agendarium" target="_blank" class="hover:text-[#E4405F]"
                    aria-label="Instagram">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="https://www.youtube.com/@agendarium_app" target="_blank" class="hover:text-[#FF0000]"
                    aria-label="YouTube">
                    <i class="fab fa-youtube"></i>
                </a>
                <a href="mailto:soporte@agendarium.com" class="hover:text-[#1B637F]" aria-label="Email">
                    <span class="material-icons">mail_outline</span>
                </a>
            </div>

        </div>

        <!-- Línea inferior -->
        <div class="border-t border-[#1C4175]/20 mt-8 pt-4 text-center text-sm text-[#1C4175]/70">
            &copy; <?php echo date('Y'); ?> Agendarium. Todos los derechos reservados.
        </div>
    </div>
</footer>

<script>
    const baseUrl = "<?php echo $baseUrl; ?>";
</script>