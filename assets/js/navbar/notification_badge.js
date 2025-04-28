// Manejo de notificaciones
document.addEventListener("DOMContentLoaded", function () {
  const notificationDropdown = document.getElementById("notificationDropdown");
  const notificationList = document.getElementById("notification-list");
  const viewAllBtn = document.getElementById("view-all-notifications");

  if (notificationDropdown) {
    // Cargar notificaciones al abrir el dropdown
    notificationDropdown.addEventListener("shown.bs.dropdown", loadNotifications);

    // Marcar como leída al hacer clic
    notificationList.addEventListener("click", function (e) {
      if (e.target.closest(".notification-item")) {
        const item = e.target.closest(".notification-item");
        const notificationId = item.dataset.notificationId;
        markAsRead(notificationId, item);
        goToNotificationPage(); // Redirigir a la página de notificaciones
      }
    });
  }

  if (viewAllBtn) {
    viewAllBtn.addEventListener("click", function (e) {
      e.preventDefault();
      // Aquí puedes redirigir a una página de todas las notificaciones
      goToNotificationPage();
    });
  }
});

function goToNotificationPage() {
  // Redirigir a la página de notificaciones
  sessionStorage.setItem("lastPage", "notificaciones");
  location.reload();
}

async function loadNotifications() {
  const notificationList = document.getElementById("notification-list");

  if (!notificationList) {
    console.error("Elemento notification-list no encontrado");
    return;
  }

  try {
    // Mostrar loader mientras se cargan los datos
    notificationList.innerHTML = `
        <li class="px-3 py-2 text-center">
          <div class="spinner-border spinner-border-sm" role="status">
            <span class="visually-hidden">Cargando...</span>
          </div>
        </li>`;

    const response = await fetch(`${baseUrl}user_admin/controllers/notification_controller.php?action=getUnread`, { method: "GET" });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const data = await response.json();

    if (data.success && data.notifications.length > 0) {
      let html = "";

      for (const notification of data.notifications) {
        const icon = getNotificationIcon(notification.notification_type);
        const timeAgo = getTimeAgo(notification.created_at);

        html += `
            <li class="notification-item px-3 py-2" data-notification-id="${notification.user_notification_id}" style="cursor: pointer;">
              <div class="d-flex gap-2">
                <div class="flex-shrink-0 text-primary">${icon}</div>
                <div class="flex-grow-1">
                  <h6 class="mb-1">${notification.title}</h6>
                  <div class="d-flex justify-content-between align-items-center mt-1">
                    <small class="text-muted">${timeAgo}</small>
                    <small class="badge bg-${getNotificationBadgeClass(notification.notification_type)}">
                      ${notification.notification_type === "feature" ? "Nuevo" : notification.notification_type === "bugfix" ? "Corrección" : "Aviso"}
                    </small>
                  </div>
                </div>
              </div>
            </li>
            <li><hr class="dropdown-divider my-1"></li>`;
      }

      notificationList.innerHTML = html;
    } else {
      notificationList.innerHTML = `
          <li class="px-3 py-3 text-center text-muted">
            No tienes notificaciones nuevas
          </li>`;
    }
  } catch (error) {
    console.error("Error loading notifications:", error);
    notificationList.innerHTML = `
        <li class="px-3 py-3 text-center text-danger">
          Error al cargar notificaciones
        </li>`;
  }
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

// Funciones de ayuda
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
