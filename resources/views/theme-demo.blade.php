@extends('layouts.app')

@section('title', 'Blue Theme Demo - GCMS')

@push('styles')
<style>
    /* Demo Page Specific Styles */
    .demo-section {
        margin-bottom: 40px;
    }

    .color-swatch {
        width: 100%;
        height: 60px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 500;
        margin-bottom: 10px;
        transition: transform 0.2s;
    }

    .color-swatch:hover {
        transform: scale(1.05);
    }

    .stat-card {
        background: var(--theme-white);
        border-radius: 12px;
        padding: 24px;
        position: relative;
        overflow: hidden;
        border: 1px solid var(--theme-sky-100);
        transition: all 0.3s ease;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--bg-accent);
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(30, 58, 138, 0.15);
    }

    .stat-number {
        font-size: 2rem;
        font-weight: 600;
        color: var(--theme-ocean-600);
    }

    .stat-label {
        color: var(--theme-gray-600);
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="card mb-4" style="background: linear-gradient(135deg, var(--theme-ocean-500) 0%, var(--theme-navy-600) 100%); border: none;">
        <div class="card-body text-white py-5">
            <h1 class="display-4 fw-light">Blue-White-Navy Theme</h1>
            <p class="lead mb-0">โทนสีฟ้า ขาว น้ำเงิน - Professional Medical System Design</p>
        </div>
    </div>

    <!-- Color Palette -->
    <div class="demo-section">
        <h2 class="mb-4" style="color: var(--theme-navy-700);">
            <i class="bi bi-palette me-2"></i>Color Palette
        </h2>
        <div class="row g-3">
            <div class="col-md-3">
                <div class="card card-blue">
                    <div class="card-body">
                        <h6 class="text-uppercase mb-3" style="color: var(--theme-ocean-700);">Sky Blue</h6>
                        <div class="color-swatch" style="background: var(--theme-sky-500);">#0ea5e9</div>
                        <div class="color-swatch" style="background: var(--theme-sky-300);">#7dd3fc</div>
                        <div class="color-swatch" style="background: var(--theme-sky-100); color: var(--theme-navy-700);">#e0f2fe</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-blue">
                    <div class="card-body">
                        <h6 class="text-uppercase mb-3" style="color: var(--theme-ocean-700);">Ocean Blue</h6>
                        <div class="color-swatch" style="background: var(--theme-ocean-600);">#2563eb</div>
                        <div class="color-swatch" style="background: var(--theme-ocean-400);">#60a5fa</div>
                        <div class="color-swatch" style="background: var(--theme-ocean-200); color: var(--theme-navy-700);">#bfdbfe</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-blue">
                    <div class="card-body">
                        <h6 class="text-uppercase mb-3" style="color: var(--theme-ocean-700);">Navy Blue</h6>
                        <div class="color-swatch" style="background: var(--theme-navy-800);">#1e2a5e</div>
                        <div class="color-swatch" style="background: var(--theme-navy-600);">#1e3a8a</div>
                        <div class="color-swatch" style="background: var(--theme-navy-500);">#1e40af</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-blue">
                    <div class="card-body">
                        <h6 class="text-uppercase mb-3" style="color: var(--theme-ocean-700);">White & Gray</h6>
                        <div class="color-swatch" style="background: var(--theme-white); border: 1px solid var(--theme-gray-200); color: var(--theme-gray-600);">#ffffff</div>
                        <div class="color-swatch" style="background: var(--theme-gray-100); color: var(--theme-gray-700);">#f1f5f9</div>
                        <div class="color-swatch" style="background: var(--theme-gray-500);">#64748b</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Components Demo -->
    <div class="demo-section">
        <h2 class="mb-4" style="color: var(--theme-navy-700);">
            <i class="bi bi-box me-2"></i>Components
        </h2>

        <!-- Buttons -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Buttons</h5>
            </div>
            <div class="card-body">
                <div class="d-flex gap-3 flex-wrap">
                    <button class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Primary Button
                    </button>
                    <button class="btn btn-secondary">
                        <i class="bi bi-gear me-2"></i>Secondary Button
                    </button>
                    <button class="btn btn-ocean">
                        <i class="bi bi-water me-2"></i>Ocean Button
                    </button>
                    <button class="btn btn-navy">
                        <i class="bi bi-anchor me-2"></i>Navy Button
                    </button>
                    <button class="btn btn-outline-ocean">
                        <i class="bi bi-wind me-2"></i>Outline Ocean
                    </button>
                </div>
            </div>
        </div>

        <!-- Forms -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Form Elements</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Text Input</label>
                        <input type="text" class="form-control" placeholder="Enter text here...">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Select Dropdown</label>
                        <select class="form-select">
                            <option>Choose option...</option>
                            <option>Option 1</option>
                            <option>Option 2</option>
                            <option>Option 3</option>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Textarea</label>
                        <textarea class="form-control" rows="3" placeholder="Enter description..."></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alerts -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Alerts</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info mb-3">
                    <i class="bi bi-info-circle me-2"></i>
                    This is an info alert with blue theme styling
                </div>
                <div class="alert alert-success mb-3">
                    <i class="bi bi-check-circle me-2"></i>
                    Success! Your operation completed successfully
                </div>
                <div class="alert alert-danger mb-0">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Error! Something went wrong
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-label">Total Patients</div>
                    <div class="stat-number">1,234</div>
                    <small class="text-success">
                        <i class="bi bi-arrow-up"></i> +12% from last month
                    </small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-label">Appointments</div>
                    <div class="stat-number">856</div>
                    <small class="text-info">
                        <i class="bi bi-calendar3"></i> This month
                    </small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-label">Revenue</div>
                    <div class="stat-number">฿45.2K</div>
                    <small class="text-success">
                        <i class="bi bi-arrow-up"></i> +8% increase
                    </small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-label">Active Courses</div>
                    <div class="stat-number">423</div>
                    <small class="text-warning">
                        <i class="bi bi-box-seam"></i> 89 expiring soon
                    </small>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Sample Table</h5>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Patient ID</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>#P001</td>
                            <td>John Doe</td>
                            <td>081-234-5678</td>
                            <td><span class="badge bg-primary">Active</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-ocean">View</button>
                            </td>
                        </tr>
                        <tr>
                            <td>#P002</td>
                            <td>Jane Smith</td>
                            <td>082-345-6789</td>
                            <td><span class="badge bg-info">Course</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-ocean">View</button>
                            </td>
                        </tr>
                        <tr>
                            <td>#P003</td>
                            <td>Bob Johnson</td>
                            <td>083-456-7890</td>
                            <td><span class="badge bg-success">New</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-ocean">View</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection