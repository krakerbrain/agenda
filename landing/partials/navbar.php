  <!-- Navbar Superior - Versión Desktop -->
  <nav id="navbar" class="transition-all duration-500 ease-in-out fixed top-0 left-0 right-0 shadow-none z-50 pt-4">
      <div class="container mx-auto px-4">
          <!-- Navbar Container -->
          <div class="flex justify-between items-center h-16 w-full">

              <!-- Logo + Nombre: oculto en móviles -->
              <a href="<?php echo $baseUrl; ?>">
                  <div class="md:flex items-center space-x-3 md:mb-6 md:mb-0">
                      <img src="<?php echo $baseUrl; ?>assets/img/landing/logo/Isotipo-Agendarium.svg"
                          alt="Logo Agendarium" class="h-10 w-auto">
                      <div class="hidden md:block">
                          <span class="text-xl font-semibold text-[#1B637F]">Agendarium</span>
                          <p class="text-sm text-[#1C4175]/80 text-[#1B637F]">Gestión de citas simplificada</p>
                      </div>
                  </div>
              </a>

              <!-- Menú de Navegación: solo visible en md+ -->
              <?php if ($section == "home") : ?>
                  <div class="hidden md:flex space-x-8">
                      <a href="#how"
                          class="nav-hover-effect text-[#28809C]  hover:text-[#28809C] font-semibold text-sm border-b-2 border-transparent hover:border-[#28809C] transition-colors"
                          style="text-shadow: 1px 1px #ffffffd9;">Proceso
                          Completo</a>
                      <a href="#functions"
                          class="text-[#28809C] hover:text-[#28809C] font-semibold text-sm border-b-2 border-transparent hover:border-[#28809C] transition-colors"
                          style="text-shadow: 1px 1px #ffffffd9;">Funciones
                          Clave</a>
                      <a href="#demo"
                          class="text-[#28809C] hover:text-[#28809C] font-semibold text-sm border-b-2 border-transparent hover:border-[#28809C] transition-colors"
                          style="text-shadow: 1px 1px #ffffffd9;">Demostración</a>
                      <a href="#pricing"
                          class="text-[#28809C] hover:text-[#28809C] font-semibold text-sm border-b-2 border-transparent hover:border-[#28809C] transition-colors"
                          style="text-shadow: 1px 1px #ffffffd9;">Precios</a>
                  </div>
                  <!-- Login button: siempre visible -->
                  <div class="flex space-x-4 items-center justify-end sm:justify-start text-sm">
                      <!-- Contacto -->
                      <a href="<?php echo $baseUrl; ?>landing/contacto/contacto.php"
                          class="flex items-center text-[#28809C] hover:text-[#2B819F] font-medium transition-colors"
                          style="text-shadow: 1px 1px #ffffffd9;">
                          <span class="material-icons mr-1 text-lg">mail_outline</span>
                          <span class="hidden sm:inline font-semibold">Contacto</span>
                      </a>

                      <!-- Login -->
                      <a href="<?php echo $baseUrl; ?>login/index.php"
                          class="flex items-center text-[#28809C] hover:text-[#2B819F] font-medium transition-colors"
                          style="text-shadow: 1px 1px #ffffffd9;">
                          <span class="material-icons mr-1 text-lg">login</span>
                          <span class="hidden sm:inline font-semibold">Login</span>
                      </a>
                  </div>
              <?php endif; ?>

          </div>

      </div>
  </nav>

  <!-- Navbar Inferior - Versión Mobile -->
  <nav class="fixed bottom-0 left-0 right-0 bg-white shadow-lg z-50 md:hidden border-t border-[#249373]/10">
      <div class="flex justify-around py-2">
          <a href="#home" class="flex flex-col items-center text-xs text-[#1B637F] px-4 py-1 nav-hover-effect">
              <span class="material-icons">home</span>
              <span>Inicio</span>
          </a>
          <a href="#functions" class="flex flex-col items-center text-xs text-gray-600 px-4 py-1">
              <span class="material-icons">widgets</span>
              <span>Funciones</span>
          </a>
          <a href="#demo" class="flex flex-col items-center text-xs text-gray-600 px-4 py-1">
              <span class="material-icons">play_circle</span>
              <span>Demo</span>
          </a>
          <a href="#pricing" class="flex flex-col items-center text-xs text-gray-600 px-4 py-1">
              <span class="material-icons">attach_money</span>
              <span>Precios</span>
          </a>
      </div>
  </nav>


  <!-- Espacio para evitar que el contenido quede detrás del navbar -->
  <!-- <div class="h-16"></div> -->