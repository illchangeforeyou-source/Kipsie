import { loadData } from "./storage.js";
import { addDoctor, updateDoctor } from "./doctor.js";
import { addAppointment } from "./appointment.js";
import { renderDoctorsTable, renderAppointmentsTable, populateDoctorSelect } from "./ui.js";

// ------------------ INITIALIZE ------------------

document.addEventListener("DOMContentLoaded", () => {
    loadData();
    renderDoctorsTable();
    renderAppointmentsTable();
    populateDoctorSelect();

    // Doctor form submit
    document.getElementById("saveDoctor").onclick = () => {
        const id = document.getElementById("doctorId").value;

        const doctor = {
            name: document.getElementById("doctorName").value,
            specialization: document.getElementById("doctorSpecialization").value,
            experience: parseInt(document.getElementById("doctorExperience").value),
            photo: document.getElementById("doctorPhoto").value
        };

        if (id) updateDoctor(parseInt(id), doctor);
        else addDoctor(doctor);

        bootstrap.Modal.getInstance("#doctorModal").hide();
    };

    // Appointment form submit
    document.getElementById("saveAppointment").onclick = () => {
        const doctorId = parseInt(document.getElementById("appointmentDoctorSelect").value);
        const doctorName = document.querySelector(`#appointmentDoctorSelect option[value="${doctorId}"]`)?.textContent || "";

        const app = {
            patientName: document.getElementById("patientName").value,
            phone: document.getElementById("patientPhone").value,
            email: document.getElementById("patientEmail").value,
            doctorId,
            doctorName,
            date: document.getElementById("appointmentDate").value,
            complaint: document.getElementById("patientComplaint").value,
            status: document.getElementById("appointmentStatus").value
        };

        addAppointment(app);

        bootstrap.Modal.getInstance("#appointmentModal").hide();
    };
});
