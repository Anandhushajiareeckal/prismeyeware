@extends('layouts.app')

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <h3 class="page-title mb-0">My Profile</h3>
        <p class="text-muted small mb-0 mt-1">Manage your account name, email, and password.</p>
    </div>
</div>

<div class="row g-4">
    <!-- Profile Info -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white pt-4 pb-3">
                <h5 class="mb-0 fw-semibold text-primary"><i class="bi bi-person-circle me-2"></i>Account Details</h5>
            </div>
            <div class="card-body p-4 p-md-5">
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control bg-light border-0 @error('name') is-invalid @enderror"
                                value="{{ old('name', $user->name) }}" required placeholder="Your name">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control bg-light border-0 @error('email') is-invalid @enderror"
                                value="{{ old('email', $user->email) }}" required placeholder="you@example.com">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <hr class="my-4">
                    <h6 class="fw-semibold text-dark mb-1">Change Password</h6>
                    <p class="text-muted small mb-4">Leave blank to keep your current password.</p>

                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="form-label fw-medium text-muted">Current Password</label>
                            <input type="password" name="current_password"
                                class="form-control bg-light border-0 @error('current_password') is-invalid @enderror"
                                placeholder="••••••••">
                            @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium text-muted">New Password</label>
                            <input type="password" name="new_password"
                                class="form-control bg-light border-0 @error('new_password') is-invalid @enderror"
                                placeholder="Min 8 chars, mixed case, number">
                            @error('new_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium text-muted">Confirm New Password</label>
                            <input type="password" name="new_password_confirmation"
                                class="form-control bg-light border-0"
                                placeholder="Repeat new password">
                        </div>
                    </div>

                    <div class="text-end mt-5 pt-3 border-top">
                        <button type="submit" class="btn btn-primary px-5 shadow-sm">
                            <i class="bi bi-check2-circle me-1"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Info card -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-4 d-flex flex-column align-items-center text-center">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mb-3"
                    style="width:80px; height:80px; font-size:2rem; font-weight:700;">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <h5 class="fw-bold mb-1 text-dark">{{ $user->name }}</h5>
                <p class="text-muted small mb-0">{{ $user->email }}</p>
                <hr class="w-100 my-4">
                <div class="w-100 text-start">
                    <div class="mb-3">
                        <span class="text-muted small fw-bold text-uppercase">Account Created</span><br>
                        <span class="fw-medium text-dark">{{ $user->created_at->format('M d, Y') }}</span>
                    </div>
                    <div>
                        <span class="text-muted small fw-bold text-uppercase">Last Updated</span><br>
                        <span class="fw-medium text-dark">{{ $user->updated_at->format('M d, Y') }}</span>
                    </div>
                </div>
                <div class="mt-auto pt-4 w-100">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="bi bi-box-arrow-right me-2"></i>Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
