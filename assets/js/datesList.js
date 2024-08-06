export function initDateList() {
  console.log("aqui");

  document.querySelector(".confirm").addEventListener("click", function (event) {
    event.preventDefault();
    confirmReservation(event.currentTarget.dataset.id);
  });

  function confirmReservation(id) {
    fetch(`${baseUrl}user_admin/confirm.php`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        id: id,
      }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          if (data.success) {
            alert(data.message);
            location.reload();
          }
        } else {
          alert("Error desconocido al confirmar la reserva.");
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        alert("Error al confirmar la reserva.");
      });
  }
}
