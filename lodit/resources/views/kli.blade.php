@extends('layouts.app')

@section('title', 'LODIT - Home')

@section('head')
<style>
     /* Only force white text on dark backgrounds */
     .bg-dark .text-muted,
     .bg-dark p,
     .bg-dark h6,
     .bg-dark h1,
     .bg-dark h2,
     .bg-dark h3,
     .bg-dark h4,
     .bg-dark h5 {
         color: #ffffff !important;
     }
     
     /* Allow normal text on light backgrounds */
     .bg-white .text-muted,
     .bg-white p,
     .bg-white h6,
     .text-dark {
         color: inherit !important;
     }
     
     /* Pharmacists section - make text black */
     #dokter .card-body p,
     #dokter .card-body h5 {
         color: #000000 !important;
     }
     
     /* Testimonies section - make text black under stars */
     .bg-primary .card-text,
     .bg-primary .blockquote-footer {
         color: #000000 !important;
     }
     
     /* Our Services title - make white */
     #layanan .display-5 {
         color: #ffffff !important;
     }
 </style>
@endsection

@section('content')
    <div class="container">
    <div class="row align-items-center">
    <div class="col-lg-6">

    <h1 class="display-4 fw-bold text-primary mb-4">
    The Guarantee to Better Health
    </h1>

    <p class="lead mb-4">
   KIPS will bring you the guarantee to health, medicine, treatments, and everything to make you and your family be happy and healthy together.
    </p>

<div class="d-flex gap-3">

<button id="res" class="btn btn-primary">
<i class="bi bi-calendar-check"></i> Reservations
</button>

<button class="btn btn-outline-primary btn-lg" data-bs-toggle="modal" data-bs-target="#facilitiesModal">
<i class="bi bi-building"></i> Our Facilities
</button>
                    
</div>

<div class="mt-4">
<div class="row text-center">
<div class="col-4">

<h3 class="text-primary fw-bold">15+</h3>
<p class="mb-0">Years of Experience</p>

</div>

<div class="col-4">
<h3 class="text-primary fw-bold">8145+</h3>
<p class="mb-0">Satisfied Patients</p>
</div>

<div class="col-4">
<h3 class="text-primary fw-bold">24/7</h3>
<p class="mb-0">Service</p>
</div>

</div>
</div>
</div>

<div class="col-lg-6">
<img width=200% src="foto\hosp1.jpg"  alt="Clinical" class="img-fluid rounded-3 shadow">
<!-- "foto\logo.jpg" -->
</div>

</div>
</div>
</section>


<!-- ------------------------------------------------------------------------------------------------------- -->
<div class="modal fade" id="facilitiesModal" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">

      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">
          <i class="bi bi-capsule"></i> Pharmacy Services
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div class="row g-4">

          <div class="col-md-6">
            <h6 class="fw-bold mb-3">
              <i class="bi bi-bag-check text-primary"></i> Prescription Services
            </h6>
            <p class="text-muted">
              We provide accurate and fast prescription services, ensuring your medicines are prepared safely according to your doctor’s instructions.
            </p>
          </div>

          <div class="col-md-6">
            <h6 class="fw-bold mb-3">
              <i class="bi bi-capsule-pill text-success"></i> Over-the-Counter Medicine
            </h6>
            <p class="text-muted">
              A wide range of over-the-counter medicines is available for common conditions such as flu, fever, pain relief, allergies, and digestion.
            </p>
          </div>

          <div class="col-md-6">
            <h6 class="fw-bold mb-3">
              <i class="bi bi-shield-check text-info"></i> Medicine Consultation
            </h6>
            <p class="text-white">
              Our licensed pharmacists are available to guide you on proper medication usage, dosage, and possible side effects.
            </p>
          </div>

          <div class="col-md-6">
            <h6 class="fw-bold mb-3 text-white">
              <i class="bi bi-box-seam text-warning"></i> Medical Supplies
            </h6>
            <p class="text-white">
              We sell medical supplies such as masks, thermometers, first-aid kits, vitamins, and health supplements.
            </p>
          </div>

          <div class="col-md-6">
            <h6 class="fw-bold mb-3 text-white">
              <i class="bi bi-truck text-danger"></i> Medicine Delivery
            </h6>
            <p class="text-white">
              Safe and reliable medicine delivery services are available to ensure you receive your medication without leaving your home.
            </p>
          </div>

          <div class="col-md-6">
            <h6 class="fw-bold mb-3 text-white">
              <i class="bi bi-clock-history text-secondary"></i> 24/7 Pharmacy Support
            </h6>
            <p class="text-white">
              Our pharmacy support is available around the clock for urgent medicine needs and essential healthcare products.
            </p>
          </div>

