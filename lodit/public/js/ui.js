import { doctors, appointments } from "./storage.js";
import { deleteDoctor } from "./doctor.js";
import { deleteAppointment, confirmAppointment, getAppointmentDetail } from "./appointment.js";

// ------------------ UI HELPER ------------------

export function showToast(message, type = "success") {
    const toastEl = document.getElementById("liveToast");
    const toastMsg = document.getElementById("toastMessage");
    const header = toastEl.querySelector(".toast-header");

    toastMsg.textContent = message;

    header.className = "toast-header";
    header.classList.add(type === "success" ? "bg-success text-white"
                     : type === "error" ? "bg-danger text-white"
                     : "bg-warning");

    new bootstrap.Toast(toastEl).show();
}

// ------------------ RENDER DOCTORS TABLE ------------------

export function renderDoctorsTable() {
    const table = document.getElementById("doctorsTable");
    table.innerHTML = "";

    if (doctors.length === 0) {
        table.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-4">Tidak ada data</td>
            </tr>
        `;
        return;
    }

    doctors.forEach(d => {
        const row = document.createElement("tr");

        row.innerHTML = `
            <td><span class="badge bg-primary">${d.id}</span></td>
            <td><img src="${d.photo}" width="50" height="50" class="rounded-circle"></td>
            <td>${d.name}</td>
            <td>${d.specialization}</td>
            <td><span class="badge bg-info">${d.experience} tahun</span></td>
            <td>
                <button class="btn btn-sm btn-warning edit-doctor" data-id="${d.id}">Edit</button>
                <button class="btn btn-sm btn-danger delete-doctor" data-id="${d.id}">Hapus</button>
            </td>
        `;

        table.appendChild(row);
    });

    // Attach delete listeners
    document.querySelectorAll(".delete-doctor").forEach(btn => {
        btn.onclick = () => deleteDoctor(parseInt(btn.dataset.id));
    });
}

// ------------------ RENDER APPOINTMENTS TABLE ------------------

export function renderAppointmentsTable() {
    const table = document.getElementById("appointmentsTable");
    table.innerHTML = "";

    if (appointments.length === 0) {
        table.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-4">Tidak ada janji temu</td>
            </tr>
        `;
        return;
    }

    appointments.forEach(a => {
        const row = document.createElement("tr");

        const statusBadge =
            a.status === "confirmed" ? `<span class="badge bg-success">Confirm</span>` :
            a.status === "pending"   ? `<span class="badge bg-warning">Pending</span>` :
            a.status === "cancelled" ? `<span class="badge bg-danger">Cancel</span>` :
                                       `<span class="badge bg-info">Done</span>`;

        row.innerHTML = `
            <td>${a.id}</td>
            <td>${a.patientName}</td>
            <td>${a.phone}</td>
            <td>${a.doctorName}</td>
            <td>${a.date}</td>
            <td>${statusBadge}</td>
            <td>
                <button class="btn btn-sm btn-info view-appointment" data-id="${a.id}">View</button>
                <button class="btn btn-sm btn-success confirm-appointment" data-id="${a.id}">Confirm</button>
                <button class="btn btn-sm btn-danger delete-appointment" data-id="${a.id}">Hapus</button>
            </td>
        `;

        table.appendChild(row);
    });

    // Attach actions
    document.querySelectorAll(".confirm-appointment").forEach(btn => {
        btn.onclick = () => confirmAppointment(parseInt(btn.dataset.id));
    });

    document.querySelectorAll(".delete-appointment").forEach(btn => {
        btn.onclick = () => deleteAppointment(parseInt(btn.dataset.id));
    });

    document.querySelectorAll(".view-appointment").forEach(btn => {
        btn.onclick = () => showAppointmentDetail(parseInt(btn.dataset.id));
    });
}

// ------------------ SHOW DETAIL IN MODAL ------------------

export function showAppointmentDetail(id) {
    const a = getAppointmentDetail(id);
    if (!a) return;

    document.getElementById("appointmentDetail").innerHTML = `
        <p><strong>Nama:</strong> ${a.patientName}</p>
        <p><strong>Dokter:</strong> ${a.doctorName}</p>
        <p><strong>Tanggal:</strong> ${a.date}</p>
        <p><strong>Status:</strong> ${a.status}</p>
        <p><strong>Keluhan:</strong> ${a.complaint}</p>
    `;

    new bootstrap.Modal("#appointmentDetailModal").show();
}
