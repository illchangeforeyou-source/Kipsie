
        document.addEventListener('DOMContentLoaded', function() {

            // --- DATA PALSU (MOCK DATA) ---
            const patients = [
                { id: 'P001', name: 'feli', dob: '1990-05-15', phone: '0812-3456-7890', address: 'bali' },
                { id: 'P002', name: 'Sita', dob: '1985-11-22', phone: '0813-9876-5432', address: 'bali' },
                { id: 'P003', name: 'Budi ', dob: '1992-03-08', phone: '0821-1122-3344', address: 'bali' },
            ];

            const appointments = [
                { time: '08:00', patientId: 'P001', patientName: 'feli', complaint: 'tantrum', status: 'Dikonfirmasi' },
                { time: '09:30', patientId: 'P002', patientName: 'Sita', complaint: 'demam', status: 'Menunggu' },
                { time: '11:00', patientId: 'P003', patientName: 'Budi ', complaint: 'Keluhan dada', status: 'Dikonfirmasi' },
            ];

            const patientHistory = {
                'P001': [
                    { date: '2023-10-20', diagnosis: 'tantrum', therapy: 'Pemberian obat panadol' },
                    { date: '2023-09-15', diagnosis: 'stres', therapy: 'Edukasi dan manajemen stres' }
                ],
                'P002': [
                    { date: '2023-10-05', diagnosis: 'Demam Berdarah', therapy: 'Rawat inap dan cairan infus' }
                ]
            };

            // --- FUNGSI RENDER TABEL ---
            function renderSchedule() {
                const tbody = document.getElementById('scheduleTableBody');
                tbody.innerHTML = appointments.map(a => `
                    <tr>
                        <td>${a.time}</td>
                        <td><a href="#" class="text-decoration-none" onclick="showPatientDetail('${a.patientId}')">${a.patientName}</a></td>
                        <td>${a.complaint}</td>
                        <td><span class="badge bg-success">${a.status}</span></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" onclick="showPatientDetail('${a.patientId}')"><i class="bi bi-eye"></i></button>
                            <button class="btn btn-sm btn-outline-success"><i class="bi bi-check-circle"></i></button>
                        </td>
                    </tr>
                `).join('');
            }

            function renderPatients() {
                const tbody = document.getElementById('patientsTableBody');
                tbody.innerHTML = patients.map(p => `
                    <tr>
                        <td>${p.id}</td>
                        <td><a href="#" class="text-decoration-none" onclick="showPatientDetail('${p.id}')">${p.name}</a></td>
                        <td>${p.dob}</td>
                        <td>${p.phone}</td>
                        <td>
                            <button class="btn btn-sm btn-primary" onclick="showPatientDetail('${p.id}')"><i class="bi bi-eye"></i> Detail</button>
                        </td>
                    </tr>
                `).join('');
            }

            // --- FUNGSI MODAL DETAIL PASIEN ---
            function showPatientDetail(patientId) {
                const patient = patients.find(p => p.id === patientId);
                const history = patientHistory[patientId] || [];

                document.getElementById('modalPatientName').innerText = `Detail Pasien: ${patient.name}`;
                document.getElementById('modalPatientBody').innerHTML = `
                    <div class="row mb-3">
                        <div class="col-sm-3"><strong>ID Pasien:</strong></div>
                        <div class="col-sm-9">${patient.id}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3"><strong>Nama Lengkap:</strong></div>
                        <div class="col-sm-9">${patient.name}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3"><strong>Tanggal Lahir:</strong></div>
                        <div class="col-sm-9">${patient.dob}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3"><strong>No. Telepon:</strong></div>
                        <div class="col-sm-9">${patient.phone}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3"><strong>Alamat:</strong></div>
                        <div class="col-sm-9">${patient.address}</div>
                    </div>
                    <hr>
                    <h6>Riwayat Kunjungan</h6>
                    ${history.length > 0 ? `
                        <ul class="list-group">
                            ${history.map(h => `
                                <li class="list-group-item">
                                    <strong>${h.date}</strong> - ${h.diagnosis} <br>
                                    <small>Terapi: ${h.therapy}</small>
                                </li>
                            `).join('')}
                        </ul>
                    ` : '<p>Belum ada riwayat kunjungan.</p>'}
                `;
                
                const patientModal = new bootstrap.Modal(document.getElementById('patientModal'));
                patientModal.show();
            }

            // --- FUNGSI GRAFIK ---
            function renderChart() {
                const ctx = document.getElementById('visitChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: ['Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober'],
                        datasets: [{
                            label: 'Jumlah Kunjungan',
                            data: [65, 78, 90, 81, 96, 105],
                            borderColor: 'rgb(25, 135, 84)', // Bootstrap success color
                            backgroundColor: 'rgba(25, 135, 84, 0.1)',
                            tension: 0.3,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });
            }

            // --- INISIALISASI HALAMAN ---
            renderSchedule();
            renderPatients();
            renderChart();

        });