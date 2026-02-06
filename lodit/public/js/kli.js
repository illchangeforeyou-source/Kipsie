// Initialize data (kosong, akan diisi melalui form)
        let doctors = [];
        let appointments = [];

        // Load data from localStorage if available
        function loadData() {
            const savedDoctors = localStorage.getItem('doctors');
            const savedAppointments = localStorage.getItem('appointments');
            
            if (savedDoctors) {
                doctors = JSON.parse(savedDoctors);
            }
            
            if (savedAppointments) {
                appointments = JSON.parse(savedAppointments);
            }
        }

        // Save data to localStorage
        function saveData() {
            localStorage.setItem('doctors', JSON.stringify(doctors));
            localStorage.setItem('appointments', JSON.stringify(appointments));
        }

        // Show toast notification
        function showToast(message, type = 'success') {
            const toastEl = document.getElementById('liveToast');
            const toastMessage = document.getElementById('toastMessage');
            const toastHeader = toastEl.querySelector('.toast-header');
            
            toastMessage.textContent = message;
            
            // Set header color based on type
            toastHeader.className = 'toast-header';
            if (type === 'success') {
                toastHeader.classList.add('bg-success', 'text-white');
            } else if (type === 'error') {
                toastHeader.classList.add('bg-danger', 'text-white');
            } else if (type === 'warning') {
                toastHeader.classList.add('bg-warning');
            }
            
            const toast = new bootstrap.Toast(toastEl);
            toast.show();
        }

        // Populate doctor select options
        function populateDoctorSelect() {
            const doctorSelect = document.getElementById('appointmentDoctorSelect');
            doctorSelect.innerHTML = '<option value="">-- Pilih Dokter --</option>';
            
            doctors.forEach(doctor => {
                const option = document.createElement('option');
                option.value = doctor.id;
                option.textContent = `${doctor.name} - ${doctor.specialization}`;
                doctorSelect.appendChild(option);
            });
        }

        // Render doctors table
        function renderDoctorsTable() {
            const table = document.getElementById('doctorsTable');
            table.innerHTML = '';
            
            if (doctors.length === 0) {
                table.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-1"></i>
                            <p class="mt-2">Belum ada data dokter</p>
                            <small>Klik tombol "Tambah Dokter" untuk menambah data</small>
                        </td>
                    </tr>
                `;
                return;
            }
            
            doctors.forEach(doctor => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td><span class="badge bg-primary">${doctor.id}</span></td>
                    <td>
                        <img src="${doctor.photo}" alt="${doctor.name}" width="50" height="50" 
                             class="rounded-circle border" onerror="this.src='https://picsum.photos/seed/default/50/50'">
                    </td>
                    <td><strong>${doctor.name}</strong></td>
                    <td>${doctor.specialization}</td>
                    <td>
                        <span class="badge bg-info">${doctor.experience} tahun</span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-warning edit-doctor" data-id="${doctor.id}" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-danger delete-doctor" data-id="${doctor.id}" title="Hapus">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                `;
                table.appendChild(row);
            });
            
            // Add event listeners for edit and delete buttons
            document.querySelectorAll('.edit-doctor').forEach(button => {
                button.addEventListener('click', function() {
                    const id = parseInt(this.getAttribute('data-id'));
                    editDoctor(id);
                });
            });
            
            document.querySelectorAll('.delete-doctor').forEach(button => {
                button.addEventListener('click', function() {
                    const id = parseInt(this.getAttribute('data-id'));
                    deleteDoctor(id);
                });
            });
        }

        // Render appointments table
        function renderAppointmentsTable() {
            const table = document.getElementById('appointmentsTable');
            table.innerHTML = '';
            
            if (appointments.length === 0) {
                table.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-1"></i>
                            <p class="mt-2">Belum ada data janji temu</p>
                            <small>Klik tombol "Tambah Janji Temu" untuk menambah data</small>
                        </td>
                    </tr>
                `;
                return;
            }
            
            appointments.forEach(appointment => {
                const row = document.createElement('tr');
                let statusBadge = '';
                
                if (appointment.status === 'confirmed') {
                    statusBadge = '<span class="badge bg-success">✓ Confirm</span>';
                } else if (appointment.status === 'pending') {
                    statusBadge = '<span class="badge bg-warning">⏱ Waiting...</span>';
                } else if (appointment.status === 'cancelled') {
                    statusBadge = '<span class="badge bg-danger">✗ Cancel</span>';
                } else {
                    statusBadge = '<span class="badge bg-info">✔ Finished</span>';
                }
                
                row.innerHTML = `
                    <td><span class="badge bg-secondary">${appointment.id}</span></td>
                    <td><strong>${appointment.patientName}</strong></td>
                    <td>${appointment.phone}</td>
                    <td>${appointment.doctorName}</td>
                    <td>${appointment.date}</td>
                    <td>${statusBadge}</td>
                    <td>
                        <button class="btn btn-sm btn-info view-appointment" data-id="${appointment.id}" title="Lihat Detail">
                            <i class="bi bi-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-success confirm-appointment" data-id="${appointment.id}" title="Konfirmasi">
                            <i class="bi bi-check"></i>
                        </button>
                        <button class="btn btn-sm btn-danger delete-appointment" data-id="${appointment.id}" title="Hapus">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                `;
                table.appendChild(row);
            });
            
            // Add event listeners for view, confirm and delete buttons
            document.querySelectorAll('.view-appointment').forEach(button => {
                button.addEventListener('click', function() {
                    const id = parseInt(this.getAttribute('data-id'));
                    viewAppointment(id);
                });
            });
            
            document.querySelectorAll('.confirm-appointment').forEach(button => {
                button.addEventListener('click', function() {
                    const id = parseInt(this.getAttribute('data-id'));
                    confirmAppointment(id);
                });
            });
            
            document.querySelectorAll('.delete-appointment').forEach(button => {
                button.addEventListener('click', function() {
                    const id = parseInt(this.getAttribute('data-id'));
                    deleteAppointment(id);
                });
            });
        }

        // CRUD Functions for Doctors
        function addDoctor(doctor) {
            const newId = doctors.length > 0 ? Math.max(...doctors.map(d => d.id)) + 1 : 1;
            doctor.id = newId;
            if (!doctor.photo) {
                doctor.photo = `https://picsum.photos/seed/doctor${newId}/300/300`;
            }
            doctors.push(doctor);
            saveData();
            renderDoctorsTable();
            populateDoctorSelect();
            showToast('Dokter berhasil ditambahkan!', 'success');
        }

        function editDoctor(id) {
            const doctor = doctors.find(d => d.id === id);
            if (doctor) {
                document.getElementById('doctorId').value = doctor.id;
                document.getElementById('doctorName').value = doctor.name;
                document.getElementById('doctorSpecialization').value = doctor.specialization;
                document.getElementById('doctorExperience').value = doctor.experience;
                document.getElementById('doctorPhoto').value = doctor.photo;
                
                document.getElementById('doctorModalTitle').innerHTML = '<i class="bi bi-pencil"></i> Edit Dokter';
                const doctorModal = new bootstrap.Modal(document.getElementById('doctorModal'));
                doctorModal.show();
            }
        }

        function updateDoctor(id, updatedDoctor) {
            const index = doctors.findIndex(d => d.id === id);
            if (index !== -1) {
                doctors[index] = { ...doctors[index], ...updatedDoctor };
                saveData();
                renderDoctorsTable();
                populateDoctorSelect();
                showToast('Data dokter berhasil diperbarui!', 'success');
            }
        }

        function deleteDoctor(id) {
            if (confirm('Apakah Anda yakin ingin menghapus dokter ini?')) {
                doctors = doctors.filter(d => d.id !== id);
                saveData();
                renderDoctorsTable();
                populateDoctorSelect();
                showToast('Dokter berhasil dihapus!', 'warning');
            }
        }

        // CRUD Functions for Appointments
        function addAppointment(appointment) {
            const newId = appointments.length > 0 ? Math.max(...appointments.map(a => a.id)) + 1 : 1;
            appointment.id = newId;
            appointments.push(appointment);
            saveData();
            renderAppointmentsTable();
            showToast('Janji temu berhasil ditambahkan!', 'success');
        }

        function viewAppointment(id) {
            const appointment = appointments.find(a => a.id === id);
            if (appointment) {
                const detailContainer = document.getElementById('appointmentDetail');
                detailContainer.innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong>ID Janji Temu:</strong>
                                <span class="badge bg-secondary">${appointment.id}</span>
                            </div>
                            <div class="mb-3">
                                <strong>Nama Pasien:</strong><br>
                                ${appointment.patientName}
                            </div>
                            <div class="mb-3">
                                <strong>Telepon:</strong><br>
                                <i class="bi bi-telephone"></i> ${appointment.phone}
                            </div>
                            <div class="mb-3">
                                <strong>Email:</strong><br>
                                <i class="bi bi-envelope"></i> ${appointment.email}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong>Dokter:</strong><br>
                                <i class="bi bi-person-badge"></i> ${appointment.doctorName}
                            </div>
                            <div class="mb-3">
                                <strong>Tanggal:</strong><br>
                                <i class="bi bi-calendar"></i> ${appointment.date}
                            </div>
                            <div class="mb-3">
                                <strong>Keluhan:</strong><br>
                                <i class="bi bi-chat-text"></i> ${appointment.complaint}
                            </div>
                            <div class="mb-3">
                                <strong>Status:</strong><br>
                                ${appointment.status === 'confirmed' ? '<span class="badge bg-success">Dikonfirmasi</span>' : 
                                  appointment.status === 'pending' ? '<span class="badge bg-warning">Menunggu</span>' :
                                  appointment.status === 'cancelled' ? '<span class="badge bg-danger">Dibatalkan</span>' :
                                  '<span class="badge bg-info">Selesai</span>'}
                            </div>
                        </div>
                    </div>
                `;
                
                const appointmentDetailModal = new bootstrap.Modal(document.getElementById('appointmentDetailModal'));
                appointmentDetailModal.show();
            }
        }

        function confirmAppointment(id) {
            const appointment = appointments.find(a => a.id === id);
            if (appointment) {
                appointment.status = 'confirmed';
                saveData();
                renderAppointmentsTable();
                showToast('Janji temu dikonfirmasi!', 'success');
            }
        }

        function deleteAppointment(id) {
            if (confirm('Apakah Anda yakin ingin menghapus janji temu ini?')) {
                appointments = appointments.filter(a => a.id !== id);
                saveData();
                renderAppointmentsTable();
                showToast('Janji temu dihapus!', 'warning');
            }
        }

        // Event Listeners
        document.addEventListener('DOMContentLoaded', function() {
            loadData();
            renderDoctorsTable();
            renderAppointmentsTable();
            populateDoctorSelect();
            
            // Set minimum date to today for appointment date
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('appointmentDate').setAttribute('min', today);
            
            // Doctor form submission
            document.getElementById('saveDoctor').addEventListener('click', function() {
                const doctorId = document.getElementById('doctorId').value;
                const doctor = {
                    name: document.getElementById('doctorName').value,
                    specialization: document.getElementById('doctorSpecialization').value,
                    experience: parseInt(document.getElementById('doctorExperience').value),
                    photo: document.getElementById('doctorPhoto').value
                };
                
                if (doctorId) {
                    updateDoctor(parseInt(doctorId), doctor);
                } else {
                    addDoctor(doctor);
                }
                
                const doctorModal = bootstrap.Modal.getInstance(document.getElementById('doctorModal'));
                doctorModal.hide();
                
                // Reset form
                document.getElementById('doctorForm').reset();
                document.getElementById('doctorId').value = '';
                document.getElementById('doctorModalTitle').innerHTML = '<i class="bi bi-person-plus"></i> Tambah Dokter';
            });
            
            // Appointment form submission
            document.getElementById('saveAppointment').addEventListener('click', function() {
                const doctorId = parseInt(document.getElementById('appointmentDoctorSelect').value);
                const doctor = doctors.find(d => d.id === doctorId);
                
                const appointment = {
                    patientName: document.getElementById('patientName').value,
                    phone: document.getElementById('patientPhone').value,
                    email: document.getElementById('patientEmail').value,
                    doctorId: doctorId,
                    doctorName: doctor ? doctor.name : '',
                    date: document.getElementById('appointmentDate').value,
                    complaint: document.getElementById('patientComplaint').value,
                    status: document.getElementById('appointmentStatus').value
                };
                
                addAppointment(appointment);
                
                const appointmentModal = bootstrap.Modal.getInstance(document.getElementById('appointmentModal'));
                appointmentModal.hide();
                
                // Reset form
                document.getElementById('appointmentForm').reset();
            });
            
            // Export appointments data
            document.getElementById('exportAppointments').addEventListener('click', function() {
                const dataStr = JSON.stringify(appointments, null, 2);
                const dataUri = 'data:application/json;charset=utf-8,'+ encodeURIComponent(dataStr);
                
                const exportFileDefaultName = `appointments_${new Date().toISOString().split('T')[0]}.json`;

                
                const linkElement = document.createElement('a');
                linkElement.setAttribute('href', dataUri);
                linkElement.setAttribute('download', exportFileDefaultName);
                linkElement.click();
                
                showToast('Data berhasil diekspor!', 'success');
            });
            
            // Reset doctor modal when hidden
            document.getElementById('doctorModal').addEventListener('hidden.bs.modal', function() {
                document.getElementById('doctorForm').reset();
                document.getElementById('doctorId').value = '';
                document.getElementById('doctorModalTitle').innerHTML = '<i class="bi bi-person-plus"></i> Tambah Dokter';
            });
            
            // Reset appointment modal when hidden
            document.getElementById('appointmentModal').addEventListener('hidden.bs.modal', function() {
                document.getElementById('appointmentForm').reset();
            });
        });

        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));

    if (target) {
        target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                });
            }
        });
    });

        window.addEventListener('scroll', function() {
        let sections = document.querySelectorAll('section');
        let navLinks = document.querySelectorAll('.navbar-nav .nav-link');
            
        sections.forEach(section => {
                let top = section.offsetTop - 100;
                let height = section.offsetHeight;
                let id = section.getAttribute('id');
                
    if(window.scrollY >= top && window.scrollY < top + height) {
                navLinks.forEach(link => {
                    link.classList.remove('active');

    if(link.getAttribute('href') === '#' + id) {
                    link.classList.add('active');
                    }
                });
            }
        });
    });

    document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Thank you, we will contact you soon.');
            form.reset();
            });
        });


// Initialize data (kosong, akan diisi melalui form)
        let doctors = [];
        let appointments = [];

        // Load data from localStorage if available
        function loadData() {
            const savedDoctors = localStorage.getItem('doctors');
            const savedAppointments = localStorage.getItem('appointments');
            
            if (savedDoctors) {
                doctors = JSON.parse(savedDoctors);
            }
            
            if (savedAppointments) {
                appointments = JSON.parse(savedAppointments);
            }
        }

        // Save data to localStorage
        function saveData() {
            localStorage.setItem('doctors', JSON.stringify(doctors));
            localStorage.setItem('appointments', JSON.stringify(appointments));
        }

        // Show toast notification
        function showToast(message, type = 'success') {
            const toastEl = document.getElementById('liveToast');
            const toastMessage = document.getElementById('toastMessage');
            const toastHeader = toastEl.querySelector('.toast-header');
            
            toastMessage.textContent = message;
            
            // Set header color based on type
            toastHeader.className = 'toast-header';
            if (type === 'success') {
                toastHeader.classList.add('bg-success', 'text-white');
            } else if (type === 'error') {
                toastHeader.classList.add('bg-danger', 'text-white');
            } else if (type === 'warning') {
                toastHeader.classList.add('bg-warning');
            }
            
            const toast = new bootstrap.Toast(toastEl);
            toast.show();
        }

        // Populate doctor select options
        function populateDoctorSelect() {
            const doctorSelect = document.getElementById('appointmentDoctorSelect');
            doctorSelect.innerHTML = '<option value="">-- Pilih Dokter --</option>';
            
            doctors.forEach(doctor => {
                const option = document.createElement('option');
                option.value = doctor.id;
                option.textContent = `${doctor.name} - ${doctor.specialization}`;
                doctorSelect.appendChild(option);
            });
        }

        // Render doctors table
        function renderDoctorsTable() {
            const table = document.getElementById('doctorsTable');
            table.innerHTML = '';
            
            if (doctors.length === 0) {
                table.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-1"></i>
                            <p class="mt-2">Belum ada data dokter</p>
                            <small>Klik tombol "Tambah Dokter" untuk menambah data</small>
                        </td>
                    </tr>
                `;
                return;
            }
            
            doctors.forEach(doctor => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td><span class="badge bg-primary">${doctor.id}</span></td>
                    <td>
                        <img src="${doctor.photo}" alt="${doctor.name}" width="50" height="50" 
                             class="rounded-circle border" onerror="this.src='https://picsum.photos/seed/default/50/50'">
                    </td>
                    <td><strong>${doctor.name}</strong></td>
                    <td>${doctor.specialization}</td>
                    <td>
                        <span class="badge bg-info">${doctor.experience} tahun</span>
                    </td>
                    <td>
                        <button class="btn btn-outline-warning edit-doctor" data-id="${doctor.id}" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-outline-danger delete-doctor" data-id="${doctor.id}" title="Hapus">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                `;
                table.appendChild(row);
            });
            
            // Add event listeners for edit and delete buttons
            document.querySelectorAll('.edit-doctor').forEach(button => {
                button.addEventListener('click', function() {
                    const id = parseInt(this.getAttribute('data-id'));
                    editDoctor(id);
                });
            });
            
            document.querySelectorAll('.delete-doctor').forEach(button => {
                button.addEventListener('click', function() {
                    const id = parseInt(this.getAttribute('data-id'));
                    deleteDoctor(id);
                });
            });
        }

        // Render appointments table
        function renderAppointmentsTable() {
            const table = document.getElementById('appointmentsTable');
            table.innerHTML = '';
            
            if (appointments.length === 0) {
                table.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-1"></i>
                            <p class="mt-2">Belum ada data janji temu</p>
                            <small>Klik tombol "Tambah Janji Temu" untuk menambah data</small>
                        </td>
                    </tr>
                `;
                return;
            }
            
            appointments.forEach(appointment => {
                const row = document.createElement('tr');
                let statusBadge = '';
                
                if (appointment.status === 'confirmed') {
                    statusBadge = '<span class="badge bg-success">✓ Dikonfirmasi</span>';
                } else if (appointment.status === 'pending') {
                    statusBadge = '<span class="badge bg-warning">⏱ Menunggu</span>';
                } else if (appointment.status === 'cancelled') {
                    statusBadge = '<span class="badge bg-danger">✗ Dibatalkan</span>';
                } else {
                    statusBadge = '<span class="badge bg-info">✔ Selesai</span>';
                }
                
                row.innerHTML = `
                    <td><span class="badge bg-secondary">${appointment.id}</span></td>
                    <td><strong>${appointment.patientName}</strong></td>
                    <td>${appointment.phone}</td>
                    <td>${appointment.doctorName}</td>
                    <td>${appointment.date}</td>
                    <td>${appointment.keluhan}</td>
                    <td>${statusBadge}</td>
                    <td>


                        <button class="btn btn-outline-warning edit-appointment" data-id="${appointment.id}" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </button>

                        <button class="btn btn-outline-info view-appointment" data-id="${appointment.id}" title="Lihat Detail">
                            <i class="bi bi-eye"></i>
                        </button>

                        <button class="btn btn-outline-success confirm-appointment" data-id="${appointment.id}" title="Konfirmasi">
                            <i class="bi bi-check"></i>
                        </button>

                        <button class="btn btn-outline-danger delete-appointment" data-id="${appointment.id}" title="Hapus">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                `;
                table.appendChild(row);
            });
            
            // Add event listeners for view, confirm and delete buttons

            document.querySelectorAll('.edit-appointment').forEach(button => {
                button.addEventListener('click', function() {
                    const id = parseInt(this.getAttribute('data-id'));
                    viewAppointment(id);
                });
            });
    
            document.querySelectorAll('.view-appointment').forEach(button => {
                button.addEventListener('click', function() {
                    const id = parseInt(this.getAttribute('data-id'));
                    viewAppointment(id);
                });
            });
            
            document.querySelectorAll('.confirm-appointment').forEach(button => {
                button.addEventListener('click', function() {
                    const id = parseInt(this.getAttribute('data-id'));
                    confirmAppointment(id);
                });
            });
            
            document.querySelectorAll('.delete-appointment').forEach(button => {
                button.addEventListener('click', function() {
                    const id = parseInt(this.getAttribute('data-id'));
                    deleteAppointment(id);
                });
            });
        }

        // CRUD Functions for Doctors
        function addDoctor(doctor) {
            const newId = doctors.length > 0 ? Math.max(...doctors.map(d => d.id)) + 1 : 1;
            doctor.id = newId;
            if (!doctor.photo) {
                doctor.photo = `https://picsum.photos/seed/doctor${newId}/300/300`;

            }
            doctors.push(doctor);
            saveData();
            renderDoctorsTable();
            populateDoctorSelect();
            showToast('Dokter berhasil ditambahkan!', 'success');
        }

        function editDoctor(id) {
            const doctor = doctors.find(d => d.id === id);
            if (doctor) {
                document.getElementById('doctorId').value = doctor.id;
                document.getElementById('doctorName').value = doctor.name;
                document.getElementById('doctorSpecialization').value = doctor.specialization;
                document.getElementById('doctorExperience').value = doctor.experience;
                document.getElementById('doctorPhoto').value = doctor.photo;
                
                document.getElementById('doctorModalTitle').innerHTML = '<i class="bi bi-pencil"></i> Edit Dokter';
                const doctorModal = new bootstrap.Modal(document.getElementById('doctorModal'));
                doctorModal.show();
            }
        }

        function updateDoctor(id, updatedDoctor) {
            const index = doctors.findIndex(d => d.id === id);
            if (index !== -1) {
                doctors[index] = { ...doctors[index], ...updatedDoctor };
                saveData();
                renderDoctorsTable();
                populateDoctorSelect();
                showToast('Data dokter berhasil diperbarui!', 'success');
            }
        }

        function deleteDoctor(id) {
            if (confirm('Apakah Anda yakin ingin menghapus dokter ini?')) {
                doctors = doctors.filter(d => d.id !== id);
                saveData();
                renderDoctorsTable();
                populateDoctorSelect();
                showToast('Dokter berhasil dihapus!', 'warning');
            }
        }

        // CRUD Functions for Appointments
        function addAppointment(appointment) {
            const newId = appointments.length > 0 ? Math.max(...appointments.map(a => a.id)) + 1 : 1;
            appointment.id = newId;
            appointments.push(appointment);
            saveData();
            renderAppointmentsTable();
            showToast('Janji temu berhasil ditambahkan!', 'success');
        }

        function viewAppointment(id) {
            const appointment = appointments.find(a => a.id === id);
            if (appointment) {
                const detailContainer = document.getElementById('appointmentDetail');
                detailContainer.innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong>ID Janji Temu:</strong>
                                <span class="badge bg-secondary">${appointment.id}</span>
                            </div>
                            <div class="mb-3">
                                <strong>Nama Pasien:</strong><br>
                                ${appointment.patientName}
                            </div>
                            <div class="mb-3">
                                <strong>Telepon:</strong><br>
                                <i class="bi bi-telephone"></i> ${appointment.phone}
                            </div>
                            <div class="mb-3">
                                <strong>Email:</strong><br>
                                <i class="bi bi-envelope"></i> ${appointment.email}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong>Dokter:</strong><br>
                                <i class="bi bi-person-badge"></i> ${appointment.doctorName}
                            </div>
                            <div class="mb-3">
                                <strong>Tanggal:</strong><br>
                                <i class="bi bi-calendar"></i> ${appointment.date}
                            </div>
                            <div class="mb-3">
                                <strong>Keluhan:</strong><br>
                                <i class="bi bi-chat-text"></i> ${appointment.complaint}
                            </div>
                            <div class="mb-3">
                                <strong>Status:</strong><br>
                                ${appointment.status === 'confirmed' ? '<span class="badge bg-success">Dikonfirmasi</span>' : 
                                  appointment.status === 'pending' ? '<span class="badge bg-warning">Menunggu</span>' :
                                  appointment.status === 'cancelled' ? '<span class="badge bg-danger">Dibatalkan</span>' :
                                  '<span class="badge bg-info">Selesai</span>'}
                            </div>
                        </div>
                    </div>
                `;
                
                const appointmentDetailModal = new bootstrap.Modal(document.getElementById('appointmentDetailModal'));
                appointmentDetailModal.show();
            }
        }

        function confirmAppointment(id) {
            const appointment = appointments.find(a => a.id === id);
            if (appointment) {
                appointment.status = 'confirmed';
                saveData();
                renderAppointmentsTable();
                showToast('Janji temu dikonfirmasi!', 'success');
            }
        }

        function deleteAppointment(id) {
            if (confirm('Apakah Anda yakin ingin menghapus janji temu ini?')) {
                appointments = appointments.filter(a => a.id !== id);
                saveData();
                renderAppointmentsTable();
                showToast('Janji temu dihapus!', 'warning');
            }
        }

         function editAppointment(id) {
            const appointments = appointments.find(d => d.id === id);
            if (appointments) {
                document.getElementById('patientName').value =  appointments.name;
                document.getElementById('phone').value =  appointments.phone;
                document.getElementById('email').value =  appointments.email;
                document.getElementById('doctorName').value =  appointmentsdoctorName;
                document.getElementById('date').value =  appointments.date;
                document.getElementById('complaint').value =  appointments.complaint;

                document.getElementById('appointmentsModalTitle').innerHTML = '<i class="bi bi-pencil"></i> Edit appointments';
                const appointmentsModal = new bootstrap.Modal(document.getElementById('appointmentsModal'));
                appointmentsModal.show();
            }
        }

        // Event Listeners
        document.addEventListener('DOMContentLoaded', function() {
            loadData();
            renderDoctorsTable();
            renderAppointmentsTable();
            populateDoctorSelect();
            
            // Set minimum date to today for appointment date
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('appointmentDate').setAttribute('min', today);
            
            // Doctor form submission
            document.getElementById('saveDoctor').addEventListener('click', function() {
                const doctorId = document.getElementById('doctorId').value;
                const doctor = {
                    name: document.getElementById('doctorName').value,
                    specialization: document.getElementById('doctorSpecialization').value,
                    experience: parseInt(document.getElementById('doctorExperience').value),
                    photo: document.getElementById('doctorPhoto').value
                };
                
                if (doctorId) {
                    updateDoctor(parseInt(doctorId), doctor);
                } else {
                    addDoctor(doctor);
                }
                
                const doctorModal = bootstrap.Modal.getInstance(document.getElementById('doctorModal'));
                doctorModal.hide();
                
                // Reset form
                document.getElementById('doctorForm').reset();
                document.getElementById('doctorId').value = '';
                document.getElementById('doctorModalTitle').innerHTML = '<i class="bi bi-person-plus"></i> Tambah Dokter';
            });
            
            // Appointment form submission
            document.getElementById('saveAppointment').addEventListener('click', function() {
                const doctorId = parseInt(document.getElementById('appointmentDoctorSelect').value);
                const doctor = doctors.find(d => d.id === doctorId);
                
                const appointment = {
                    patientName: document.getElementById('patientName').value,
                    phone: document.getElementById('patientPhone').value,
                    email: document.getElementById('patientEmail').value,
                    doctorId: doctorId,
                    doctorName: doctor ? doctor.name : '',
                    date: document.getElementById('appointmentDate').value,
                    complaint: document.getElementById('patientComplaint').value,
                    status: document.getElementById('appointmentStatus').value
                };
                
                addAppointment(appointment);
                
                const appointmentModal = bootstrap.Modal.getInstance(document.getElementById('appointmentModal'));
                appointmentModal.hide();
                
                // Reset form
                document.getElementById('appointmentForm').reset();
            });
            
            // Export appointments data
            document.getElementById('exportAppointments').addEventListener('click', function() {
                const dataStr = JSON.stringify(appointments, null, 2);
                const dataUri = 'data:application/json;charset=utf-8,'+ encodeURIComponent(dataStr);
                
                const exportFileDefaultName = appointments_${new Date().toISOString().split('T')[0]}.json;
                
                const linkElement = document.createElement('a');
                linkElement.setAttribute('href', dataUri);
                linkElement.setAttribute('download', exportFileDefaultName);
                linkElement.click();
                
                showToast('Data berhasil diekspor!', 'success');
            });
            
            // Reset doctor modal when hidden
            document.getElementById('doctorModal').addEventListener('hidden.bs.modal', function() {
                document.getElementById('doctorForm').reset();
                document.getElementById('doctorId').value = '';
                document.getElementById('doctorModalTitle').innerHTML = '<i class="bi bi-person-plus"></i> Tambah Dokter';
            });
            
            // Reset appointment modal when hidden
            document.getElementById('appointmentModal').addEventListener('hidden.bs.modal', function() {
                document.getElementById('appointmentForm').reset();
            });
        });