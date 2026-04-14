@extends('layouts.app')

@section('content')
<div class="mb-4">
    @if($customer)
        <a href="{{ route('customers.show', $customer) }}" class="text-decoration-none text-muted"><i class="bi bi-arrow-left"></i> Back to {{ $customer->first_name }}</a>
    @else
        <a href="{{ route('repairs.index') }}" class="text-decoration-none text-muted"><i class="bi bi-arrow-left"></i> Back to Repairs</a>
    @endif
    <h3 class="page-title mt-2 mb-0">New Repair Job</h3>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-4 p-md-5">
        <form action="{{ route('repairs.store') }}" method="POST">
            @csrf
            
            <div class="row g-3 mb-4">
                @if(!$customer)
                <div class="col-md-12 mb-3">
                    <label class="form-label fw-medium text-muted">Customer <span class="text-danger">*</span></label>
                    <select name="customer_id" class="form-select bg-light border-0" required>
                        <option value="">Select a customer...</option>
                        @foreach(App\Models\Customer::orderBy('first_name')->get() as $c)
                            <option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>{{ $c->full_name }} ({{ $c->customer_number }})</option>
                        @endforeach
                    </select>
                </div>
                @else
                <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                @endif

                <div class="col-md-4">
                    <label class="form-label fw-medium text-muted">Date <span class="text-danger">*</span></label>
                    <input type="date" name="repair_date" class="form-control bg-light border-0" value="{{ old('repair_date', date('Y-m-d')) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium text-muted">Target Completion</label>
                    <input type="date" name="completion_date" class="form-control bg-light border-0" value="{{ old('completion_date') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium text-muted">Status</label>
                    <select name="status" class="form-select bg-light border-0">
                        <option value="Pending" {{ old('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="In Progress" {{ old('status') == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="Completed" {{ old('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-medium text-muted">Repair Type</label>
                    <input type="text" name="repair_type" class="form-control bg-light border-0" placeholder="e.g. Frame adjustment, nose pad replacement" value="{{ old('repair_type') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium text-muted">Item / SKU</label>
                    <input type="text" name="sku" class="form-control bg-light border-0" placeholder="Product name or SKU being repaired" value="{{ old('sku') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium text-muted">Assigned Staff</label>
                    <input type="text" name="assigned_staff" class="form-control bg-light border-0" value="{{ old('assigned_staff') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium text-muted">Estimated Price ($)</label>
                    <input type="number" step="0.01" name="repair_price" class="form-control bg-light border-0" value="{{ old('repair_price') }}">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-medium text-muted">Repair Description & Notes</label>
                <textarea name="repair_notes" rows="3" class="form-control bg-light border-0" placeholder="Describe the issue and required fixes...">{{ old('repair_notes') }}</textarea>
            </div>

            <div class="text-end pt-3 mt-4 border-top">
                <a href="{{ url()->previous() }}" class="btn btn-light me-2 px-4">Cancel</a>
                <button type="submit" class="btn btn-primary px-4 shadow-sm">Save Repair Job</button>
            </div>
        </form>
    </div>
</div>
@endsection