</div>
<hr>
    <div class="row">
    <div class="col-12">
    <img src="foto\hosp1.jpg" alt="Fasilitas Klinik" class="img-fluid rounded">
    <!-- halonew.jpg  -->
</div>
</div>
</div>


                </div>
                </div>
                </div>

<!-- ------------------------------------------------------------------------------------------------------- -->
<section class="bg-danger text-white py-3">
    <div class="container">
    <div class="row align-items-center">
    <div class="col-md-8">

        <h5 class="mb-0">
        <i class="bi bi-exclamation-triangle-fill"></i> 
                 Emergency Contact 24 Hours - Contact : (081) 0853-5510
        </h5>
    </div>

    <div class="col-md-4 text-md-end mt-2 mt-md-0">
        <button id="con" class="btn btn-light">
        <i class="bi bi-telephone-fill"></i> Contact Now
        </button>
    </div>

    </div>
    </div>
</section>

<!-- ------------------------------------------------------------------------------------------------------- -->
<section id="layanan" class="py-5">
  <div class="container">

    <div class="text-center mb-5">
      <h2 class="display-5 fw-bold">Our Services</h2>
      <p class="lead text-muted">Pharmacy services we provide to support your health</p>
    </div>

    <div class="row g-4">

      <div class="col-md-6 col-lg-3">
        <div class="card h-100 border-0 shadow-sm">
          <div class="card-body text-center p-4">
            <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
              <i class="bi bi-capsule text-success fs-2"></i>
            </div>
            <h5 class="card-title">Prescription Medicine</h5>
            <p class="card-text text-muted">
              Safe and accurate preparation of prescription medicines based on doctor’s instructions.
            </p>
            <a href="#" class="btn btn-outline-success">Details →</a>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-lg-3">
        <div class="card h-100 border-0 shadow-sm">
          <div class="card-body text-center p-4">
            <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
              <i class="bi bi-bag-plus text-info fs-2"></i>
            </div>
            <h5 class="card-title">Over-the-Counter Drugs</h5>
            <p class="card-text text-muted">
              Common medicines for flu, fever, pain relief, allergies, and digestive issues.
            </p>
            <a href="#" class="btn btn-outline-info">Details →</a>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-lg-3">
        <div class="card h-100 border-0 shadow-sm">
          <div class="card-body text-center p-4">
            <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
              <i class="bi bi-chat-square-text text-warning fs-2"></i>
            </div>
            <h5 class="card-title">Pharmacist Consultation</h5>
            <p class="card-text text-muted">
              Professional guidance on medication usage, dosage, interactions, and side effects.
            </p>
            <a href="#" class="btn btn-outline-warning">Details →</a>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-lg-3">
        <div class="card h-100 border-0 shadow-sm">
          <div class="card-body text-center p-4">
            <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
              <i class="bi bi-truck text-danger fs-2"></i>
            </div>
            <h5 class="card-title">Medicine Delivery</h5>
            <p class="card-text text-muted">
              Fast and reliable medicine delivery service to ensure your health needs are met.
            </p>
            <a href="#" class="btn btn-outline-danger">Details →</a>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ------------------------------------------------------------------------------------------------------- -->
