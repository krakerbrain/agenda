export function init() {
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
    row.className = "hover:bg-gray-100 transition-colors";
    row.innerHTML = `
      <td data-cell="id" class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">${company.id}</td>
      <td data-cell="Habilitado" class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
        <label class="inline-flex items-center cursor-pointer">
          <input type="checkbox" class="sr-only peer form-check-input" ${company.is_active ? "checked" : ""}>
          <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer peer-checked:bg-blue-600 transition-all"></div>
          <div class="absolute ml-1 mt-1 w-4 h-4 bg-white rounded-full shadow transform peer-checked:translate-x-5 transition-transform"></div>
        </label>
      </td>
      <td data-cell="logo" class="px-4 py-3 whitespace-nowrap text-sm text-gray-700"><img src="${companyLogo}" alt="Logo" class="w-16 h-16 object-contain rounded border border-gray-200"></td>
      <td data-cell="nombre" class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">${company.name}</td>
      <td data-cell="url" class="px-4 py-3 whitespace-nowrap text-sm text-blue-600 underline"><a href="${baseUrl}reservas/${company.custom_url}" target="_blank">URL FORM</a></td>
      <td data-cell="accion" class="px-4 py-3 whitespace-nowrap text-sm">
        <button class="eliminarCompania inline-flex items-center px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded shadow transition-colors focus:outline-none focus:ring-2 focus:ring-red-400" title="Eliminar compañía" data-id="${
          company.id
        }">
          <i class="fas fa-times"></i>
          <span class="ml-2 spinner-border spinner-border-sm hidden" aria-hidden="true"></span>
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
