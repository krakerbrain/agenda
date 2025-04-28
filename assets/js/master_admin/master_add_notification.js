export function initAddNotification() {
  const form = document.getElementById("notificationForm");

  form.addEventListener("submit", async function (e) {
    e.preventDefault();

    const submitBtn = form.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enviando...';

    try {
      const response = await fetch(`${baseUrl}user_admin/controllers/notification_controller.php?action=create`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          type: document.getElementById("notificationType").value,
          title: document.getElementById("notificationTitle").value,
          description: document.getElementById("notificationDescription").value,
          version: document.getElementById("notificationVersion").value || null,
          send_email: document.getElementById("sendEmail").checked,
        }),
      });

      const data = await response.json();

      if (data.success) {
        handleInfoModal("Éxito", data.message);
        setTimeout(() => {
          location.reload();
        }, 2500);
      } else {
        handleInfoModal("Error", data.message);
      }
    } catch (error) {
      console.error("Error:", error);
      handleInfoModal("Error", "Error de conexión. Por favor, inténtelo de nuevo más tarde.");
    } finally {
      submitBtn.disabled = false;
      submitBtn.innerHTML = '<i class="bi bi-send me-1"></i> Publicar Notificación';
    }
  });

  function handleInfoModal(title = null, message = null) {
    let titulo = document.getElementById("infoModalLabel");
    let mensaje = document.getElementById("infoModalMessage");
    titulo.textContent = title;
    mensaje.textContent = message;
    const modal = new bootstrap.Modal(document.getElementById("infoModal"));
    modal.show();
  }
}
