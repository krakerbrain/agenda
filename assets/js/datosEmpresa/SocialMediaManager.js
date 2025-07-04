export class SocialMediaManager {
  constructor(formElement) {
    this.form = formElement;
    this.apiUrl = `${baseUrl}user_admin/controllers/redesSociales.php`;
    this.table = document.getElementById("social-networks");
  }

  async load() {
    try {
      const res = await fetch(this.apiUrl);
      const { success, data } = await res.json();
      if (success) this.render(data);
    } catch (e) {
      console.error(e);
    }
  }

  render(data) {
    this.table.innerHTML = "";
    data.forEach((social) => {
      const row = document.createElement("tr");
      row.innerHTML = `
        <td>${social.nombre}</td>
        <td>${social.url}</td>
        <td><input type="radio" name="redPreferida" ${social.red_preferida ? "checked" : ""} disabled></td>
        <td><button class="btn btn-danger" data-id="${social.id}">Eliminar</button></td>`;
      this.table.appendChild(row);
    });
  }

  async save() {
    const formData = new FormData(this.form);
    const res = await fetch(this.apiUrl, {
      method: "POST",
      body: formData,
    });
    return await res.json();
  }

  async delete(id) {
    const res = await fetch(this.apiUrl, {
      method: "DELETE",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ id }),
    });
    return await res.json();
  }
}
