// Clase para renderizar la tabla de citas y eventos
import { DateFormatter } from "./DateFormatter.js";
import { DatesUIHelpers } from "./DatesUIHelpers.js";

export class DatesTableRenderer {
  constructor(tableContentSelector) {
    this.tableContent = document.querySelector(tableContentSelector);
  }

  renderAppointments(data, showProviderColumn, getActionButtons) {
    if (!Array.isArray(data)) data = [];
    this.tableContent.innerHTML = "";
    data.forEach((item) => {
      const row = document.createElement("tr");
      row.className = "body-table appointments-row text-gray-700 hover:bg-cyan-50 transition";
      row.innerHTML = `
        <td data-cell='servicio' class='data cell-service px-2 py-2'>${item.service}</td>
        <td data-cell='categoría' class='data cell-category px-2 py-2'>${item.category}</td>
        <td data-cell='nombre' class='data cell-name px-2 py-2'>${item.name}</td>
        <td data-cell='teléfono' class='data cell-phone px-2 py-2' nowrap>
          <i class="fab fa-whatsapp text-green-500" style="font-size:0.85rem"></i>
          <a href="https://wa.me/${item.phone}" target="_blank" class="text-cyan-600 hover:text-cyan-800">+${item.phone}</a>
        </td>
        <td data-cell='correo' class='data cell-mail px-2 py-2'>
          <a href="mailto:${item.mail}" class="text-cyan-600 hover:text-cyan-800">${item.mail}</a>
        </td>
        ${showProviderColumn ? `<td data-cell='prestador' class='data cell-provider px-2 py-2'>${item.provider_name}</td>` : ""}
        <td data-cell='fecha' class='data cell-date px-2 py-2'>
          ${DateFormatter.formatDate(item.date)}
          <span class='inline-block ms-4 text-gray-500 md:hidden'>${DateFormatter.formatTimeTo12h(item.start_time)}</span>
        </td>
        <td data-cell='hora' class='data cell-time px-2 py-2 hidden md:table-cell' nowrap>${DateFormatter.formatTimeTo12h(item.start_time)}</td>
        <td data-cell='estado' class='data cell-status px-2 py-2 w-32'>
          ${DatesUIHelpers.renderBadges(
            DatesUIHelpers.getStatusBadges({
              status: item.status,
              abono_badge: item.abono_badge,
            })
          )}
        </td>
        <td data-cell='acciones' class="cell-actions px-2 py-2"><div class="actionBtns flex justify-evenly gap-2">${getActionButtons(item.status, item.id_appointment)}</div></td>
        <td class="expand-btn">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
          </svg>
        </td>
      `;
      // Evento expandir/colapsar
      const expandBtn = row.querySelector(".expand-btn");
      expandBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        row.classList.toggle("expanded");
      });
      this.tableContent.appendChild(row);
    });
    this.updateTableHeaders(showProviderColumn);
  }

  updateTableHeaders(showProviderColumn = false) {
    const headers = `
      <th class="px-2 py-1">Servicio</th>
      <th class="px-2 py-1">Categoria</th>
      <th class="px-2 py-1">Nombre</th>
      <th class="px-2 py-1">Teléfono</th>
      <th class="px-2 py-1">Correo</th>
      ${showProviderColumn ? "<th class='px-2 py-1'>Prestador</th>" : ""}
      <th class="px-2 py-1">Fecha</th>
      <th class="px-2 py-1">Hora</th>
      <th class="px-2 py-1">Estado</th>
      <th class="px-2 py-1">Acción</th>
    `;
    document.querySelector(".head-table").innerHTML = headers;
  }

  renderEventTable(data, getStatusBadge, getActionButtons) {
    this.updateTableHeaders(false); // fuerza encabezado estándar sin prestador
    this.tableContent.innerHTML = "";
    data.forEach((event) => {
      const row = document.createElement("tr");
      row.className = "body-table";
      row.innerHTML = `
        <td data-cell="servicio" class="data px-2 py-2">${event.event_name}</td>
        <td data-cell="categoria" class="data px-2 py-2">-</td>
        <td data-cell="nombre" class="data px-2 py-2">${event.participant_name}</td>
        <td data-cell="telefono" class="data px-2 py-2"><i class="fab fa-whatsapp pe-1 text-green-500" style="font-size:0.85rem"></i><a href="https://wa.me/${
          event.phone
        }" target="_blank" class="text-cyan-600 hover:text-cyan-800">+${event.phone}</a></td>
        <td data-cell="correo" class="data px-2 py-2">${event.email}</td>
        <td data-cell="fecha" class="data px-2 py-2">${event.event_date}</td>
                <td data-cell='hora' class='data px-2 py-2 hidden md:table-cell'>${DateFormatter.formatTimeTo12h(event.event_start_time)}</td>
        <td data-cell='estado' class='data px-2 py-2'>${getStatusBadge(event.status)}</td>
        <td class="px-2 py-2"><div class="actionBtns flex justify-evenly gap-2">${getActionButtons(event.status, event.inscription_id, "event")}</div></td>
        <td class="expand-btn">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
          </svg>
        </td>
      `;
      this.tableContent.appendChild(row);
    });
    // Add listeners for confirm and delete buttons
    data.forEach((event_list) => {
      const confirmarBtn = document.getElementById(`confirmarBtn${event_list.inscription_id}`);
      const eliminarBtn = document.getElementById(`eliminarBtn${event_list.inscription_id}`);
      if (confirmarBtn) {
        confirmarBtn.addEventListener("click", function () {
          const type = confirmarBtn.getAttribute("data-type");
          window.confirmReservation(event_list.inscription_id, type);
        });
      }
      if (eliminarBtn) {
        eliminarBtn.addEventListener("click", function () {
          window.deleteEvent(event_list.inscription_id);
        });
      }
    });
  }
}
