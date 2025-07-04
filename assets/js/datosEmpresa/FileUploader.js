export class FileUploader {
  constructor(uploadUrl) {
    this.uploadUrl = uploadUrl;
  }

  async upload(file, extraData = {}, fileName = null) {
    const formData = new FormData();
    formData.append("file", file, fileName ?? file.name);

    for (const key in extraData) {
      formData.append(key, extraData[key]);
    }

    try {
      const response = await fetch(this.uploadUrl, {
        method: "POST",
        body: formData,
      });
      return await response.json();
    } catch (err) {
      console.error("Error al subir archivo:", err);
      return { success: false, error: "Error de red" };
    }
  }

  validateFile(file, acceptedTypes = [], maxSizeMB = 2) {
    const isValidType = acceptedTypes.includes(file.type);
    const isValidSize = file.size <= maxSizeMB * 1024 * 1024;
    return isValidType && isValidSize;
  }
}
