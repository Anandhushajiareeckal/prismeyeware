@extends('layouts.app')

@section('content')
<div class="mb-4">
    @if($customer)
        <a href="{{ route('customers.show', $customer) }}" class="text-decoration-none text-muted"><i class="bi bi-arrow-left"></i> Back to {{ $customer->first_name }}</a>
    @else
        <a href="{{ route('prescriptions.index') }}" class="text-decoration-none text-muted"><i class="bi bi-arrow-left"></i> Back to Prescriptions</a>
    @endif
    <h3 class="page-title mt-2 mb-0">New Prescription {{ $customer ? 'for ' . $customer->full_name : '' }}</h3>
</div>

<div class="card shadow-sm">
    <div class="card-body p-4 p-md-5">
        <form action="{{ route('prescriptions.store') }}" method="POST">
            @csrf
            
            <div class="row g-3 mb-4">
                @if(!$customer)
                <div class="col-md-12 mb-3">
                    <label class="form-label fw-medium">Select Customer <span class="text-danger">*</span></label>
                    <select name="customer_id" class="form-select bg-light border-0" required>
                        <option value="">Choose a customer...</option>
                        @foreach(App\Models\Customer::orderBy('first_name')->get() as $c)
                            <option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>{{ $c->full_name }} ({{ $c->customer_number }})</option>
                        @endforeach
                    </select>
                </div>
                @else
                <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                @endif

                <div class="col-md-4">
                    <label class="form-label text-muted fw-medium">Date <span class="text-danger">*</span></label>
                    <input type="date" name="prescription_date" class="form-control bg-light border-0" value="{{ old('prescription_date', date('Y-m-d')) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted fw-medium">Type <span class="text-danger">*</span></label>
                    <select name="type" class="form-select bg-light border-0" required>
                        <option value="Distance" {{ old('type') == 'Distance' ? 'selected' : '' }}>Distance</option>
                        <option value="Reading" {{ old('type') == 'Reading' ? 'selected' : '' }}>Reading</option>
                        <option value="Bifocal" {{ old('type') == 'Bifocal' ? 'selected' : '' }}>Bifocal</option>
                        <option value="Progressive" {{ old('type') == 'Progressive' ? 'selected' : '' }}>Progressive</option>
                        <option value="Contact Lens" {{ old('type') == 'Contact Lens' ? 'selected' : '' }}>Contact Lens</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted fw-medium">Recall Date</label>
                    <input type="date" name="recall_date" class="form-control bg-light border-0" value="{{ old('recall_date') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted fw-medium">Doctor Name</label>
                    <input type="text" name="doctor_name" class="form-control bg-light border-0" value="{{ old('doctor_name') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted fw-medium">Eye <span class="text-danger">*</span></label>
                    <select name="eye_side" class="form-select bg-light border-0" required>
                        <option value="Both" {{ old('eye_side') == 'Both' ? 'selected' : '' }}>Both Eyes</option>
                        <option value="R" {{ old('eye_side') == 'R' ? 'selected' : '' }}>Right (OD)</option>
                        <option value="L" {{ old('eye_side') == 'L' ? 'selected' : '' }}>Left (OS)</option>
                    </select>
                </div>
            </div>

            <h5 class="mb-3 mt-4 text-primary fw-semibold border-bottom pb-2">Measurements</h5>
            <div class="table-responsive mb-4">
                <table class="table table-bordered align-middle">
                    <thead class="table-light text-center">
                        <tr>
                            <th>SPH</th>
                            <th>CYL</th>
                            <th>AXIS</th>
                            <th>PRISM (H)</th>
                            <th>PRISM (V)</th>
                            <th>ADD</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="text" name="sphere" class="form-control text-center bg-light border-0" value="{{ old('sphere') }}"></td>
                            <td><input type="text" name="cylinder" class="form-control text-center bg-light border-0" value="{{ old('cylinder') }}"></td>
                            <td><input type="text" name="axis" class="form-control text-center bg-light border-0" value="{{ old('axis') }}"></td>
                            <td><input type="text" name="h_prism" class="form-control text-center bg-light border-0" value="{{ old('h_prism') }}"></td>
                            <td><input type="text" name="v_prism" class="form-control text-center bg-light border-0" value="{{ old('v_prism') }}"></td>
                            <td><input type="text" name="add" class="form-control text-center bg-light border-0" value="{{ old('add') }}"></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mb-4">
                <label class="form-label text-muted fw-medium">Notes / Comments</label>
                <textarea name="comments" rows="3" class="form-control bg-light border-0">{{ old('comments') }}</textarea>
            </div>

            <div class="text-end pt-3 mt-4 border-top">
                <a href="{{ url()->previous() }}" class="btn btn-light me-2 px-4">Cancel</a>
                <button type="submit" class="btn btn-primary px-4 shadow-sm">Save Prescription</button>
            </div>
        </form>
    </div>
</div>
@endsection
