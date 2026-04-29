@extends('layouts.app')

@section('content')
<div class="mb-4">
    <a href="{{ route('customers.show', $customer) }}" class="text-decoration-none text-muted"><i class="bi bi-arrow-left"></i> Back to Profile</a>
    <h3 class="page-title mt-2 mb-0">Edit Customer: {{ $customer->full_name }}</h3>
</div>

<div class="card">
    <div class="card-body p-4 p-md-5">
        <form action="{{ route('customers.update', $customer) }}" method="POST">
            @csrf
            @method('PUT')
            
            <h5 class="mb-3 text-primary fw-semibold border-bottom pb-2">Personal Details</h5>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label text-muted fw-medium">First Name <span class="text-danger">*</span></label>
                    <input type="text" name="first_name" class="form-control bg-light border-0 @error('first_name') is-invalid @enderror" value="{{ old('first_name', $customer->first_name) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted fw-medium">Last Name</label>
                    <input type="text" name="last_name" class="form-control bg-light border-0" value="{{ old('last_name', $customer->last_name) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted fw-medium">Business</label>
                    <input type="text" name="business" class="form-control bg-light border-0" value="{{ old('business', $customer->business) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted fw-medium">Gender</label>
                    <select name="gender" class="form-select bg-light border-0">
                        <option value="">Select...</option>
                        <option value="Male" {{ old('gender', $customer->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender', $customer->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Other" {{ old('gender', $customer->gender) == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted fw-medium">Date of Birth</label>
                    <input type="date" name="date_of_birth" class="form-control bg-light border-0" value="{{ old('date_of_birth', $customer->date_of_birth) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted fw-medium">Status</label>
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" name="status" value="1" {{ old('status', $customer->status) ? 'checked' : '' }}>
                        <label class="form-check-label">Active Customer</label>
                    </div>
                </div>
            </div>

            <h5 class="mb-3 text-primary fw-semibold border-bottom pb-2">Contact Details</h5>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label text-muted fw-medium">Phone Number</label>
                    <input type="text" name="phone_number" class="form-control bg-light border-0" value="{{ old('phone_number', $customer->phone_number) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted fw-medium">Alternate Phone</label>
                    <input type="text" name="alternate_phone_number" class="form-control bg-light border-0" value="{{ old('alternate_phone_number', $customer->alternate_phone_number) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted fw-medium">Email Address</label>
                    <input type="email" name="email" class="form-control bg-light border-0" value="{{ old('email', $customer->email) }}">
                </div>
                <div class="col-12">
                    <label class="form-label text-muted fw-medium">Address Line 1</label>
                    <input type="text" name="address_line_1" class="form-control bg-light border-0" value="{{ old('address_line_1', $customer->address_line_1) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted fw-medium">City</label>
                    <input type="text" name="city" class="form-control bg-light border-0" value="{{ old('city', $customer->city) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted fw-medium">State / Region</label>
                    <input type="text" name="state" class="form-control bg-light border-0" value="{{ old('state', $customer->state) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted fw-medium">Postal Code</label>
                    <input type="text" name="postal_code" class="form-control bg-light border-0" value="{{ old('postal_code', $customer->postal_code) }}">
                </div>
            </div>

            <div class="text-end pt-3 mt-4">
                <a href="{{ route('customers.show', $customer) }}" class="btn btn-light me-2 px-4">Cancel</a>
                <button type="submit" class="btn btn-primary px-4 shadow-sm">Update Customer</button>
            </div>
        </form>
    </div>
</div>
@endsection
