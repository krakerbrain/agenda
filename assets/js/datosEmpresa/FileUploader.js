export class FileUploader {
  constructor(uploadUrl) {
    this.uploadUrl = uploadUrl;
    this.defaultOptions = {
      acceptedTypes: ["image/jpeg", "image/png", "image/gif"],
      maxSizeMB: 2,
    };
  }

  async upload(file, customOptions = {}) {
    const options = { ...this.defaultOptions, ...customOptions };

    if (!this.validateFile(file, options.acceptedTypes, options.maxSizeMB)) {
      return {
        success: false,
        error: `Archivo no válido. Formatos aceptados: ${options.acceptedTypes.join(", ")}. Tamaño máximo: ${options.maxSizeMB}MB`,
      };
    }

    const formData = new FormData();
    formData.append(options.additionalData.tipo, file);

    // Agregar opciones adicionales al FormData
    if (customOptions.additionalData) {
      Object.entries(customOptions.additionalData).forEach(([key, value]) => {
        formData.append(key, value);
      });
    }

    try {
      const response = await fetch(this.uploadUrl, {
        method: "POST",
        body: formData,
      });
      return await response.json();
    } catch (err) {
      return { success: false, error: err.message };
    }
  }

  validateFile(file, acceptedTypes = this.defaultOptions.acceptedTypes, maxSizeMB = this.defaultOptions.maxSizeMB) {
    return acceptedTypes.includes(file.type) && file.size <= maxSizeMB * 1024 * 1024;
  }
}
