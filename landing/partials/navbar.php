  <!-- Navbar Superior - Versión Desktop -->
  <nav id="navbar" class="transition-all duration-500 ease-in-out fixed top-0 left-0 right-0 shadow-none z-50 pt-4">
      <div class="container mx-auto px-4">
          <!-- Navbar Container -->
          <div class="flex justify-between items-center h-16 w-full">

              <!-- Logo + Nombre: oculto en móviles -->
              <div class="hidden md:flex items-end">
                  <div class="w-10">
                      <img src="<?php echo $baseUrl; ?>assets/img/landing/logo/Isotipo-Agendarium.svg"
                          alt="Logo Agendarium" class="h-16 w-auto mr-3">
                  </div>
                  <a href="<?php echo $baseUrl; ?>"
                      class="text-xl font-bold text-[#1c4175] hover:text-[#2B819F] transition-colors mb-2 ml-2">
                      Agendarium
                  </a>
              </div>

              <!-- Menú de Navegación: solo visible en md+ -->
              <?php if ($section == "home") : ?>
                  <div class="hidden md:flex space-x-8">
                      <a href="#how"
                          class="nav-hover-effect text-gray-600 hover:text-[#1c4175] font-semibold border-b-2 border-transparent hover:border-[#1c4175] transition-colors"
                          style="text-shadow: 1px 1px 2px rgba(255,255,255,0.4);">Proceso Completo</a>
                      <a href="#functions"
                          class="text-gray-600 hover:text-[#1c4175] font-semibold border-b-2 border-transparent hover:border-[#1c4175] transition-colors"
                          style="text-shadow: 1px 1px 2px rgba(255,255,255,0.4);">Funciones Clave</a>
                      <a href="#demo"
                          class="text-gray-600 hover:text-[#1c4175] font-semibold border-b-2 border-transparent hover:border-[#1c4175] transition-colors"
                          style="text-shadow: 1px 1px 2px rgba(255,255,255,0.4);">Demostración</a>
                      <a href="#pricing"
                          class="text-gray-600 hover:text-[#1c4175] font-semibold border-b-2 border-transparent hover:border-[#1c4175] transition-colors"
                          style="text-shadow: 1px 1px 2px rgba(255,255,255,0.4);">Precios</a>
                  </div>
                  <!-- Login button: siempre visible -->
                  <a href="<?php echo $baseUrl; ?>login/index.php"
                      class="flex items-center text-[#1c4175] hover:text-[#2B819F] font-medium transition-colors"
                      style="text-shadow: 1px 1px 2px rgba(255,255,255,0.4);">
                      <span class=" sm:inline mr-1 font-semibold">Login</span>
                      <span class="material-icons">login</span>
                  </a>
              <?php endif; ?>
          </div>

      </div>
  </nav>
  <?php if ($section == "home") : ?>
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
  <?php endif; ?>

  <!-- Espacio para evitar que el contenido quede detrás del navbar -->
  <!-- <div class="h-16"></div> -->