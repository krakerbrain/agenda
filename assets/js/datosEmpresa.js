import { FileUploader } from "./datosEmpresa/FileUploader.js";
import { CompanyDataManager } from "./datosEmpresa/CompanyDataManager.js";
import { SocialMediaManager } from "./datosEmpresa/SocialMediaManager.js";
import { BannerManager } from "./datosEmpresa/BannerManager.js";
import { LogoManager } from "./datosEmpresa/LogoManager.js";
import { ModalManager } from "./config/ModalManager.js";
// Si decides implementarlo más adelante:
// import { ProfilePictureManager } from "./datosEmpresa/ProfilePictureManager.js";

export function init() {
  // 1. Inicialización del Uploader (sin cambios)
  const uploader = new FileUploader(`${baseUrl}user_admin/controllers/datosEmpresa/uploadHandler.php`);

  // 2. Inicialización de managers principales
  const companyForm = document.getElementById("datosEmpresaForm");
  const companyManager = new CompanyDataManager(companyForm, uploader);
  const socialManager = new SocialMediaManager(document.getElementById("social-form"));

  // 3. Inicialización de gestores de imágenes (versión mejorada)
  const bannerManager = new BannerManager({
    baseUrl,
    uploader,
    // Configuración específica para banners
    cropConfig: {
      aspectRatio: 600 / 150,
      minDimensions: { width: 600, height: 150 },
    },
  });

  const logoManager = new LogoManager({
    baseUrl,
    uploader,
    // Configuración específica para logos
    validationConfig: {
      maxSizeMB: 1, // Más pequeño que el banner
      allowedTypes: ["image/png"], // Solo PNG para logos
    },
  });

  /* 
   * Ejemplo de cómo se implementaría ProfilePictureManager más adelante:
   *
  const profilePictureManager = new ProfilePictureManager({
    baseUrl,
    uploader,
    userId: currentUserId, // Necesitarías obtener este valor
    cropConfig: {
      aspectRatio: 1, // Cuadrado
      circularCrop: true // Para recorte circular
    }
  });
  */

  // 4. Carga inicial de datos (sin cambios)
  companyManager.load();
  socialManager.load();

  // 5. Evento para guardar empresa (mejorado con manejo de errores)
  companyForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    try {
      const result = await companyManager.save();
      showNotification(result.message || "Datos guardados correctamente", "Éxito");
      companyManager.load();
      socialManager.load();
    } catch (error) {
      showNotification("Error al guardar: " + error.message, "Error");
      console.error("Error al guardar:", error);
    }
  });

  // Función auxiliar para mostrar notificaciones (ejemplo)
  function showNotification(message, title) {
    // Implementar según tu sistema de notificaciones
    // alert(`${type.toUpperCase()}: ${message}`);
    ModalManager.show("infoModal", {
      title: title,
      message: message,
    });
  }
  // Configurar listeners para cerrar los modales
  ModalManager.setupCloseListeners();
}
