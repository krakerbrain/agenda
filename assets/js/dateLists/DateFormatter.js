// Clase para formatear fechas y horas
export class DateFormatter {
  static formatDate(dateString) {
    // Formato de entrada: "31-05-2025" (DD-MM-YYYY)
    const [day, month, year] = dateString.split("-");
    const date = new Date(year, month - 1, day);
    if (isNaN(date.getTime())) return dateString;
    const options = { weekday: "long", day: "numeric", month: "numeric", year: "numeric" };
    return new Intl.DateTimeFormat("es-ES", options).format(date);
  }

  static formatTimeTo12h(timeString) {
    if (!timeString) return "";
    const [hour, minute] = timeString.split(":");
    let h = parseInt(hour, 10);
    const ampm = h >= 12 ? "pm" : "am";
    h = h % 12;
    if (h === 0) h = 12;
    return `${h}:${minute} ${ampm}`;
  }
}
