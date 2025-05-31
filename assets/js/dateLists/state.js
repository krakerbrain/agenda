// assets/js/state.js
export const appointmentsStore = {
  current: [],
  setAppointments(data) {
    this.current = Array.isArray(data) ? data : [];
  },
  getAppointment(id) {
    return this.current.find((a) => String(a.id_appointment) === String(id));
  },
};
