@extends('layouts.app')

@section('title', 'Appointments - GCMS')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-1"><i class="bi bi-calendar-check me-2"></i>Appointments</h2>
            <p class="text-muted">Manage patient appointments</p>
        </div>
    </div>

    <!-- Working Status Card -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-check-circle me-2"></i>Appointments Page is Working!</h5>
                </div>
                <div class="card-body">
                    <h4 class="mb-3">System Status:</h4>
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-check-circle text-success me-2"></i>Page loaded successfully</span>
                            <span class="badge bg-success">OK</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-check-circle text-success me-2"></i>Layout with sidebar working</span>
                            <span class="badge bg-success">OK</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-database text-info me-2"></i>Branches available</span>
                            <span class="badge bg-info">{{ $branches->count() }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-people text-warning me-2"></i>PT Staff available</span>
                            <span class="badge bg-warning">{{ $staff->count() }}</span>
                        </li>
                    </ul>

                    <hr class="my-4">

                    <h4 class="mb-3">Available Branches:</h4>
                    <div class="row g-3">
                        @forelse($branches as $branch)
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h5 class="card-title"><i class="bi bi-building me-2"></i>{{ $branch->name }}</h5>
                                        <p class="card-text text-muted mb-0">
                                            <small>{{ $branch->address ?? 'No address' }}</small>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle me-2"></i>No branches found
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <hr class="my-4">

                    <h4 class="mb-3">Available PT Staff:</h4>
                    @if($staff->count() > 0)
                        <div class="row g-3">
                            @foreach($staff as $pt)
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <i class="bi bi-person-badge fs-1 text-primary"></i>
                                            <h6 class="mt-2 mb-0">{{ $pt->name }}</h6>
                                            <small class="text-muted">{{ $pt->position }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>No PT staff found. PT dropdown will be empty in appointment form.
                        </div>
                    @endif

                    <hr class="my-4">

                    <div class="alert alert-primary">
                        <h5><i class="bi bi-calendar3 me-2"></i>Full Calendar View</h5>
                        <p class="mb-0">The full FullCalendar view with interactive calendar has been temporarily simplified to ensure page loading. The calendar functionality is available and can be restored by using the full version.</p>
                    </div>

                    <div class="mt-3">
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
                        </a>
                        <a href="{{ route('patients.index') }}" class="btn btn-primary">
                            <i class="bi bi-people me-2"></i>Go to Patients
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
