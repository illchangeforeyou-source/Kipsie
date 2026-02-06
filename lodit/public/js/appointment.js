import { appointments, doctors, saveData } from "./storage.js";
import { renderAppointmentsTable, showToast } from "./ui.js";

// ------------------ APPOINTMENT CRUD ------------------

export function addAppointment(app) {
    const newId = appointments.length ? Math.max(...appointments.map(a => a.id)) + 1 : 1;
    app.id = newId;

    appointments.push(app);
    saveData();

    renderAppointmentsTable();
    showToast("Janji temu berhasil ditambahkan!", "success");
}

export function confirmAppointment(id) {
    const appointment = appointments.find(a => a.id === id);
    if (!appointment) return;

    appointment.status = "confirmed";
    saveData();

    renderAppointmentsTable();
    showToast("Janji temu dikonfirmasi!", "success");
}

export function deleteAppointment(id) {
    if (!confirm("Hapus janji temu ini?")) return;

    const index = appointments.findIndex(a => a.id === id);
    if (index === -1) return;

    appointments.splice(index, 1);
    saveData();

    renderAppointmentsTable();
    showToast("Janji temu dihapus!", "warning");
}

export function getAppointmentDetail(id) {
    return appointments.find(a => a.id === id);
}
