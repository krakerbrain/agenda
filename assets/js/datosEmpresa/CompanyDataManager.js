export class CompanyDataManager {
  constructor(formElement, uploader) {
    this.form = formElement;
    this.uploader = uploader;
    this.apiUrl = `${baseUrl}user_admin/controllers/datosEmpresa.php`;
  }

  async load() {
    try {
      const response = await fetch(this.apiUrl);
      const { success, data } = await response.json();
      if (success) this.populateForm(data[0]);
    } catch (err) {
      console.error("Error cargando datos empresa:", err);
    }
  }

  populateForm(data) {
    this.form.querySelector("#companyName").value = data.name;
    this.form.querySelector("#phone").value = data.phone;
    this.form.querySelector("#address").value = data.address;
    this.form.querySelector("#description").textContent = data.description;
    // otros campos...
  }

  async save() {
    const formData = new FormData(this.form);
    const response = await fetch(this.apiUrl, {
      method: "POST",
      body: formData,
    });
    return await response.json();
  }
}
