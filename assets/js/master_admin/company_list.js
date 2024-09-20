document.addEventListener("DOMContentLoaded", function () {
  fetchCompanies();
});

async function fetchCompanies() {
  try {
    const response = await fetch(baseUrl + "master_admin/controllers/companyController.php", {
      method: "GET",
    });

    const { success, data } = await response.json();

    if (success) {
      renderCompanies(data);
    }
  } catch (error) {
    console.error("Error fetching companies:", error);
  }
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

document.addEventListener("change", function (event) {
  if (event.target.closest(".form-check-input")) {
    const checkbox = event.target;
    const companyId = checkbox.closest("tr").querySelector('[data-cell="id"]').textContent;
    const isActive = checkbox.checked;

    toggleCompanyStatus(companyId, isActive);
  }
});

async function toggleCompanyStatus(companyId, isActive) {
  try {
    const response = await fetch(baseUrl + "master_admin/controllers/companyController.php", {
      method: "PATCH",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ id: companyId, is_active: isActive }),
    });

    const { success } = await response.json();

    if (!success) {
      alert("Failed to update company status.");
    }
  } catch (error) {
    console.error("Error updating company status:", error);
  }
}

document.addEventListener("click", function (event) {
  if (event.target.closest(".eliminarReserva")) {
    const button = event.target.closest(".eliminarReserva");
    const companyId = button.getAttribute("data-id");

    if (confirm("Está seguro de querer borrar esta compañía?")) {
      deleteCompany(companyId, button);
    }
  }
});

async function deleteCompany(companyId, button) {
  button.querySelector(".spinner-border").classList.remove("d-none");

  try {
    const response = await fetch(baseUrl + "master_admin/controllers/companyController.php", {
      method: "DELETE",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ id: companyId }),
    });

    const { success } = await response.json();

    if (success) {
      button.closest("tr").remove();
    } else {
      alert("Failed to delete company.");
    }
  } catch (error) {
    console.error("Error deleting company:", error);
  } finally {
    button.querySelector(".spinner-border").classList.add("d-none");
  }
}
