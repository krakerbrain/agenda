import { loadIcon, NOTIFICATION_ICONS } from "../helpers/icons.js";

export function init() {
  loadAllNotifications();

  document.getElementById("mark-all-read")?.addEventListener("click", function () {
    markAllAsRead();
  });
}

async function loadAllNotifications() {
  try {
    const container = document.getElementById("all-notifications-list");
    if (!container) return;

    // Mostrar loader mientras se cargan los datos
    container.innerHTML = `
      <div class="text-center py-4">
        <div class="inline-block h-8 w-8 border-4 border-blue-200 border-t-blue-500 rounded-full animate-spin" role="status">
          <span class="sr-only">Cargando...</span>
        </div>
      </div>`;

    const response = await fetch(`${baseUrl}user_admin/controllers/notification_controller.php?action=getAll`, { method: "GET" });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const data = await response.json();

    if (data.success && data.notifications?.length > 0) {
      let html = "";

      for (const notification of data.notifications) {
        const icon = await getNotificationIcon(notification.notification_type);
        const timeAgo = getTimeAgo(notification.created_at);
        const readClass = notification.is_read ? "bg-gray-50 opacity-75" : "bg-white hover:bg-gray-50 cursor-pointer";
        const readBadge = notification.is_read
          ? `<small class="text-gray-500 text-sm">Leído ${getTimeAgo(notification.read_at)}</small>`
          : '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Nuevo</span>';

        html += `
          <div class="border border-gray-200 rounded-lg p-4 mb-2 ${readClass}" 
               data-notification-id="${notification.user_notification_id}">
            <div class="flex gap-3 w-full justify-between">
              <div class="flex-shrink-0 mt-1 text-blue-500">${icon}</div>
              <div class="flex-grow-1 min-w-0">
                <div class="flex justify-between items-start gap-2">
                  <h6 class="mb-1 font-medium text-gray-900 truncate">${notification.title}</h6>
                  ${readBadge}
                </div>
                <p class="mb-1 text-gray-600">${notification.description.replace(/\n/g, "<br>")}</p>
                <div class="flex justify-between mt-2">
                  <small class="text-gray-500 text-sm">Versión ${notification.version}</small>
                  <small class="text-gray-500 text-sm">${timeAgo}</small>
                </div>
              </div>
            </div>
          </div>`;
      }

      container.innerHTML = html;

      // Manejar clic en notificaciones
      container.querySelectorAll("[data-notification-id]").forEach((item) => {
        if (!item.classList.contains("bg-gray-50")) {
          item.addEventListener("click", async function () {
            const notificationId = this.dataset.notificationId;
            await markAsRead(notificationId, this);
            this.classList.remove("bg-white", "hover:bg-gray-50");
            this.classList.add("bg-gray-50", "opacity-75");
            await updateNotificationCount();
          });
        }
      });
    } else {
      container.innerHTML = `
        <div class="text-center py-4 text-gray-500">
          No tienes notificaciones
        </div>`;
    }
  } catch (error) {
    console.error("Error loading notifications:", error);
    const container = document.getElementById("all-notifications-list");
    if (container) {
      container.innerHTML = `
        <div class="text-center py-4 text-red-500">
          Error al cargar notificaciones
        </div>`;
    }
  }
}

async function markAllAsRead() {
  try {
    const response = await fetch(`${baseUrl}user_admin/controllers/notification_controller.php?action=markAllAsRead`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
    });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const data = await response.json();

    if (data.success) {
      // Actualizar la UI
      document.querySelectorAll("[data-notification-id]").forEach((item) => {
        if (!item.classList.contains("bg-gray-50")) {
          item.classList.remove("bg-white", "hover:bg-gray-50");
          item.classList.add("bg-gray-50", "opacity-75");
        }
      });

      // Actualizar contador
      await updateNotificationCount();
      loadAllNotifications();
      // Mostrar feedback
      showToast(data.message);
      return true;
    } else {
      showToast(data.message, "error");
      return false;
    }
  } catch (error) {
    console.error("Error:", error);
    showToast("Error de conexión", "error");
    return false;
  }
}

