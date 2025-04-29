  <!-- Navbar Superior - Versión Desktop -->
  <nav class="fixed top-0 left-0 right-0 bg-white shadow-sm z-50 border-b border-[#249373]/10">
      <div class="container mx-auto px-4">
          <div class="flex justify-between items-center h-16">

              <!-- Contenedor Logo + Nombre -->
              <div class="flex items-center space-x-3">
                  <!-- ESPACIO PARA LOGO -->
                  <div class="w-10">
                      <!-- Icono temporal (reemplazar por tu logo) -->
                      <img src="<?php echo $baseUrl; ?>assets/img/landing/logo_agendarium.png" alt="Logo Agendarium"
                          class="h-16 w-auto mr-3">
                  </div>

                  <!-- Nombre de la app - Puedes usar texto o incluir logo con texto -->
                  <a href="#" class="text-xl font-bold text-[#1B637F] hover:text-[#2B819F] transition-colors">
                      Agendarium
                  </a>
              </div>

              <!-- Menú de Navegación -->
              <div class="hidden md:flex items-center space-x-8">
                  <a href="#features" class="text-gray-600 hover:text-[#1B637F] font-medium transition-colors">
                      Funciones
                  </a>
                  <a href="#how" class="text-gray-600 hover:text-[#1B637F] font-medium transition-colors">
                      ¿Cómo funciona?
                  </a>
                  <a href="#pricing" class="text-gray-600 hover:text-[#1B637F] font-medium transition-colors">
                      Precios
                  </a>
              </div>

              <!-- Botón Login -->
              <a href="<?php echo $baseUrl; ?>login/index.php"
                  class="flex items-center text-[#1B637F] hover:text-[#2B819F] font-medium transition-colors">
                  <span class="hidden sm:inline mr-1">Iniciar Sesión</span>
                  <span class="material-icons">login</span>
              </a>
          </div>
      </div>
  </nav>

  <!-- Navbar Inferior - Versión Mobile -->
  <nav class="fixed bottom-0 left-0 right-0 bg-white shadow-lg z-50 md:hidden border-t border-[#249373]/10">
      <div class="flex justify-around py-2">
          <a href="#home" class="flex flex-col items-center text-xs text-[#1B637F] px-4 py-1">
              <span class="material-icons">home</span>
              <span>Inicio</span>
          </a>
          <a href="#features" class="flex flex-col items-center text-xs text-gray-600 px-4 py-1">
              <span class="material-icons">widgets</span>
              <span>Funciones</span>
          </a>
          <a href="#pricing" class="flex flex-col items-center text-xs text-gray-600 px-4 py-1">
              <span class="material-icons">attach_money</span>
              <span>Precios</span>
          </a>
      </div>
  </nav>

  <!-- Espacio para evitar que el contenido quede detrás del navbar -->
  <div class="h-16"></div>