// Clase para helpers de UI y modales
export class DatesUIHelpers {
  static getStatusBadge(status) {
    const statusMap = {
      0: { text: "Pendiente", class: "bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs" },
      1: { text: "Confirmada", class: "bg-green-100 text-green-800 px-2 py-1 rounded text-xs" },
    };
    const statusInfo = statusMap[status] || { text: "Desconocido", class: "bg-gray-200 text-gray-700 px-2 py-1 rounded text-xs" };
    return `<span class="${statusInfo.class}">${statusInfo.text}</span>`;
  }

  // Badge para estado de clientes
  static getCustomerStatusBadge({ blocked, hasIncidents }) {
    if (blocked) {
      return '<span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-semibold">Bloqueado</span>';
    }
    if (hasIncidents) {
      return '<span class="bg-orange-100 text-orange-800 px-2 py-1 rounded text-xs font-semibold">Incidencia</span>';
    }
    return '<span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-semibold">Activo</span>';
  }
}
