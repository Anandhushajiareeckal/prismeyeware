@extends('layouts.app')

@section('content')
<div class="mb-4">
    <a href="{{ route('customers.index') }}" class="text-decoration-none text-muted"><i class="bi bi-arrow-left"></i> Back to Customers</a>
    <h3 class="page-title mt-2 mb-0">New Customer</h3>
</div>

<div class="card">
    <div class="card-body p-4 p-md-5">
        <form action="{{ route('customers.store') }}" method="POST">
            @csrf
            
            <h5 class="mb-3 text-primary fw-semibold border-bottom pb-2">Personal Details</h5>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label text-muted fw-medium">First Name <span class="text-danger">*</span></label>
                    <input type="text" name="first_name" class="form-control bg-light border-0 @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted fw-medium">Last Name</label>
                    <input type="text" name="last_name" class="form-control bg-light border-0" value="{{ old('last_name') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted fw-medium">Gender</label>
                    <select name="gender" class="form-select bg-light border-0">
                        <option value="">Select...</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted fw-medium">Date of Birth</label>
                    <input type="date" name="date_of_birth" class="form-control bg-light border-0" value="{{ old('date_of_birth') }}">
                </div>
            </div>

            <h5 class="mb-3 text-primary fw-semibold border-bottom pb-2">Contact Details</h5>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label text-muted fw-medium">Phone Number</label>
                    <input type="text" name="phone_number" class="form-control bg-light border-0" value="{{ old('phone_number') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted fw-medium">Alternate Phone</label>
                    <input type="text" name="alternate_phone_number" class="form-control bg-light border-0" value="{{ old('alternate_phone_number') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted fw-medium">Email Address</label>
                    <input type="email" name="email" class="form-control bg-light border-0" value="{{ old('email') }}">
                </div>
                <div class="col-12">
                    <label class="form-label text-muted fw-medium">Address Line 1</label>
                    <input type="text" name="address_line_1" class="form-control bg-light border-0" value="{{ old('address_line_1') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted fw-medium">City</label>
                    <input type="text" name="city" class="form-control bg-light border-0" value="{{ old('city') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted fw-medium">State / Region</label>
                    <input type="text" name="state" class="form-control bg-light border-0" value="{{ old('state') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted fw-medium">Postal Code</label>
                    <input type="text" name="postal_code" class="form-control bg-light border-0" value="{{ old('postal_code') }}">
                </div>
            </div>

            <h5 class="mb-3 text-primary fw-semibold border-bottom pb-2">Other Info</h5>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label text-muted fw-medium">Referred By</label>
                    <input type="text" name="referred_by" class="form-control bg-light border-0" placeholder="Name or code" value="{{ old('referred_by') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted fw-medium">Preferred Store</label>
                    <input type="text" name="preferred_store" class="form-control bg-light border-0" value="{{ old('preferred_store') }}">
                </div>
            </div>

            <div class="text-end pt-3 mt-4">
                <a href="{{ route('customers.index') }}" class="btn btn-light me-2 px-4">Cancel</a>
                <button type="submit" class="btn btn-primary px-4 shadow-sm">Save Customer</button>
            </div>
        </form>
    </div>
</div>
@endsection
