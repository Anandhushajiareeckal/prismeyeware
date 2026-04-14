@extends('layouts.app')

@section('content')
<div class="mb-4">
    <a href="{{ route('repairs.show', $repair) }}" class="text-decoration-none text-muted"><i class="bi bi-arrow-left"></i> Back to Repair</a>
    <h3 class="page-title mt-2 mb-0">Edit Repair Job</h3>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-4 p-md-5">
        <form action="{{ route('repairs.update', $repair) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row g-3 mb-4">
                <div class="col-md-12 mb-3">
                    <label class="form-label text-muted fw-medium">Customer</label>
                    <input type="text" class="form-control bg-light border-0 text-dark fw-medium" value="{{ $repair->customer->full_name ?? 'Unknown' }}" readonly disabled>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-medium text-muted">Date In <span class="text-danger">*</span></label>
                    <input type="date" name="repair_date" class="form-control bg-light border-0" value="{{ old('repair_date', \Carbon\Carbon::parse($repair->repair_date)->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium text-muted">Target Completion</label>
                    <input type="date" name="completion_date" class="form-control bg-light border-0" value="{{ old('completion_date', $repair->completion_date ? \Carbon\Carbon::parse($repair->completion_date)->format('Y-m-d') : '') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium text-muted">Status</label>
                    <select name="status" class="form-select bg-light border-0 fw-medium">
                        <option value="Pending" {{ old('status', $repair->status) == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="In Progress" {{ old('status', $repair->status) == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="Completed" {{ old('status', $repair->status) == 'Completed' ? 'selected' : '' }}>Completed</option>
                        <option value="Collected" {{ old('status', $repair->status) == 'Collected' ? 'selected' : '' }}>Collected</option>
                        <option value="Cancelled" {{ old('status', $repair->status) == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
            </div>

            <div class="row g-3 mb-4 border-top pt-4">
                <div class="col-md-6">
                    <label class="form-label fw-medium text-muted">Repair Type</label>
                    <input type="text" name="repair_type" class="form-control bg-light border-0" value="{{ old('repair_type', $repair->repair_type) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium text-muted">Item / SKU</label>
                    <input type="text" name="sku" class="form-control bg-light border-0" value="{{ old('sku', $repair->sku) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium text-muted">Assigned Staff</label>
                    <input type="text" name="assigned_staff" class="form-control bg-light border-0" value="{{ old('assigned_staff', $repair->assigned_staff) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium text-muted">Estimated Price ($)</label>
                    <input type="number" step="0.01" name="repair_price" class="form-control bg-light border-0 text-success fw-medium" value="{{ old('repair_price', $repair->repair_price) }}">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-medium text-muted">Repair Description & Notes</label>
                <textarea name="repair_notes" rows="3" class="form-control bg-light border-0">{{ old('repair_notes', $repair->repair_notes) }}</textarea>
            </div>
            
            <div class="mb-4 border-top pt-4">
                <label class="form-label fw-medium text-muted">Collection / Outcome Notes</label>
                <textarea name="collection_notes" rows="2" class="form-control bg-light border-0">{{ old('collection_notes', $repair->collection_notes) }}</textarea>
            </div>

            <div class="text-end pt-3 mt-4 border-top">
                <a href="{{ route('repairs.show', $repair) }}" class="btn btn-light me-2 px-4">Cancel</a>
                <button type="submit" class="btn btn-primary px-4 shadow-sm">Update Repair Job</button>
            </div>
        </form>
    </div>
</div>
@endsection
