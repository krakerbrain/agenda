// Clase para renderizar la tabla de clientes con los estilos de datesList
export class CustomersTableRenderer {
  constructor(tableContentSelector) {
    this.tableContent = document.querySelector(tableContentSelector);
  }

  render(data, getStatusBadge, getActionIcons) {
    if (!Array.isArray(data)) data = [];
    this.tableContent.innerHTML = "";
    data.forEach((customer) => {
      const row = document.createElement("tr");
      row.className = "body-table text-gray-700 hover:bg-cyan-50 transition";
      row.innerHTML = `
        <td data-cell='nombre' class='data px-2 py-2'>
          <a id="customerDetailLink${customer.id}" data-id="${customer.id}" href="#">${customer.name}</a>
        </td>
        <td data-cell='telefono' class='data px-2 py-2'>
          <i class="fab fa-whatsapp pe-1" style="font-size:0.85rem"></i>
          <a href="https://wa.me/${customer.phone}" target="_blank" class="text-cyan-600 hover:text-cyan-800">+${customer.phone}</a>
        </td>
        <td data-cell='correo' class='data px-2 py-2'>${customer.mail}</td>
        <td data-cell='estado' class='data px-2 py-2'>${getStatusBadge({ blocked: customer.blocked, hasIncidents: customer.has_incidents })}</td>
        <td data-cell='acciones' class='data px-2 py-2'>
          <div class="actionBtns flex justify-evenly gap-2">${getActionIcons(customer.id, customer.blocked)}</div>
        </td>
        <td class="expand-btn">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
          </svg>
        </td>
      `;
      this.tableContent.appendChild(row);
    });
    this.updateTableHeaders();
  }

  updateTableHeaders() {
    const headers = `
      <th class="px-2 py-1">Nombre</th>
      <th class="px-2 py-1">Teléfono</th>
      <th class="px-2 py-1">Correo</th>
      <th class="px-2 py-1">Estado</th>
      <th class="px-2 py-1">Acción</th>
    `;
    document.querySelector(".head-table").innerHTML = headers;
  }
}
