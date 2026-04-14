@extends('layouts.app')

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <a href="{{ route('repairs.index') }}" class="text-decoration-none text-muted"><i class="bi bi-arrow-left"></i> All Repairs</a>
        <h3 class="page-title mt-2 mb-0">Repair Job: {{ $repair->repair_number }}</h3>
    </div>
    <div>
        <a href="{{ route('repairs.edit', $repair) }}" class="btn btn-primary"><i class="bi bi-pencil"></i> Edit</a>
        <form action="{{ route('repairs.destroy', $repair) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this repair job?');">
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
                    <a href="{{ route('customers.show', $repair->customer_id) }}" class="fw-medium fs-5 text-dark text-decoration-none">{{ $repair->customer->full_name ?? 'Unknown' }}</a>
                </div>
                <div class="mb-3">
                    <span class="text-muted small fw-bold text-uppercase tracking-wide">Status</span><br>
                    @php
                        $statusColors = [
                            'Pending' => 'warning',
                            'In Progress' => 'info',
                            'Completed' => 'success',
                            'Collected' => 'secondary',
                            'Cancelled' => 'danger'
                        ];
                        $color = $statusColors[$repair->status] ?? 'light';
                    @endphp
                    <span class="badge bg-{{ $color }}-subtle text-{{ $color }} border border-{{ $color }}-subtle px-3 py-2 rounded-pill fs-6">{{ $repair->status }}</span>
                </div>
                <div class="mb-3">
                    <span class="text-muted small fw-bold text-uppercase tracking-wide">Date In</span><br>
                    <span class="fw-medium text-dark">{{ \Carbon\Carbon::parse($repair->repair_date)->format('M d, Y') }}</span>
                </div>
                <div class="mb-3">
                    <span class="text-muted small fw-bold text-uppercase tracking-wide">Target Completion</span><br>
                    <span class="fw-medium text-dark">{{ $repair->completion_date ? \Carbon\Carbon::parse($repair->completion_date)->format('M d, Y') : 'Not set' }}</span>
                </div>
                <div class="mb-0">
                    <span class="text-muted small fw-bold text-uppercase tracking-wide">Assigned To</span><br>
                    <span class="fw-medium text-dark">{{ $repair->assigned_staff ?? 'Unassigned' }}</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header bg-white border-bottom pb-3 pt-4">
                <h5 class="mb-0 fw-semibold text-primary">Repair Details</h5>
            </div>
            <div class="card-body p-4">
                <div class="row mb-4">
                    <div class="col-sm-6">
                        <span class="text-muted fw-bold">Type of Repair</span>
                        <p class="fs-5 fw-medium text-dark mb-0">{{ $repair->repair_type ?: 'Not specified' }}</p>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted fw-bold">Item / SKU</span>
                        <p class="fs-5 fw-medium text-dark mb-0">{{ $repair->sku ?: 'Not specified' }}</p>
                    </div>
                </div>
                <div class="mb-4">
                    <span class="text-muted fw-bold">Estimated Cost</span>
                    <p class="fs-5 fw-medium text-success mb-0">${{ number_format($repair->repair_price, 2) }}</p>
                </div>
                <div class="mb-0 border-top pt-4">
                    <span class="text-muted fw-bold d-block mb-2">Description & Notes</span>
                    <div class="bg-light p-3 rounded text-dark" style="white-space: pre-wrap;">{{ $repair->repair_notes ?: 'No description provided.' }}</div>
                </div>
            </div>
        </div>
        
        @if($repair->collection_notes)
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom pb-3 pt-4 border-0">
                <h5 class="mb-0 fw-semibold text-primary">Collection Notes</h5>
            </div>
            <div class="card-body p-4">
                <p class="mb-0 text-dark" style="white-space: pre-wrap;">{{ $repair->collection_notes }}</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
