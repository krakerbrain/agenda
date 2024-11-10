export function initCompanyList() {
  async function fetchCompanies() {
    try {
      const response = await fetch(baseUrl + "user_admin/controllers/companyController.php", {
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

  fetchCompanies();

  document.addEventListener("change", function (event) {
    if (event.target.closest(".form-check-input")) {
      const checkbox = event.target;
      const companyId = checkbox.closest("tr").querySelector('[data-cell="id"]').textContent;
      const isActive = checkbox.checked;

      toggleCompanyStatus(companyId, isActive);
    }
  });

  document.addEventListener("click", function (event) {
    if (event.target.closest(".eliminarCompania")) {
      const button = event.target.closest(".eliminarCompania");
      const companyId = button.getAttribute("data-id");
      if (confirm("Está seguro de querer borrar esta compañía?")) {
        deleteCompany(companyId, button);
      }
    }
  });

  async function deleteCompany(companyId, button) {
    button.querySelector(".spinner-border").classList.remove("d-none");

    try {
      const response = await fetch(baseUrl + "user_admin/controllers/companyController.php", {
        method: "DELETE",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ id: companyId }),
      });

      const { success } = await response.json();

      if (success) {
        fetchCompanies();
      } else {
        alert("Failed to delete company.");
      }
    } catch (error) {
      console.error("Error deleting company:", error);
    } finally {
      button.querySelector(".spinner-border").classList.add("d-none");
    }
  }
}

function renderCompanies(companies) {
  const tbody = document.querySelector("tbody");
  tbody.innerHTML = ""; // Limpiar el contenido existente

  companies.forEach((company) => {
    const row = document.createElement("tr");
    // Verifica si el logo es null o está vacío
    const companyLogo = company.logo ? `${baseUrl}${company.logo}` : `${baseUrl}assets/img/no_logo.png`;
    row.innerHTML = `
          <td data-cell="id" class="data">${company.id}</td>
          <td data-cell="Habilitado" class="data">
              <div class="form-check form-switch">
                  <input type="checkbox" class="form-check-input" ${company.is_active ? "checked" : ""}>
              </div>
          </td>
          <td data-cell="logo" class="data"><img src="${companyLogo}" alt="Logo" style="width:70px"></td>
          <td data-cell="nombre" class="data">${company.name}</td>
          <td data-cell="url" class="data"><a href="${baseUrl}reservas/${company.custom_url}" target="_blank">URL FORM</a></td>
          <td data-cell="accion">
              <button class="btn btn-danger btn-sm eliminarCompania" title="Eliminar compañía" data-id="${company.id}">
                  <i class="fas fa-times"></i>
                  <span class="spinner-border spinner-border-sm d-none" aria-hidden="true"></span>
                  <span class="button-text"></span>
              </button>
          </td>
      `;

    tbody.appendChild(row);
  });
}

async function toggleCompanyStatus(companyId, isActive) {
  try {
    const response = await fetch(baseUrl + "user_admin/controllers/companyController.php", {
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