<section id="dokter" class="bg-dark py-5">
    <div class="container">
    <div class="text-center mb-5">
        <h2 class="display-5 fw-bold text-white">Our Pharmacists</h2>
        <p class="lead text-white">Trusted and professional pharmacy staff</p>
    </div>

    <div class="row g-4">
    <div class="col-md-6 col-lg-3">
    <div class="card border-0 shadow-sm">
    <img src="foto\kaikai.jpg" class="card-img-top" alt="Pharmacist" style="width: 100%; height: auto; object-fit: cover;">
    <div class="card-body text-center">
        <h5 class="card-title text-white">Dr. Kai </h5>
        <p class="text-white small">Specializes in Heart Medications</p>
        <p class="small text-white">Experience: 15 months</p>
        <div class="d-flex justify-content-center gap-2">
            <span class="badge bg-primary">Pharmacy</span>
            <span class="badge bg-success">Heart Meds</span>
    </div>
    </div>
    </div>
    </div>

    <div class="col-md-6 col-lg-3">
    <div class="card border-0 shadow-sm">
    <img src="foto\chippy.jpg" class="card-img-top" alt="Pharmacist" style="width: 100%; height: auto; object-fit: cover;">
            <div class="card-body text-center">
            <h5 class="card-title text-white">Dr.Chips</h5>
            <p class="text-white small">Specializes in Neurological Meds</p>
            <p class="small text-white">Experience: 18 months</p>
            <div class="d-flex justify-content-center gap-2">
                    <span class="badge bg-info">Neuro Meds</span>
                    <span class="badge bg-warning">Expert</span>
            </div>
            </div>
            </div>
            </div>

    <div class="col-md-6 col-lg-3">
    <div class="card border-0 shadow-sm">
    <img src="foto\vandeture.jpg" class="card-img-top" alt="Pharmacist" style="width: 100%; height: auto; object-fit: cover;">
            <div class="card-body text-center">
                <h5 class="card-title text-white">Dr. Van </h5>
                <p class="text-white small">Specializes in Wellness Medications</p>
                <p class="small text-white">Experience: 18 months</p>
                <div class="d-flex justify-content-center gap-2">
                        <span class="badge bg-primary">Wellness</span>
                        <span class="badge bg-secondary">Pharmacist</span>
                </div>
                </div>
                </div>
                </div>

    <div class="col-md-6 col-lg-3">
    <div class="card border-0 shadow-sm">
    <img src="foto\thepinkone.jpg" class="card-img-top" alt="Pharmacist" style="width: 100%; height: auto; object-fit: cover;">
                <div class="card-body text-center">
                    <h5 class="card-title text-white">Dr. Mimi</h5>
                    <p class="text-white small">Specializes in Medicinal Field</p>
                    <p class="small text-white">Experience: 15 months</p>
                <div class="d-flex justify-content-center gap-2">
                    <span class="badge bg-danger">Medicinal</span>
                    <span class="badge bg-info">Specialist</span>
                </div>
                </div>
                </div>
                </div>
                </div>
                </div>
</section>

