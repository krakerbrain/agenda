import { ModalManager } from "../config/ModalManager.js";
export class SocialMediaManager {
  constructor(formElement) {
    this.form = formElement;
    this.apiUrl = `${baseUrl}user_admin/controllers/redesSociales.php`;
    this.container = document.getElementById("social-networks");
    this.sortableInstance = null;
    this.form.addEventListener("submit", (e) => this.handleFormSubmit(e));
  }

  async load() {
    try {
      const res = await fetch(this.apiUrl);
      const { success, data } = await res.json();
      if (success) {
        // Ordenar los datos por el campo 'orden' antes de renderizar
        const sortedData = data.sort((a, b) => a.orden - b.orden);
        this.render(sortedData);
        this.initSortable();
      }
    } catch (e) {
      console.error(e);
    }
  }

  render(data) {
    this.container.innerHTML = "";

    data.forEach((social) => {
      const card = document.createElement("div");
      card.className = "relative bg-white rounded-lg shadow p-4 border border-gray-200 hover:border-blue-300 transition cursor-move";
      card.dataset.id = social.id;

      const socialIcon = this.getSocialIcon(social.nombre);

      // Eliminado el radio button de red preferida y simplificado el diseño
      card.innerHTML = `
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 bg-white rounded-lg shadow border border-gray-200 hover:border-blue-300 transition cursor-move">
          <!-- Primera línea: Icono y nombre -->
          <div class="flex items-center gap-3 w-full sm:w-auto">
            ${socialIcon}
            <h4 class="font-medium text-gray-800 flex-shrink-0">${social.nombre}</h4>
          </div>
          
          <!-- Segunda línea: URL -->
          <div class="w-full sm:w-auto">
            <a href="${social.url}" target="_blank" class="md:text-sm text-xs text-blue-600 hover:underline break-all sm:truncate block max-w-full">
              ${social.url}
            </a>
          </div>
          
          <!-- Tercera línea: Solo botón eliminar en desktop -->
          <div class="flex flex-row-reverse sm:flex-row items-center justify-end gap-4 w-full sm:w-auto">
            <button class="text-red-500 hover:text-red-700 transition delete-social" data-id="${social.id}">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
            </button>
          </div>
        </div>
      `;

      this.container.appendChild(card);
    });

    // Event listeners para botones de eliminar
    document.querySelectorAll(".delete-social").forEach((button) => {
      button.addEventListener("click", (e) => {
        e.preventDefault();
        const id = button.dataset.id;
        this.delete(id);
      });
    });
  }

  getSocialIcon(socialName) {
    // Puedes personalizar esto con los iconos adecuados para cada red social
    const icons = {
      Facebook: "facebook",
      Twitter: "twitter",
      Instagram: "instagram",
      LinkedIn: "linkedin",
      YouTube: "youtube",
      TikTok: "tiktok",
      Pinterest: "pinterest",
      WhatsApp: "whatsapp",
    };

    const iconClass = icons[socialName] || "globe";

    return `
      <div class="flex-shrink-0 bg-blue-100 p-2 rounded-full">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <use xlink:href="${baseUrl}assets/icons/social-icons.svg#${iconClass}" />
        </svg>
      </div>
    `;
  }

  initSortable() {
    if (this.sortableInstance) {
      this.sortableInstance.destroy();
    }

    this.sortableInstance = new Sortable(this.container, {
      animation: 150,
      ghostClass: "bg-blue-50",
      onEnd: (evt) => {
        this.updateSocialOrder();
      },
    });
  }

  async updateSocialOrder() {
    const order = Array.from(this.container.children).map((card, index) => ({
      id: card.dataset.id,
      order: index + 1,
    }));

    try {
      const res = await fetch(this.apiUrl, {
        method: "PUT",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ order }),
      });

      const { success } = await res.json();
      if (!success) {
        console.error("Error al actualizar el orden");
      }
    } catch (error) {
      console.error("Error:", error);
    }
  }

  async handleFormSubmit(event) {
    event.preventDefault();

    try {
      const formData = new FormData(this.form);
      const response = await this.save(formData);

      if (response.success) {
        // Recargar las redes sociales después de agregar una nueva
        await this.load();
        // Resetear el formulario
        this.form.reset();
        // Mostrar notificación de éxito
        this.showNotification("Red social agregada correctamente", "Éxito");
      } else {
        this.showNotification(response.error || "Error al agregar red social", "Error");
      }
    } catch (error) {
      console.error("Error:", error);
      this.showNotification("Error al procesar la solicitud", "Error");
    }
  }

  async save(formData) {
    try {
      const res = await fetch(this.apiUrl, {
        method: "POST",
        body: formData,
      });

      const { success, message, error } = await res.json();
      if (success) {
        this.load();
        this.showNotification(message || "Red social guardada", "Éxito");
        return { success: true };
      } else {
        return { success: false, error: "Error al guardar red social. Posiblemente ya existe una red social con ese nombre" };
      }
    } catch (error) {
      console.error("Error:", error);
      return { success: false, error: "Error de conexión" };
    }
  }

  async delete(id) {
    if (!confirm("¿Estás seguro de que quieres eliminar esta red social?")) {
      return;
    }

    try {
      const res = await fetch(this.apiUrl, {
        method: "DELETE",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id }),
      });

      const { success, message } = await res.json();
      if (success) {
        this.load();
        this.showNotification(message || "Red social eliminada", "Éxito");
      } else {
        this.showNotification(message || "Error al eliminar", "Error");
      }
    } catch (error) {
      console.error(error);
      this.showNotification("Error al eliminar", "Error");
    }
  }

  showNotification(message, title) {
    // alert(`${type.toUpperCase()}: ${message}`);
    ModalManager.show("infoModal", {
      title: title,
      message: message,
    });
  }
}