async function updateNotificationCount() {
  try {
    const response = await fetch(`${baseUrl}user_admin/controllers/notification_controller.php?action=getUnreadCount`, { method: "GET" });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const data = await response.json();
    const badge = document.querySelector(".navbar .badge");
    const icon = document.querySelector(".navbar .bi-envelope");

    if (!icon) return;

    if (data.count > 0) {
      if (!badge) {
        // Crear el badge si no existe
        const newBadge = document.createElement("span");
        newBadge.className =
          "absolute -top-2 -right-2 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-500 rounded-full";
        newBadge.innerHTML = `${data.count}<span class="sr-only">notificaciones no leídas</span>`;
        icon.parentElement.classList.add("relative");
        icon.parentElement.appendChild(newBadge);
      } else {
        badge.textContent = data.count;
      }
    } else if (badge) {
      badge.remove();
    }
  } catch (error) {
    console.error("Error updating notification count:", error);
  }
}

async function getNotificationIcon(type) {
  try {
    const iconName = NOTIFICATION_ICONS[type] || NOTIFICATION_ICONS.default;
    const icon = await loadIcon(iconName, "w-4 h-4 text-blue-500");
    return icon || '<span class="w-4 h-4 bg-gray-300 rounded-full"></span>';
  } catch (error) {
    console.error("Error loading notification icon:", error);
    return '<span class="w-4 h-4 bg-gray-300 rounded-full"></span>';
  }
}

async function showToast(message, type = "success") {
  const toastContainer = document.getElementById("toast-container");
  if (!toastContainer) return;

  try {
    // Cargar iconos en paralelo
    const [icon, closeIcon] = await Promise.all([loadIcon(type === "error" ? "x-circle" : "check-circle", "w-5 h-5"), loadIcon("x", "w-5 h-5")]);

    const toast = document.createElement("div");
    toast.className = `flex items-center w-full max-w-xs p-4 mb-4 text-white rounded-lg shadow ${type === "error" ? "bg-red-500" : "bg-green-500"}`;
    toast.setAttribute("role", "alert");

    toast.innerHTML = `
      <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg">
        ${icon}
      </div>
      <div class="ml-3 text-sm font-normal">${message}</div>
      <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex h-8 w-8" aria-label="Close">
        <span class="sr-only">Close</span>
        ${closeIcon}
      </button>`;

    toastContainer.appendChild(toast);

    // Auto-remove after delay
    const timer = setTimeout(() => toast.remove(), 5000);

    // Close button functionality
    toast.querySelector("button").addEventListener("click", () => {
      clearTimeout(timer);
      toast.remove();
    });
  } catch (error) {
    console.error("Error showing toast:", error);
    // Fallback básico sin iconos
    const fallbackToast = document.createElement("div");
    fallbackToast.className = `p-4 mb-4 text-white rounded-lg ${type === "error" ? "bg-red-500" : "bg-green-500"}`;
    fallbackToast.textContent = message;
    toastContainer.appendChild(fallbackToast);
    setTimeout(() => fallbackToast.remove(), 5000);
  }
}

function getNotificationBadgeClass(type) {
  const classes = {
    feature: "primary",
    bugfix: "success",
    announcement: "warning",
  };
  return classes[type] || "secondary";
}

function getTimeAgo(dateString) {
  const date = new Date(dateString);
  const now = new Date();
  const seconds = Math.floor((now - date) / 1000);

  if (seconds < 60) return "hace unos segundos";
  if (seconds < 3600) return `hace ${Math.floor(seconds / 60)} minutos`;
  if (seconds < 86400) return `hace ${Math.floor(seconds / 3600)} horas`;
  return `hace ${Math.floor(seconds / 86400)} días`;
}

async function markAsRead(notificationId, element) {
  try {
    const response = await fetch(`${baseUrl}user_admin/controllers/notification_controller.php?action=markAsRead`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ notification_id: notificationId }),
    });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const data = await response.json();

    if (data.success) {
      // Actualizar el contador de notificaciones
      await updateNotificationCount();

      // Cambiar el estilo de la notificación leída si se proporcionó el elemento
      if (element) {
        element.classList.add("opacity-75");
        element.classList.remove("unread");
        element.classList.add("read");
      }
    }
  } catch (error) {
    console.error("Error marking notification as read:", error);
    // Podrías mostrar un mensaje al usuario aquí si lo deseas
  }
}
