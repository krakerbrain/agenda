document.addEventListener("DOMContentLoaded", function () {
  loadScheduleTable();
});

const horariosDemo = [
  {
    day: "Lunes",
    day_id: 1,
    enabled: true,
    start: "09:00",
    end: "20:00",
    breaks: ["13:00", "14:00"],
  },
  {
    day: "Martes",
    day_id: 2,
    enabled: false,
    start: "00:00",
    end: "00:00",
    breaks: [],
  },
  {
    day: "Miércoles",
    day_id: 3,
    enabled: false,
    start: "00:00",
    end: "00:00",
    breaks: [],
  },
  {
    day: "Jueves",
    day_id: 4,
    enabled: false,
    start: "00:00",
    end: "00:00",
    breaks: [],
  },
  {
    day: "Viernes",
    day_id: 5,
    enabled: false,
    start: "00:00",
    end: "00:00",
    breaks: [],
  },
  {
    day: "Sábado",
    day_id: 6,
    enabled: false,
    start: "00:00",
    end: "00:00",
    breaks: [],
  },
  {
    day: "Domingo",
    day_id: 7,
    enabled: false,
    start: "00:00",
    end: "00:00",
    breaks: [],
  },
];

function loadScheduleTable() {
  const tableBody = document.getElementById("scheduleTableBody");

  tableBody.innerHTML = "";

  horariosDemo.forEach((horario, index) => {
    const tr = document.createElement("tr");
    tr.className = "work-day body-table";

    // Botón "Copiar en todos" solo para Lunes
    const copyButton = horario.day === "Lunes" ? `<button type="button" class="btn btn-link copy-all">Copiar en todos</button>` : "";

    tr.innerHTML = `
<td data-cell="día" class="data">${horario.day}</td>
<td data-cell="estado" class="data">
<div class="form-check form-switch">
<input class="form-check-input day-toggle" type="checkbox" ${horario.enabled ? "checked" : ""}>
</div>
</td>
<td data-cell="Inicio Jornada" class="data">
<input type="time" class="form-control work-start" value="${horario.start}" ${!horario.enabled ? "disabled" : ""}>
</td>
<td data-cell="Fin Jornada" class="data">
<input type="time" class="form-control work-end" value="${horario.end}" ${!horario.enabled ? "disabled" : ""}>
</td>
<td>
<button type="button" class="btn btn-outline-primary btn-sm descanso" ${!horario.enabled ? "disabled" : ""}>+ Descanso</button>
</td>
<td data-cell="">
${copyButton}
</td>
`;

    tableBody.appendChild(tr);

    // Agregar filas de descanso si existen
    if (horario.breaks.length > 0) {
      const breakRow = document.createElement("tr");
      breakRow.className = "break-time";
      breakRow.innerHTML = `
<td colspan="6">
<div class="d-flex align-items-center gap-3 p-2">
    <span>Descanso:</span>
    <input type="time" class="form-control form-control-sm w-auto break-start" value="${horario.breaks[0]}">
    <span>a</span>
    <input type="time" class="form-control form-control-sm w-auto break-end" value="${horario.breaks[1]}">
    <button type="button" class="btn btn-outline-danger btn-sm remove-break">×</button>
</div>
</td>
`;
      tableBody.appendChild(breakRow);
    }
  });

  // Agregar evento al botón "Copiar en todos"
  document.querySelector(".copy-all")?.addEventListener("click", function () {
    // Obtener datos del Lunes (primer elemento del array)
    const lunesData = horariosDemo[0];

    // Actualizar todos los días excepto el Lunes
    for (let i = 1; i < horariosDemo.length; i++) {
      horariosDemo[i] = {
        ...horariosDemo[i], // Mantener propiedades existentes
        enabled: lunesData.enabled,
        start: lunesData.start,
        end: lunesData.end,
        breaks: [...lunesData.breaks], // Copia del array de breaks
      };
    }

    // Recargar tabla con los nuevos datos
    loadScheduleTable();
  });

  // Botón para ir a servicios
  document.querySelector(".goToServicesBtn")?.addEventListener("click", function (e) {
    e.preventDefault(); // Prevenir envío del formulario

    // Ocultar horarios y mostrar servicios
    document.getElementById("workScheduleContainer").classList.add("d-none");
    document.getElementById("servicesContainer").classList.remove("d-none");

    // Opcional: Scroll al inicio de la sección
    window.scrollTo({ top: 0, behavior: "smooth" });
  });

  // Si necesitas volver atrás (opcional)
  document.querySelector(".backToScheduleBtn")?.addEventListener("click", function () {
    document.getElementById("servicesContainer").classList.add("d-none");
    document.getElementById("workScheduleContainer").classList.remove("d-none");
    window.scrollTo({ top: 0, behavior: "smooth" });
  });
}

// Función para agregar categoría de ejemplo
function addExampleCategory() {
  // 1. Encontrar la primera fila de servicio
  const serviceRow = document.querySelector("tr.service-row");

  if (!serviceRow) return;

  // 2. Crear fila de categoría
  const categoryRow = document.createElement("tr");
  categoryRow.className = "category-item body-table";

  // Datos random de ejemplo
  const randomNames = ["Corte Básico", "Premium", "Express", "Estándar"];
  const randomDesc = ["Incluye lo esencial", "Servicio completo", "Entrega rápida", "Paquete regular"];
  const randomData = {
    name: randomNames[0],
    desc: randomDesc[0],
  };

  // 3. HTML de la categoría (simplificado)
  categoryRow.innerHTML = `
    <td></td>
    <td class="text-center">CATEGORÍA</td>
    <td>
      <input type="text" class="form-control" 
             value="${randomData.name}" 
             placeholder="Nombre">
    </td>
    <td>
      <textarea class="form-control" 
                placeholder="Descripción">${randomData.desc}</textarea>
    </td>
    <td>
      <button type="button" class="btn btn-sm btn-outline-danger">×</button>
    </td>
  `;

  // 4. Insertar después del servicio
  serviceRow.parentNode.insertBefore(categoryRow, serviceRow.nextSibling);

  // 5. Evento para eliminar (opcional)
  categoryRow.querySelector("button").addEventListener("click", () => {
    categoryRow.remove();
  });
}

// Asignar evento al botón "+Categoría"
document.querySelector(".add-category")?.addEventListener("click", addExampleCategory);
