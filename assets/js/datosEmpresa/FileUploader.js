export class FileUploader {
  constructor(uploadUrl) {
    this.uploadUrl = uploadUrl;
  }

  async upload(data) {
    // Si es una instancia de FormData, úsala tal cual
    const formData = data instanceof FormData ? data : new FormData();

    return fetch(this.uploadUrl, {
      method: "POST",
      body: formData,
    })
      .then((res) => res.json())
      .catch((err) => ({ success: false, error: err.message }));
  }

  validateFile(file, acceptedTypes = [], maxSizeMB = 2) {
    const isValidType = acceptedTypes.includes(file.type);
    const isValidSize = file.size <= maxSizeMB * 1024 * 1024;
    return isValidType && isValidSize;
  }
}
