export function initNotificaciones() {
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
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Cargando...</span>
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
        const icon = getNotificationIcon(notification.notification_type);
        const timeAgo = getTimeAgo(notification.created_at);
        const readClass = notification.is_read ? "read" : "unread";
        const readBadge = notification.is_read ? `<small class="text-muted">Leído ${getTimeAgo(notification.read_at)}</small>` : '<span class="badge bg-info">Nuevo</span>';

        html += `
                <div class="list-group-item list-group-item-action ${readClass}" 
                     data-notification-id="${notification.user_notification_id}">
                    <div class="d-flex gap-3 w-100 justify-content-between">
                        <div class="flex-shrink-0 mt-1 text-primary">${icon}</div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <h6 class="mb-1">${notification.title}</h6>
                                ${readBadge}
                            </div>
                            <p class="mb-1">${notification.description}</p>
                            <div class="d-flex justify-content-between">
                                <small class="text-muted">Versión ${notification.version}</small>
                                <small class="text-muted">${timeAgo}</small>
                            </div>
                        </div>
                    </div>
                </div>`;
      }

      container.innerHTML = html;

      // Manejar clic en notificaciones
      container.querySelectorAll(".list-group-item").forEach((item) => {
        item.addEventListener("click", async function () {
          if (!this.classList.contains("read")) {
            const notificationId = this.dataset.notificationId;
            await markAsRead(notificationId);
            this.classList.remove("unread");
            this.classList.add("read");
            await updateNotificationCount();
          }
        });
      });
    } else {
      container.innerHTML = `
                <div class="text-center py-4 text-muted">
                    No tienes notificaciones
                </div>`;
    }
  } catch (error) {
    console.error("Error loading notifications:", error);
    const container = document.getElementById("all-notifications-list");
    if (container) {
      container.innerHTML = `
                <div class="text-center py-4 text-danger">
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
      document.querySelectorAll(".unread").forEach((item) => {
        item.classList.replace("unread", "read");
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
        newBadge.className = "position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger";
        newBadge.innerHTML = `${data.count}<span class="visually-hidden">notificaciones no leídas</span>`;
        icon.parentNode.appendChild(newBadge);
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

// Función auxiliar para mostrar mensajes (implementación básica)
function showToast(message, type = "success") {
  // Implementa tu propio sistema de notificaciones/toasts
  // Ejemplo básico:
  const toast = document.createElement("div");
  toast.className = `toast align-items-center text-white bg-${type === "error" ? "danger" : "success"}`;
  toast.setAttribute("role", "alert");
  toast.setAttribute("aria-live", "assertive");
  toast.setAttribute("aria-atomic", "true");
  toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>`;

  document.getElementById("toast-container").appendChild(toast);
  const bsToast = new bootstrap.Toast(toast);
  bsToast.show();

  // Auto-remove after hide
  toast.addEventListener("hidden.bs.toast", () => {
    toast.remove();
  });
}

function getNotificationIcon(type) {
  const icons = {
    feature: '<i class="bi bi-star-fill"></i>',
    bugfix: '<i class="bi bi-bug-fill"></i>',
    announcement: '<i class="bi bi-megaphone-fill"></i>',
  };
  return icons[type] || '<i class="bi bi-bell-fill"></i>';
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
