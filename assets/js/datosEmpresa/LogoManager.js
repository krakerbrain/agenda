import { BaseImageManager } from "./BaseImageManager.js";
import { ModalManager } from "../config/ModalManager.js"; // ✅ Asegúrate que esta ruta es correcta

export class LogoManager extends BaseImageManager {
  constructor({ baseUrl, uploader }) {
    super({
      baseUrl,
      uploader,
      config: {
        elements: {
          dropzone: document.getElementById("logoDropzone"),
          input: document.getElementById("logo"),
          preview: document.getElementById("preview-logo"),
          hiddenInput: document.getElementById("logoUrl"),
        },
        defaultImage: `${baseUrl}assets/img/no_logo.png`,
      },
    });
  }

  async handleFileSelect(file) {
    const result = await this.uploader.upload(file, {
      additionalData: {
        tipo: "logo",
        companyId: document.getElementById("companyId").value,
      },
    });

    if (!result.success) {
      this.resetToDefault();
      ModalManager.show("infoModal", {
        title: "Error",
        message: result.error || "Hubo un problema al subir el logo.",
      });
    } else {
      this.showPreview(file);
      this.elements.hiddenInput.value = result.imageUrl;

      // ✅ Mostrar modal de éxito
      ModalManager.show("infoModal", {
        title: "Logo actualizado",
        message: "El logo se subió correctamente.",
      });
    }
  }
}
