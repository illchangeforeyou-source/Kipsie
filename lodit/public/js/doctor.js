import { doctors, saveData } from "./storage.js";
import { renderDoctorsTable, populateDoctorSelect, showToast } from "./ui.js";

// ------------------ DOCTOR CRUD ------------------

export function addDoctor(doctor) {
    const newId = doctors.length ? Math.max(...doctors.map(d => d.id)) + 1 : 1;
    doctor.id = newId;

    if (!doctor.photo) {
        doctor.photo = `https://picsum.photos/seed/doctor${newId}/300/300`;
    }

    doctors.push(doctor);
    saveData();

    renderDoctorsTable();
    populateDoctorSelect();
    showToast("Dokter berhasil ditambahkan!", "success");
}

export function updateDoctor(id, updated) {
    const index = doctors.findIndex(d => d.id === id);
    if (index === -1) return;

    doctors[index] = { ...doctors[index], ...updated };
    saveData();

    renderDoctorsTable();
    populateDoctorSelect();
    showToast("Data dokter diperbarui!", "success");
}

export function deleteDoctor(id) {
    if (!confirm("Hapus dokter ini?")) return;

    const index = doctors.findIndex(d => d.id === id);
    if (index === -1) return;

    doctors.splice(index, 1);
    saveData();

    renderDoctorsTable();
    populateDoctorSelect();
    showToast("Dokter dihapus!", "warning");
}
