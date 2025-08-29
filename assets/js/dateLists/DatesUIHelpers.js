export class DatesUIHelpers {
  static baseBadgeClass = "w-full px-2 py-1 rounded text-xs";

  // Devuelve un array de badges de estado de cita
  static getStatusBadges({ status, abono_badge }) {
    const badges = [];

    const statusMap = {
      0: { text: "Pendiente", class: "bg-yellow-100 text-yellow-800" },
      1: { text: "Confirmada", class: "bg-green-100 text-green-800" },
    };

    // Badge de estado principal
    const statusInfo = statusMap[status] || {
      text: "Desconocido",
      class: "bg-gray-200 text-gray-700",
    };
    badges.push(`<span class="${this.baseBadgeClass} ${statusInfo.class}">${statusInfo.text}</span>`);

    // Badge de abono (ya viene desde el backend)
    if (abono_badge && abono_badge !== "") {
      let abonoClass = "bg-gray-100 text-gray-800"; // default
      if (abono_badge.includes("24h")) abonoClass = "bg-red-100 text-blue-800";
      if (abono_badge.includes("48h")) abonoClass = "bg-red-500 text-white";
      badges.push(`<span class="${this.baseBadgeClass} ${abonoClass}">${abono_badge}</span>`);
    }

    return badges;
  }

  // Renderizador de badges → siempre usa un <div> contenedor
  static renderBadges(badges) {
    const containerClass = badges.length > 1 ? "flex flex-col gap-1 text-left md:text-center" : "flex text-left md:text-center";
    return `<div class="${containerClass}">${badges.join("")}</div>`;
  }

  // Badge para estado de clientes
  static getCustomerStatusBadge({ blocked, hasIncidents }) {
    const base = "w-full px-2 py-1 rounded text-xs font-semibold";
    if (blocked) {
      return `<span class="${base} bg-red-100 text-red-800">Bloqueado</span>`;
    }
    if (hasIncidents) {
      return `<span class="${base} bg-orange-100 text-orange-800">Incidencia</span>`;
    }
    return `<span class="${base} bg-green-100 text-green-800">Activo</span>`;
  }
}
