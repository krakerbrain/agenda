import { BaseImageManager } from "./BaseImageManager.js";

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
      alert(result.error);
    } else {
      this.showPreview(file);
      this.elements.hiddenInput.value = result.imageUrl;
    }
  }
}
