document.addEventListener("DOMContentLoaded", function () {
  fetchCompanies();
});

function fetchCompanies() {
  fetch(baseUrl + "master_admin/controllers/CompanyController.php")
    .then((response) => response.json())
    .then((data) => {
      renderCompanies(data);
    })
    .catch((error) => console.error("Error fetching companies:", error));
}

function renderCompanies(companies) {
  const tbody = document.querySelector("tbody");
  tbody.innerHTML = ""; // Limpiar el contenido existente

  companies.forEach((company) => {
    const row = document.createElement("tr");

    row.innerHTML = `
            <td data-cell="id" class="data">${company.id}</td>
            <td data-cell="Habilitado" class="data">
                <div class="form-check form-switch">
                    <input type="checkbox" class="form-check-input" ${company.is_active ? "checked" : ""}>
                </div>
            </td>
            <td data-cell="logo" class="data"><img src="${baseUrl}${company.logo}" alt="Logo" style="width:70px"></td>
            <td data-cell="nombre" class="data">${company.name}</td>
            <td data-cell="url" class="data"><a href="${baseUrl}${company.token}" target="_blank">URL FORM</a></td>
            <td data-cell="accion">
                <button class="btn btn-danger btn-sm eliminarReserva" title="Eliminar reserva" data-id="${company.id}">
                    <i class="fas fa-times"></i>
                    <span class="spinner-border spinner-border-sm d-none" aria-hidden="true"></span>
                    <span class="button-text"></span>
                </button>
            </td>
        `;

    tbody.appendChild(row);
  });
}