<!-- ------------------------------------------------------------------------------------------------------- -->
<!-- <section id="janji-temu" class="py-5">
  <div class="container">
    <div class="row">
      
      <div class="col-lg-6 mb-4">
        <div class="p-4 border rounded-4 shadow bg-light">
          <h2 class="fw-bold mb-4">Create Reservation</h2>
          <p class="text-muted mb-4">Fill out our form to create your reservation</p>

          <form method="POST" action="{{ route('reservations.store') }}">
            @csrf
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Full Name</label>
                <input type="text" name="patient_name" class="form-control" placeholder="Insert your name" required>
              </div>

              <div class="col-md-6">
                <label class="form-label">Phone Number</label>
                <input type="tel" name="phone" class="form-control" placeholder="XXXX-XXXX-XXXX" required>
              </div>

              <div class="col-md-6">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="email@example.com">
              </div>

              <div class="col-md-6">
                <label class="form-label">Choose your Doctor</label>
                <select name="doctor" class="form-select" required>
                  <option value="">Choose Doctor</option>
                  <option value="Dr. Kai">Dr. Kai</option>
                  <option value="Dr. Chips">Dr. Chips</option>
                  <option value="Dr. Van">Dr. Van</option>
                  <option value="Dr. Mimi">Dr. Mimi</option>
                </select>
              </div>

              <div class="col-md-6">
                <label class="form-label">Date of Reservation</label>
                <input type="date" name="date" class="form-control" required>
              </div>

              <div class="col-12">
                <label class="form-label">Illness or Problem</label>
                <textarea name="complaint" class="form-control" rows="3" placeholder="Explain your symptoms..." required></textarea>
              </div>

              <div class="col-12">
                <button type="submit" class="btn btn-primary w-100">
                  <i class="bi bi-send"></i> Send Reservation Request
                </button>
              </div>
            </div>
          </form>
        </div>
      </div> -->

     <div class="row justify-content-center">
  <div class="col-lg-6 mb-4">
    <div class="bg-light rounded-4 p-4 shadow-sm">
      <h4 class="mb-4 text-center">Operational Hours</h4>

      <div class="table-responsive">
        <table class="table">
          <tbody>
            <tr>
              <td>Monday - Friday</td>
              <td class="text-end fw-bold">08:00 - 20:00</td>
            </tr>
            <tr>
              <td>Saturday</td>
              <td class="text-end fw-bold">07:00 - 20:00</td>
            </tr>
            <tr>
              <td>Sunday</td>
              <td class="text-end fw-bold">09:00 - 21:00</td>
            </tr>
            <tr>
              <td>Emergency Contact</td>
              <td class="text-end fw-bold text-danger">24 Hours</td>
            </tr>
          </tbody>
        </table>
      </div>

      <hr>

      <h5 class="mb-3 text-center">Why Should You Pick Us?</h5>

      <ul class="list-unstyled text-center">
        <li class="mb-2">
          <i class="bi bi-check-circle-fill text-success"></i>
          Trusted and Professional Doctors
        </li>
        <li class="mb-2">
          <i class="bi bi-check-circle-fill text-success"></i>
          Modern and Complete Facilities
        </li>
        <li class="mb-2">
          <i class="bi bi-check-circle-fill text-success"></i>
          Polite and Professional Etiquette
        </li>
        <li class="mb-2">
          <i class="bi bi-check-circle-fill text-success"></i>
          Affordable and Transparent Prices
        </li>
        <li class="mb-2">
          <i class="bi bi-check-circle-fill text-success"></i>
          Very Strategic and Accessible Location
        </li>
      </ul>
    </div>
  </div>
</div>




<!-- @if(session('level') == 2 || session('level') == 3)
<div class="container mt-5">
        <div class="text-center mb-5">
            <h1 class="display-5 fw-bold">Admin Clinic Panel</h1>
            <p class="lead text-muted">Edit Doctor Data and Customer Reservations</p>
        </div>
        
        <ul class="nav nav-tabs mb-4" id="adminTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="doctors-tab" data-bs-toggle="tab" data-bs-target="#doctors-tab-pane" type="button" role="tab">
                    <i class="bi bi-person-badge"></i> Edit Doctor
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="appointments-tab" data-bs-toggle="tab" data-bs-target="#appointments-tab-pane" type="button" role="tab">
                    <i class="bi bi-calendar-check"></i> Edit Reservations
                </button>
            </li>
        </ul>
        
        <div class="tab-content" id="adminTabsContent">
            <div class="tab-pane fade show active" id="doctors-tab-pane" role="tabpanel">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-people-fill"></i> Doctor Data
                        </h5>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#doctorModal">
                            <i class="bi bi-plus-circle"></i> Add Doctor
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Photo</th>
                                        <th>Name</th>
                                        <th>Specialized in</th>
                                        <th>Experience</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="doctorsTable">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="tab-pane fade" id="appointments-tab-pane" role="tabpanel">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-calendar-week"></i> Reservation Data
                        </h5>
                        <div>
                            <button class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#appointmentModal">
                                <i class="bi bi-plus-circle"></i> Add Reservations
                            </button>
                            <button class="btn btn-info" id="exportAppointments">
                                <i class="bi bi-download"></i> Export Data
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Patient Name</th>
                                        <th>Phone Number</th>
                                        <th>Doctor</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="appointmentsTable">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="doctorModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="doctorModalTitle">
                        <i class="bi bi-person-plus"></i> Add Doctor
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="doctorForm">
                        <input type="hidden" id="doctorId">
                        <div class="mb-3">
                            <label class="form-label">Doctor's Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="doctorName" placeholder="Insert Full Name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Specialized in <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="doctorSpecialization" placeholder="Example: Therapist" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Experience <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="doctorExperience" placeholder="Example: 10" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Photo URL (Optional)</label>
                            <input type="url" class="form-control" id="doctorPhoto" placeholder="https://example.com/photo.jpg">
                            <small class="text-muted">If left empty, it will use the default image</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-primary" id="saveDoctor">
                        <i class="bi bi-check-circle"></i> Save
                    </button>
                </div>
            </div>
        </div>
    </div> -->

    <!-- Appointment Modal
    <div class="modal fade" id="appointmentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-calendar-plus"></i> Create new Reservation
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="appointmentForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Patient's Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="patientName" placeholder="Input the Patient's Name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" id="patientPhone" placeholder="0XXX-XXXX-XXXX" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="patientEmail" placeholder="email@example.com" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Pick Doctor <span class="text-danger">*</span></label>
                                    <select class="form-select" id="appointmentDoctorSelect" required>
                                        <option value="">-= Pick Doctor =-</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Date<span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="appointmentDate" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select class="form-select" id="appointmentStatus">
                                        <option value="pending">Pending</option>
                                        <option value="confirmed">Confirmed</option>
                                        <option value="cancelled">Cancelled</option>
                                        <option value="completed">Completed</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Illness <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="patientComplaint" rows="3" placeholder="Explain the Patient's Symptoms..." required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-success" id="saveAppointment">
                        <i class="bi bi-check-circle"></i> Save Reservation
                    </button>
                </div>
            </div>
        </div>
    </div> -->

    <!-- Appointment Detail Modal -->
    <div class="modal fade" id="appointmentDetailModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-calendar-check"></i> Reservation Details
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="appointmentDetail">
                        <!-- Appointment details will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto">Notifications</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" id="toastMessage">
                <!-- Toast message will be displayed here -->
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        
    </script>
    <br><br><br><br><br>
    @endif
