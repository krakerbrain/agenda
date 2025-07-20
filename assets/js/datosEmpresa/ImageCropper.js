// src/js/datosEmpresa/ImageCropper.js
export class ImageCropper {
  constructor(imageElement, config = {}) {
    this.imageElement = imageElement;
    this.cropper = null;
    this.config = {
      viewMode: 1,
      autoCropArea: 1,
      zoomable: true,
      ...config,
    };
  }

  init(src) {
    this.imageElement.src = src;
    if (this.cropper) this.cropper.destroy();
    this.cropper = new Cropper(this.imageElement, this.config);
  }

  getCanvas(width = 600, height = 150) {
    if (!this.cropper) return null;
    return this.cropper.getCroppedCanvas({ width, height });
  }

  getBlob(callback, width = 600, height = 150) {
    const canvas = this.getCanvas(width, height);
    if (!canvas) return;
    canvas.toBlob((blob) => callback(blob), "image/png", 1);
  }

  reset() {
    if (this.cropper) this.cropper.destroy();
    this.cropper = null;
    this.imageElement.src = "#";
  }
}
