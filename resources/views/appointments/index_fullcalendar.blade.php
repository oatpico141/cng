@extends('layouts.app')

@section('title', 'Appointment Calendar - GCMS')

@push('styles')
<!-- FullCalendar CSS -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />
<style>
    #calendar {
        max-width: 1200px;
        margin: 0 auto;
    }
    .fc-event {
        cursor: pointer;
    }
    .fc-daygrid-event {
        white-space: normal;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h2 class="mb-1"><i class="bi bi-calendar-check me-2"></i>Appointment Calendar</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                            <li class="breadcrumb-item active">Appointments</li>
                        </ol>
                    </nav>
                </div>
                <div class="mt-2 mt-md-0">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAppointmentModal">
                        <i class="bi bi-plus-circle me-1"></i> New Appointment
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar Card -->
    <div class="card">
        <div class="card-body">
            <div id="calendar"></div>
        </div>
    </div>
</div>

<!-- Create Appointment Modal -->
<div class="modal fade" id="createAppointmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="appointmentForm" method="POST" action="{{ route('appointments.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Create New Appointment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Patient Selection -->
                        <div class="col-md-6">
                            <label for="patient_id" class="form-label">Patient Phone <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="patient_phone"
                                   placeholder="Search by phone number" required>
                            <input type="hidden" id="patient_id" name="patient_id">
                            <small class="text-muted" id="patient_info"></small>
                        </div>

                        <!-- Branch -->
                        <div class="col-md-6">
                            <label for="branch_id" class="form-label">Branch <span class="text-danger">*</span></label>
                            <select class="form-select" id="branch_id" name="branch_id" required>
                                <option value="">Select Branch</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Appointment Date -->
                        <div class="col-md-6">
                            <label for="appointment_date" class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="appointment_date"
                                   name="appointment_date" required value="{{ date('Y-m-d') }}">
                        </div>

                        <!-- Appointment Time -->
                        <div class="col-md-6">
                            <label for="appointment_time" class="form-label">Time <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="appointment_time"
                                   name="appointment_time" required min="09:00" max="19:00">
                        </div>

                        <!-- PT Selection -->
                        <div class="col-md-6">
                            <label for="pt_id" class="form-label">Physical Therapist</label>
                            <select class="form-select" id="pt_id" name="pt_id">
                                <option value="">Auto Assign</option>
                                @foreach($staff as $pt)
                                    <option value="{{ $pt->id }}">{{ $pt->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Booking Channel -->
                        <div class="col-md-6">
                            <label for="booking_channel" class="form-label">Booking Channel <span class="text-danger">*</span></label>
                            <select class="form-select" id="booking_channel" name="booking_channel" required>
                                <option value="walk_in">Walk-in</option>
                                <option value="phone">Phone</option>
                                <option value="line">LINE</option>
                                <option value="website">Website</option>
                            </select>
                        </div>

                        <!-- Notes -->
                        <div class="col-12">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Create Appointment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View/Edit Appointment Modal -->
<div class="modal fade" id="viewAppointmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Appointment Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="appointmentDetails">
                <!-- Details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" id="cancelAppointmentBtn">
                    <i class="bi bi-x-circle me-1"></i> Cancel Appointment
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- FullCalendar JS -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var currentAppointmentId = null;

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: '{{ route('appointments.feed') }}',
        eventClick: function(info) {
            currentAppointmentId = info.event.id;
            showAppointmentDetails(info.event);
        },
        dateClick: function(info) {
            // Pre-fill date when clicking on calendar
            document.getElementById('appointment_date').value = info.dateStr;

            // Check if selected date is in the past
            var selectedDate = new Date(info.dateStr);
            var today = new Date();
            today.setHours(0, 0, 0, 0);

            var submitBtn = document.querySelector('#createAppointmentModal button[type="submit"]');
            if (selectedDate < today) {
                submitBtn.style.display = 'none';
            } else {
                submitBtn.style.display = 'inline-block';
            }

            var modal = new bootstrap.Modal(document.getElementById('createAppointmentModal'));
            modal.show();
        }
    });

    calendar.render();

    // Patient phone search
    document.getElementById('patient_phone').addEventListener('blur', function() {
        var phone = this.value;
        if (phone) {
            fetch(`/api/patients/search?phone=${phone}`)
                .then(response => response.json())
                .then(data => {
                    if (data.patient) {
                        document.getElementById('patient_id').value = data.patient.id;
                        document.getElementById('patient_info').textContent =
                            `${data.patient.name} - ${data.patient.email || 'No email'}`;
                        document.getElementById('patient_info').className = 'text-success';
                    } else {
                        document.getElementById('patient_info').textContent =
                            'New patient - OPD will be created';
                        document.getElementById('patient_info').className = 'text-warning';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }
    });

    // Form submission
    document.getElementById('appointmentForm').addEventListener('submit', function(e) {
        e.preventDefault();

        var formData = new FormData(this);

        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                calendar.refetchEvents();
                bootstrap.Modal.getInstance(document.getElementById('createAppointmentModal')).hide();
                this.reset();
                alert('Appointment created successfully!');
            } else {
                alert('Error: ' + (data.message || 'Failed to create appointment'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error creating appointment');
        });
    });

    // Cancel appointment
    document.getElementById('cancelAppointmentBtn').addEventListener('click', function() {
        if (currentAppointmentId && confirm('Are you sure you want to cancel this appointment?')) {
            fetch(`/appointments/${currentAppointmentId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    calendar.refetchEvents();
                    bootstrap.Modal.getInstance(document.getElementById('viewAppointmentModal')).hide();
                    alert(data.message || 'Appointment cancelled successfully!');
                } else {
                    alert('Error: ' + (data.message || 'Failed to cancel appointment'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error cancelling appointment');
            });
        }
    });

    function showAppointmentDetails(event) {
        var details = `
            <dl class="row">
                <dt class="col-sm-4">Patient:</dt>
                <dd class="col-sm-8">${event.extendedProps.patient_name}</dd>

                <dt class="col-sm-4">Date:</dt>
                <dd class="col-sm-8">${event.start.toLocaleDateString()}</dd>

                <dt class="col-sm-4">Time:</dt>
                <dd class="col-sm-8">${event.extendedProps.time}</dd>

                <dt class="col-sm-4">Branch:</dt>
                <dd class="col-sm-8">${event.extendedProps.branch_name}</dd>

                <dt class="col-sm-4">PT:</dt>
                <dd class="col-sm-8">${event.extendedProps.pt_name || 'Not assigned'}</dd>

                <dt class="col-sm-4">Status:</dt>
                <dd class="col-sm-8">
                    <span class="badge bg-${event.extendedProps.status_color}">${event.extendedProps.status}</span>
                </dd>
            </dl>
        `;

        document.getElementById('appointmentDetails').innerHTML = details;
        var modal = new bootstrap.Modal(document.getElementById('viewAppointmentModal'));
        modal.show();
    }
});
</script>
@endpush
@endsection