<!-- ------------------------------------------------------------------------------------------------------- -->
    <section class="bg-primary text-white py-5">
            <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">Patient Tesitonies</h2>
                <p>What did they say about our services</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                <div class="card bg-white text-dark h-100">
                <div class="card-body">
                            <div class="text-warning mb-3">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                            </div>
            <p class="card-text">"I like the doctor, eventhough they can throw a bit of shade here and there"</p>

                        <footer class="blockquote-footer mb-0">
                                <strong>Noah</strong>
                                <cite title="Source Title">Routine Patient</cite>
                        </footer>
                        </div>
                        </div>
                        </div>

                <div class="col-md-4">
                    <div class="card bg-white text-dark h-100">
                    <div class="card-body">
                            <div class="text-warning mb-3">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                            </div>
                    <p class="card-text">"Theyre great to talk with, although a bit sassy at times"</p>

                            <footer class="blockquote-footer mb-0">
                                <strong>Leiv</strong>
                                <cite title="Source Title">New Patient</cite>
                            </footer>
                            </div>
                            </div>
                            </div>

                <div class="col-md-4">
                    <div class="card bg-white text-dark h-100">
                    <div class="card-body">
                            <div class="text-warning mb-3">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                            </div>
                    <p class="card-text">"Why is it in China?"</p>

                            <footer class="blockquote-footer mb-0">
                                <strong>Withers</strong>
                                <cite title="Source Title">Mental Patient</cite>
                            </footer>
                            </div>
                            </div>
                            </div>
                            </div>
                            </div>
    </section>

<!---------------------------------------------------------------------------------------------------------- -->
    <section id="contact" class="py-5">
        <div class="container">
        <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">Contact Us</h2>
                <p class="lead text-muted">We are ready to fulfill your necessities and needs</p>
        </div>

            <div class="row g-4">
            <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center p-4">
            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">

                <i class="bi bi-geo-alt-fill text-primary fs-2"></i>
            </div>

                    <h5>Address</h5>
                    <p class="text-muted">Central Plaza, Units L1-3, at 381 Huaihai Middle Road, Huangpu District<br></p>
            </div>
            </div>
            </div>

            <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center p-4">
            <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">

                    <i class="bi bi-telephone-fill text-success fs-2"></i>
            </div>

                            <h5>Phone Number</h5>
                            <p class="text-muted">
                                (015) 0277-3426<br>
                                (081) 0853-5510 (Emergency Contact)
                            </p>
                        </div>
                        </div>
                        </div>

            <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center p-4">
            <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                <i class="bi bi-envelope-fill text-info fs-2"></i>
            </div>

                            <h5>Email</h5>
                            <p class="text-muted">
                                info@kips.com<br>
                                admin@kips.com
                            </p>
                    </div>
                    </div>
                    </div>
                    </div>

            <div class="row mt-5">
            <div class="col-12">
            <div class="ratio ratio-16x9">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3411.5945710524393!2d121.47395507560012!3d31.231959974347344!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x35b270427b2234b1%3A0x79ea776c7397d149!2sPop%20Mart!5e0!3m2!1sid!2sid!4v1760245282216!5m2!1sid!2sid"
                                style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    </div>
                    </div>
                    </div>
                    </div>
    </section>

