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
