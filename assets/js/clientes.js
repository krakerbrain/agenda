export function initClientes() {
  // Obtener el último estado guardado o usar "unconfirmed" por defecto
  const savedStatus = sessionStorage.getItem("status") || "todos";

  // Cargar citas para la pestaña correspondiente al último estado guardado
  loadCustomers(savedStatus);

  const triggerTabList = document.querySelectorAll("#customerTab button");

  // Seleccionar el tab correspondiente al estado guardado
  triggerTabList.forEach((triggerEl) => {
    const status = triggerEl.dataset.bsTarget.substring(1); // Extraer el estado del atributo data-bs-target
    if (status === savedStatus) {
      const tabTrigger = new bootstrap.Tab(triggerEl);
      tabTrigger.show(); // Mostrar el tab correspondiente al estado guardado
    }

    // Agregar el evento de clic para cambiar de tab y actualizar el estado en sessionStorage
    triggerEl.addEventListener("click", (event) => {
      event.preventDefault();
      document.querySelector("#searchCustomerForm").reset();
      const newStatus = event.target.dataset.bsTarget.substring(1);
      sessionStorage.setItem("status", newStatus);
      loadCustomers(newStatus);
    });
  });
}

let currentPage = 1;
const limit = 10;

// Función para cargar citas de acuerdo al estado de la pestaña
async function loadCustomers(status, page = 1) {
  try {
    let url = `${baseUrl}user_admin/controllers/customers.php?status=${status}&page=${page}`;

    const response = await fetch(url, { method: "GET" });
    const { success, data, totalPages: total } = await response.json();

    if (success) {
      fillTableCustomers(data);
      currentPage = page;
      // Si el número de citas recibidas es menor que el límite, no hay más páginas
      const hasMoreData = data.length === limit;
      updatePaginationControls(hasMoreData);
    }
  } catch (error) {
    console.error("Error al obtener citas:", error);
  }
}

function fillTableCustomers(data) {
  console.log(data);
  const tableContent = document.getElementById("tableContent");
  let html = "";

  data.forEach((customer) => {
    html += `
          <tr class="body-table">
              <td data-cell="nombre" class="data">${customer.name}</td>
               <td data-cell="telefono" class="data"><i class="fab fa-whatsapp pe-1" style="font-size:0.85rem"></i><a href="https://wa.me/${customer.phone}" target="_blank">+${customer.phone}</a></td>
              <td data-cell="correo" class="data">${customer.mail}</td>
              <td data-cell="estado" class="data">${getStatusIcon(customer.blocked, customer.has_incidents)}</td>
          </tr>
      `;
  });
  tableContent.innerHTML = html;

  //   // Añadir listeners para los botones de confirmación y eliminación después de actualizar el contenido
  //   data.forEach((customer) => {
  //     const confirmarBtn = document.getElementById(`confirmarBtn${customer.id}`);
  //     const eliminarBtn = document.getElementById(`eliminarBtn${customer.id}`);

  //     if (confirmarBtn) {
  //       confirmarBtn.addEventListener("click", function () {
  //         confirmReservation(customer.id);
  //       });
  //     }

  //     eliminarBtn.addEventListener("click", function () {
  //       deletecustomer(customer.id, customer.event_id);
  //     });
  //   });
}

function getStatusIcon(blocked, hasIncidents) {
  if (blocked) return '<i class="fas fa-ban" style="color: red;"></i>'; // Bloqueado
  if (hasIncidents) return '<i class="fas fa-exclamation-triangle" style="color: orange;"></i>'; // Tiene incidencias
  return '<i class="fas fa-check-circle" style="color: green;"></i>'; // Estado normal
}
function updatePaginationControls(hasMoreData) {
  document.getElementById("currentPage").innerText = `Página ${currentPage}`;
  document.getElementById("prevPage").disabled = currentPage === 1;
  document.getElementById("nextPage").disabled = !hasMoreData; // Deshabilitar "Siguiente" si no hay más datos
}

document.getElementById("prevPage").addEventListener("click", () => {
  if (currentPage > 1) {
    loadCustomers("all", currentPage - 1);
  }
});

document.getElementById("nextPage").addEventListener("click", () => {
  loadCustomers("all", currentPage + 1);
});
