import { BaseImageManager } from "./BaseImageManager.js";
import { ImageCropper } from "./ImageCropper.js";

export class BannerManager extends BaseImageManager {
  constructor({ baseUrl, uploader }) {
    super({
      baseUrl,
      uploader,
      config: {
        elements: {
          dropzone: document.getElementById("bannerDropzone"),
          input: document.getElementById("banner"),
          preview: document.getElementById("current-banner"),
          hiddenInput: document.getElementById("bannerUrl"),
          editorContainer: document.getElementById("banner-editor-container"),
          saveButton: document.getElementById("save-custom-banner"),
          imageToCrop: document.getElementById("image-to-crop"),
        },
        defaultImage: `${baseUrl}assets/img/default-banner.png`,
      },
    });

    this.cropper = new ImageCropper(this.elements.imageToCrop, {
      aspectRatio: 600 / 150,
      viewMode: 1,
      autoCropArea: 1,
      movable: true,
      rotatable: false,
      scalable: false,
      zoomable: true,
      minCropBoxWidth: 600,
      minCropBoxHeight: 150,
      maxCropBoxWidth: 600,
      maxCropBoxHeight: 150,
    });

    this.setupDefaultBanners();
    this.elements.saveButton.addEventListener("click", () => this.saveBanner());
  }

  async handleFileSelect(file) {
    this.handleBannerUpload(file);
  }

  setupDefaultBanners() {
    document.querySelectorAll('input[name="selected-banner"]').forEach((radio) => {
      radio.addEventListener("change", () => {
        if (radio.value !== "custom") {
          this.elements.preview.src = `${this.baseUrl}assets/img/banners/${radio.value}`;
          this.elements.hiddenInput.value = `assets/img/banners/${radio.value}`;
        }
      });
    });
  }

  handleBannerUpload(file) {
    const reader = new FileReader();
    reader.onload = (e) => {
      this.showEditor(e.target.result);
    };
    reader.readAsDataURL(file);
  }

  showEditor(imageSrc) {
    this.elements.editorContainer.classList.remove("hidden");
    this.elements.imageToCrop.src = imageSrc;
    this.cropper.init(imageSrc);
  }

  async saveBanner() {
    this.cropper.getBlob(async (blob) => {
      const formData = new FormData();

      // 1. Agregar el blob como archivo
      const file = new File([blob], `banner_${Date.now()}.png`, { type: "image/png" });

      // 2. Crear el objeto de additionalData
      const additionalData = {
        tipo: "banner",
        companyId: document.getElementById("companyId").value,
      };

      try {
        // 3. Usar el uploader con la firma correcta
        const result = await this.uploader.upload(file, { additionalData });

        if (result.success) {
          this.elements.preview.src = `${this.baseUrl}${result.imageUrl}`;
          this.elements.hiddenInput.value = result.imageUrl;
          this.elements.editorContainer.classList.add("hidden");

          // Actualizar el radio button custom
          const customRadio = document.getElementById("banner-custom");
          if (customRadio) {
            customRadio.checked = true;
            customRadio.value = result.fileName;
          }

          this.showNotification("Banner personalizado guardado correctamente", "success");
        } else {
          this.showNotification(result.error || "Error al guardar el banner", "error");
        }
      } catch (error) {
        console.error("Error:", error);
        this.showNotification("Error al conectar con el servidor", "error");
      }
    });
  }

  showNotification(message, type = "info") {
    alert(message); // Reemplazar con tu sistema de notificaciones
    // Implementación básica - puedes reemplazar con tu sistema de notificaciones
    //   const notification = document.createElement("div");
    //   notification.className = `fixed top-4 right-4 p-4 rounded-md shadow-lg ${
    //     type === "error" ? "bg-red-100 text-red-800" : type === "success" ? "bg-green-100 text-green-800" : "bg-blue-100 text-blue-800"
    //   }`;
    //   notification.textContent = message;
    //   document.body.appendChild(notification);

    //   setTimeout(() => {
    //     notification.remove();
    //   }, 5000);
    // }
  }
}
