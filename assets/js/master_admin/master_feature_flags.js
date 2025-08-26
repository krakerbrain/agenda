export function init() {
  // Traer y renderizar los flags existentes
  async function fetchFeatureFlags() {
    try {
      const response = await fetch(baseUrl + "user_admin/controllers/featureFlagController.php", {
        method: "GET",
      });
      const { success, data } = await response.json();
      if (success) renderFeatureFlags(data);
    } catch (error) {
      console.error("Error fetching feature flags:", error);
    }
  }

  // Renderizar la tabla de flags
  function renderFeatureFlags(flags) {
    const tbody = document.getElementById("featureFlagsList");
    tbody.innerHTML = "";

    flags.forEach((flag) => {
      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td data-cell="id">${flag.id}</td>
        <td>${flag.feature_name}</td>
        <td>${flag.company_name}</td>
        <td>
          <input type="checkbox" class="form-check-input" ${flag.enabled ? "checked" : ""}>
        </td>
        <td>
          <button class="btn btn-sm guardarFlag">Guardar</button>
        </td>
      `;
      tbody.appendChild(tr);
      document.addEventListener("click", async function (event) {
        if (event.target.closest(".guardarFlag")) {
          const button = event.target.closest(".guardarFlag");
          const tr = button.closest("tr");
          const id = tr.querySelector('[data-cell="id"]').textContent;
          const enabled = tr.querySelector(".form-check-input").checked;

          try {
            const res = await fetch(baseUrl + "user_admin/controllers/featureFlagController.php", {
              method: "POST",
              headers: { "Content-Type": "application/json" },
              body: JSON.stringify({ id, enabled }),
            });
            const result = await res.json();
            if (result.success) {
              alert("Flag actualizado correctamente");
            } else {
              alert("Error: " + (result.msg || "No se pudo actualizar"));
            }
          } catch (err) {
            console.error("Error actualizando flag:", err);
          }
        }
      });
    });
  }

  // Traer compañías y llenar select
  async function fetchCompanies() {
    try {
      const res = await fetch(baseUrl + "user_admin/controllers/companyController.php", {
        method: "GET",
      });
      const { success, data } = await res.json();
      console.log(success, data);
      if (success) {
        const select = document.getElementById("companySelect");
        select.innerHTML = `<option value="">Selecciona compañía</option>`;
        data.forEach((company) => {
          const option = document.createElement("option");
          option.value = company.id;
          option.textContent = company.name;
          select.appendChild(option);
        });
      }
    } catch (err) {
      console.error("Error fetching companies:", err);
    }
  }

  // Guardar cambios de un flag existente
  document.getElementById("addFeatureFlagForm").addEventListener("submit", async function (event) {
    event.preventDefault();
    const select = document.getElementById("companySelect");
    const companyId = select.value;
    if (!companyId) {
      alert("Por favor selecciona una compañía.");
      return;
    }

    const featureName = document.getElementById("featureName").value;
    if (!featureName) {
      alert("Por favor ingresa un nombre de flag.");
      return;
    }

    try {
      const res = await fetch(baseUrl + "user_admin/controllers/featureFlagController.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ companyId, featureName }),
      });
      const result = await res.json();
      if (result.success) {
        alert("Flag creado correctamente");
        location.reload();
      }
    } catch (err) {
      console.error("Error creando flag:", err);
    }
  });

  // Inicialización
  fetchCompanies(); // Llenar select de compañías
  fetchFeatureFlags(); // Mostrar flags existentes
}
