// Manejo de notificaciones
document.addEventListener("DOMContentLoaded", function () {
  const notificationDropdown = document.getElementById("notificationDropdown");
  const notificationList = document.getElementById("notification-list");
  const viewAllBtn = document.getElementById("view-all-notifications");

  if (notificationDropdown) {
    // Cargar notificaciones al abrir el dropdown
    notificationDropdown.addEventListener("click", loadNotifications);

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
      goToNotificationPage();
    });
  }
});

function goToNotificationPage() {
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
        <div class="inline-block h-4 w-4 border-2 border-blue-200 border-t-blue-500 rounded-full animate-spin" role="status">
          <span class="sr-only">Cargando...</span>
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
        const badgeClass = getNotificationBadgeClass(notification.notification_type);
        const badgeText = notification.notification_type === "feature" ? "Nuevo" : notification.notification_type === "bugfix" ? "Corrección" : "Aviso";

        html += `
          <li class="notification-item px-3 py-2 hover:bg-gray-50 cursor-pointer" data-notification-id="${notification.user_notification_id}">
            <div class="flex gap-2">
              <div class="flex-shrink-0 text-blue-500">${icon}</div>
              <div class="flex-grow-1 min-w-0">
                <h6 class="mb-1 font-medium text-gray-900 truncate">${notification.title}</h6>
                <div class="flex justify-between items-center mt-1">
                  <small class="text-gray-500">${timeAgo}</small>
                  <small class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-${badgeClass}-100 text-${badgeClass}-800">
                    ${badgeText}
                  </small>
                </div>
              </div>
            </div>
          </li>
          <li><hr class="border-t border-gray-200 my-1"></li>`;
      }

      notificationList.innerHTML = html;
    } else {
      notificationList.innerHTML = `
        <li class="px-3 py-3 text-center text-gray-500">
          No tienes notificaciones nuevas
        </li>`;
    }
  } catch (error) {
    console.error("Error loading notifications:", error);
    notificationList.innerHTML = `
      <li class="px-3 py-3 text-center text-red-500">
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
      await updateNotificationCount();
      if (element) {
        element.classList.add("opacity-75");
        element.classList.remove("hover:bg-gray-50");
      }
    }
  } catch (error) {
    console.error("Error marking notification as read:", error);
  }
}

async function updateNotificationCount() {
  try {
    const response = await fetch(`${baseUrl}user_admin/controllers/notification_controller.php?action=getUnreadCount`, { method: "GET" });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const data = await response.json();
    const badge = document.querySelector(".navbar .notification-badge");
    const icon = document.querySelector(".navbar .notification-icon");

    if (!icon) return;

    if (data.count > 0) {
      if (!badge) {
        const newBadge = document.createElement("span");
        newBadge.className =
          "absolute -top-2 -right-2 inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-500 rounded-full";
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

// Funciones de ayuda (sin cambios en el contenido, solo actualizadas las clases si es necesario)
function getNotificationIcon(type) {
  const icons = {
    feature:
      '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>',
    bugfix:
      '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9z" clip-rule="evenodd"/></svg>',
    announcement:
      '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 3a1 1 0 00-1.447-.894L8.763 6H5a3 3 0 000 6h.28l1.771 5.316A1 1 0 008 18h1a1 1 0 001-1v-4.382l6.553 3.276A1 1 0 0018 15V3z" clip-rule="evenodd"/></svg>',
  };
  return (
    icons[type] ||
    '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/></svg>'
  );
}

function getNotificationBadgeClass(type) {
  const classes = {
    feature: "blue",
    bugfix: "green",
    announcement: "yellow",
  };
  return classes[type] || "gray";
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
