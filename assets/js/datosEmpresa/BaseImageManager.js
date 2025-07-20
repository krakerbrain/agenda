export class BaseImageManager {
  constructor({ baseUrl, uploader, config }) {
    this.baseUrl = baseUrl;
    this.uploader = uploader;
    this.elements = config.elements;
    this.defaultImage = config.defaultImage;
    this.initEvents();
  }

  initEvents() {
    // Eventos comunes del dropzone
    this.elements.dropzone.addEventListener("click", () => this.elements.input.click());

    this.elements.dropzone.addEventListener("dragover", (e) => {
      e.preventDefault();
      this.elements.dropzone.classList.add("bg-gray-200", "border-blue-400");
    });

    this.elements.dropzone.addEventListener("dragleave", () => {
      this.elements.dropzone.classList.remove("bg-gray-200", "border-blue-400");
    });

    this.elements.dropzone.addEventListener("drop", (e) => {
      e.preventDefault();
      this.elements.dropzone.classList.remove("bg-gray-200", "border-blue-400");
      if (e.dataTransfer.files[0]) {
        this.handleFileSelect(e.dataTransfer.files[0]);
      }
    });

    this.elements.input.addEventListener("change", (e) => {
      if (e.target.files[0]) this.handleFileSelect(e.target.files[0]);
    });
  }

  async handleFileSelect(file) {
    throw new Error("Método handleFileSelect debe ser implementado por subclases");
  }

  showPreview(file) {
    const reader = new FileReader();
    reader.onload = (e) => {
      this.elements.preview.src = e.target.result;
    };
    reader.readAsDataURL(file);
  }

  resetToDefault() {
    this.elements.input.value = "";
    this.elements.preview.src = this.defaultImage;
    if (this.elements.hiddenInput) {
      this.elements.hiddenInput.value = "";
    }
  }
}