<!---------------------------------------------------------------------------------------------------------- -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
        <div class="row g-4">
        <div class="col-lg-4">
                    <h5 class="mb-3">
                        <i class="bi bi-hospital"></i> KIPS
                    </h5>
                    <p class="text-white-50">KIPS will bring you the guarantee to health, medicine, treatments, and everything to make you and your family be happy and healthy together.<br><br>
                                            Established May 25th 2010</p>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-white"><i class="bi bi-facebook fs-5"></i></a>
                        <a href="#" class="text-white"><i class="bi bi-twitter fs-5"></i></a>
                        <a href="#" class="text-white"><i class="bi bi-instagram fs-5"></i></a>
                        <a href="#" class="text-white"><i class="bi bi-youtube fs-5"></i></a>
                </div>
                </div>

                <div class="col-lg-2">
                    <h6 class="mb-3">Services</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Prescription Medicine</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Over-the-Counter Drugs</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Pharmacist Consultation</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Medicine Delivery</a></li>
                </ul>
                </div>

                <div class="col-lg-2">
                    <h6 class="mb-3">Quick Links</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">About Us</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Coureir</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Health Article</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">FAQ</a></li>
                </ul>
                </div>

                <div class="col-lg-4">
                    <h6 class="mb-3">Newsletter</h6>
                    <p class="text-white-50">Get information for our most recent comeuppances and discounts with promos!</p>
                    <div class="input-group">
                        <input type="email" class="form-control" placeholder="Insert your Email address">
                        <button class="btn btn-primary" type="button">send</button>
                </div>
                </div>

            </div>
            <hr class="border-secondary my-4">
            <div class="text-center text-white-50">
                <p class="mb-0">&copy; 2025 KIPS. All rights reserved. | 
                    <a href="#" class="text-white-50">Privacy Policy</a> | 
                    <a href="#" class="text-white-50">Terms of Service</a>
                </p>
            </div>
            </div>
    </footer>

<!---------------------------------------------------------------------------------------------------------- -->
    <div class="modal fade" id="appointmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
    <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-calendar-check"></i> Create your Reservation
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                <form>
                <div class="row g-3">
                <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" required>
                </div>

                <div class="col-md-6">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" required>
                </div>

                <div class="col-md-6">
                            <label class="form-label">Doctor</label>
                            <select class="form-select" required>
                                <option value="">Choose your Doctor</option>
                                <option>Dr. Kai</option>
                                <option>Dr. Chips</option>
                                <option>Dr. Van</option>
                                <option>Dr. Mimi</option>
                            </select>
                </div>

                <div class="col-md-6">
                            <label class="form-label">Date</label>
                            <input type="date" class="form-control" required>
                </div>

                <div class="col-12">
                            <label class="form-label">Illness</label>
                            <textarea class="form-control" rows="2"></textarea>
                </div>
                </div>
                </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary">Confirm Reservation</button>
                </div>
                </div>
                </div>
                </div>




@endsection

@section('scripts')
<script type="module" src="{{ asset('js/storage.js') }}"></script>
<script type="module" src="{{ asset('js/doctor.js') }}"></script>
<script type="module" src="{{ asset('js/appointment.js') }}"></script>
<script type="module" src="{{ asset('js/ui.js') }}"></script>
<script type="module" src="{{ asset('js/main.js') }}"></script>
@endsection