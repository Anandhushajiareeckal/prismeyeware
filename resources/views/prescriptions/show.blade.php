@extends('layouts.app')

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <a href="{{ route('prescriptions.index') }}" class="text-decoration-none text-muted"><i class="bi bi-arrow-left"></i> All Prescriptions</a>
        <h3 class="page-title mt-2 mb-0">Prescription Details</h3>
    </div>
    <div>
        <a href="{{ route('prescriptions.edit', $prescription) }}" class="btn btn-primary"><i class="bi bi-pencil"></i> Edit</a>
        <form action="{{ route('prescriptions.destroy', $prescription) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this prescription?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger"><i class="bi bi-trash"></i> Delete</button>
        </form>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header bg-white border-bottom pb-3 pt-4">
                <h5 class="mb-0 fw-semibold text-primary">Overview</h5>
            </div>
            <div class="card-body p-4">
                <div class="mb-4">
                    <span class="text-muted small fw-bold text-uppercase tracking-wide">Customer</span><br>
                    <a href="{{ route('customers.show', $prescription->customer_id) }}" class="fw-medium fs-5 text-dark text-decoration-none">{{ $prescription->customer->full_name ?? 'Unknown' }}</a>
                </div>
                <div class="mb-3">
                    <span class="text-muted small fw-bold text-uppercase tracking-wide">Date</span><br>
                    <span class="fw-medium text-dark">{{ \Carbon\Carbon::parse($prescription->prescription_date)->format('M d, Y') }}</span>
                </div>
                <div class="mb-3">
                    <span class="text-muted small fw-bold text-uppercase tracking-wide">Type</span><br>
                    <span class="badge bg-light text-dark border">{{ $prescription->type }}</span>
                </div>
                <div class="mb-3">
                    <span class="text-muted small fw-bold text-uppercase tracking-wide">Optometrist / Practice Name</span><br>
                    <span class="fw-medium text-dark">{{ $prescription->doctor_name ?? 'Not specified' }}</span>
                </div>
                <div class="mb-0">
                    <span class="text-muted small fw-bold text-uppercase tracking-wide">Expiry Date</span><br>
                    <span class="fw-medium{{ $prescription->recall_date && \Carbon\Carbon::parse($prescription->recall_date)->isPast() ? ' text-danger' : ' text-dark' }}">{{ $prescription->recall_date ? \Carbon\Carbon::parse($prescription->recall_date)->format('M d, Y') : 'None set' }}</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header bg-white border-bottom pb-3 pt-4">
                <h5 class="mb-0 fw-semibold text-primary">Optical Measurements</h5>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive mb-0">
                    <table class="table table-bordered align-middle text-center mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-muted small fw-bold tracking-wide border-bottom-0">Eye</th>
                                <th class="text-muted small fw-bold tracking-wide border-bottom-0">SPH</th>
                                <th class="text-muted small fw-bold tracking-wide border-bottom-0">CYL</th>
                                <th class="text-muted small fw-bold tracking-wide border-bottom-0">AXIS</th>
                                <th class="text-muted small fw-bold tracking-wide border-bottom-0">PRISM (H)</th>
                                <th class="text-muted small fw-bold tracking-wide border-bottom-0">PRISM (V)</th>
                                <th class="text-muted small fw-bold tracking-wide border-bottom-0">ADD</th>
                                <th class="text-muted small fw-bold tracking-wide border-bottom-0">PD</th>
                                <th class="text-muted small fw-bold tracking-wide border-bottom-0">FH</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-bold fs-6 text-dark py-3 align-middle bg-light">Right (OD)</td>
                                <td class="fs-5 fw-medium text-dark py-3">{{ $prescription->od_sphere ?: '-' }}</td>
                                <td class="fs-5 fw-medium text-dark py-3">{{ $prescription->od_cylinder ?: '-' }}</td>
                                <td class="fs-5 fw-medium text-dark py-3">{{ $prescription->od_axis ?: '-' }}</td>
                                <td class="fs-5 fw-medium text-dark py-3">{{ $prescription->od_h_prism ?: '-' }}</td>
                                <td class="fs-5 fw-medium text-dark py-3">{{ $prescription->od_v_prism ?: '-' }}</td>
                                <td class="fs-5 fw-medium text-dark py-3">{{ $prescription->od_add ?: '-' }}</td>
                                <td class="fs-5 fw-medium text-dark py-3">{{ $prescription->od_pd ?: '-' }}</td>
                                <td class="fs-5 fw-medium text-dark py-3">{{ $prescription->od_fh ?: '-' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold fs-6 text-dark py-3 align-middle bg-light">Left (OS)</td>
                                <td class="fs-5 fw-medium text-dark py-3">{{ $prescription->os_sphere ?: '-' }}</td>
                                <td class="fs-5 fw-medium text-dark py-3">{{ $prescription->os_cylinder ?: '-' }}</td>
                                <td class="fs-5 fw-medium text-dark py-3">{{ $prescription->os_axis ?: '-' }}</td>
                                <td class="fs-5 fw-medium text-dark py-3">{{ $prescription->os_h_prism ?: '-' }}</td>
                                <td class="fs-5 fw-medium text-dark py-3">{{ $prescription->os_v_prism ?: '-' }}</td>
                                <td class="fs-5 fw-medium text-dark py-3">{{ $prescription->os_add ?: '-' }}</td>
                                <td class="fs-5 fw-medium text-dark py-3">{{ $prescription->os_pd ?: '-' }}</td>
                                <td class="fs-5 fw-medium text-dark py-3">{{ $prescription->os_fh ?: '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if($prescription->comments)
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom pb-3 pt-4">
                <h5 class="mb-0 fw-semibold text-primary">Notes / Comments</h5>
            </div>
            <div class="card-body p-4">
                <p class="mb-0 text-dark" style="white-space: pre-wrap;">{{ $prescription->comments }}</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
