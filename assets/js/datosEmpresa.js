// datosEmpresa.js
import { FileUploader } from "./datosEmpresa/FileUploader.js";
import { ImageCropper } from "./datosEmpresa/ImageCropper.js";
import { CompanyDataManager } from "./datosEmpresa/CompanyDataManager.js";
import { SocialMediaManager } from "./datosEmpresa/SocialMediaManager.js";

export function init() {
  const uploader = new FileUploader(`${baseUrl}user_admin/controllers/datosEmpresa/uploadHandler.php`);
  const cropper = new ImageCropper(document.getElementById("image-to-crop"), document.getElementById("cropped-image"));
  const companyForm = document.getElementById("datosEmpresaForm");
  const companyManager = new CompanyDataManager(companyForm, uploader);
  const socialManager = new SocialMediaManager(document.getElementById("social-form"));

  // Cargar datos iniciales
  companyManager.load();
  socialManager.load();

  // Manejo del logo drag-and-drop y clic
  const logoDropzone = document.getElementById("logoDropzone");
  const logoInput = document.getElementById("logo");

  logoDropzone.addEventListener("click", () => {
    logoInput.click();
  });

  logoDropzone.addEventListener("dragover", (e) => {
    e.preventDefault();
    logoDropzone.classList.add("bg-gray-200");
  });

  logoDropzone.addEventListener("dragleave", () => {
    logoDropzone.classList.remove("bg-gray-200");
  });

  logoDropzone.addEventListener("drop", async (e) => {
    e.preventDefault();
    logoDropzone.classList.remove("bg-gray-200");

    const file = e.dataTransfer.files[0];
    if (file && file.type.startsWith("image/")) {
      logoInput.files = e.dataTransfer.files;
      await handleLogoUpload(file, uploader);
    }
  });

  logoInput.addEventListener("change", async (e) => {
    const file = e.target.files[0];
    if (file) {
      await handleLogoUpload(file, uploader);
    }
  });

  async function handleLogoUpload(file, uploader) {
    // Previsualización inmediata
    const reader = new FileReader();
    reader.onload = function (e) {
      document.getElementById("preview-logo").src = e.target.result;
    };
    reader.readAsDataURL(file);

    // Construir FormData manualmente
    const formData = new FormData();
    formData.append("file", file);
    formData.append("tipo", "logo");
    formData.append("companyId", document.getElementById("companyId").value);
    formData.append("nombreEmpresa", document.getElementById("companyName").value || "empresa");

    const result = await uploader.upload(formData);

    if (result.success) {
      document.getElementById("preview-logo").src = `${baseUrl}${result.imageUrl}`;
    } else {
      alert(result.error || "Error al subir logo.");
    }
  }

  // Guardar empresa
  companyForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    const result = await companyManager.save();
    alert(result.message || "Guardado con éxito");
    companyManager.load();
    socialManager.load();
  });

  // Subida de banner recortado
  document.getElementById("banner").addEventListener("change", (e) => {
    const file = e.target.files[0];
    const reader = new FileReader();
    reader.onload = () => cropper.init(reader.result);
    reader.readAsDataURL(file);
  });

  document.getElementById("save-banner").addEventListener("click", () => {
    cropper.getBlob(async (blob) => {
      const result = await uploader.upload(blob, {
        companyId: document.getElementById("companyId").value,
      });
      if (result.success) {
        document.getElementById("saved-cropped-image").src = `${baseUrl}${result.imageUrl}`;
        document.getElementById("banner-custom").value = result.fileName;
        document.getElementById("banner-custom").checked = true;
      } else {
        alert(result.error || "Error al subir imagen.");
      }
    });
  });
}
