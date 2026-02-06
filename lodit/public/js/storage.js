export let doctors = [];
export let appointments = [];

export function loadData() {
    doctors = JSON.parse(localStorage.getItem("doctors") || "[]");
    appointments = JSON.parse(localStorage.getItem("appointments") || "[]");
}

export function saveData() {
    localStorage.setItem("doctors", JSON.stringify(doctors));
    localStorage.setItem("appointments", JSON.stringify(appointments));
}
