@extends('layouts.app')

@section('content')
<div class="mb-4">
    <a href="{{ route('prescriptions.show', $prescription) }}" class="text-decoration-none text-muted"><i class="bi bi-arrow-left"></i> Back to Prescription</a>
    <h3 class="page-title mt-2 mb-0">Edit Prescription</h3>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-4 p-md-5">
        <form action="{{ route('prescriptions.update', $prescription) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row g-3 mb-4">
                <div class="col-md-12 mb-3">
                    <label class="form-label text-muted fw-medium">Customer</label>
                    <input type="text" class="form-control bg-light border-0 text-dark fw-medium" value="{{ $prescription->customer->full_name ?? 'Unknown' }}" readonly disabled>
                </div>

                <div class="col-md-4">
                    <label class="form-label text-muted fw-medium">Date <span class="text-danger">*</span></label>
                    <input type="date" name="prescription_date" class="form-control bg-light border-0" value="{{ old('prescription_date', \Carbon\Carbon::parse($prescription->prescription_date)->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted fw-medium">Type <span class="text-danger">*</span></label>
                    <select name="type" class="form-select bg-light border-0" required>
                        <option value="Distance" {{ old('type', $prescription->type) == 'Distance' ? 'selected' : '' }}>Distance</option>
                        <option value="Reading" {{ old('type', $prescription->type) == 'Reading' ? 'selected' : '' }}>Reading</option>
                        <option value="Bifocal" {{ old('type', $prescription->type) == 'Bifocal' ? 'selected' : '' }}>Bifocal</option>
                        <option value="Progressive" {{ old('type', $prescription->type) == 'Progressive' ? 'selected' : '' }}>Progressive</option>
                        <option value="Contact Lens" {{ old('type', $prescription->type) == 'Contact Lens' ? 'selected' : '' }}>Contact Lens</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted fw-medium">Recall Date</label>
                    <input type="date" name="recall_date" class="form-control bg-light border-0" value="{{ old('recall_date', $prescription->recall_date ? \Carbon\Carbon::parse($prescription->recall_date)->format('Y-m-d') : '') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted fw-medium">Doctor Name</label>
                    <input type="text" name="doctor_name" class="form-control bg-light border-0" value="{{ old('doctor_name', $prescription->doctor_name) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted fw-medium">Eye <span class="text-danger">*</span></label>
                    <select name="eye_side" class="form-select bg-light border-0" required>
                        <option value="Both" {{ old('eye_side', $prescription->eye_side) == 'Both' ? 'selected' : '' }}>Both Eyes</option>
                        <option value="R" {{ old('eye_side', $prescription->eye_side) == 'R' ? 'selected' : '' }}>Right (OD)</option>
                        <option value="L" {{ old('eye_side', $prescription->eye_side) == 'L' ? 'selected' : '' }}>Left (OS)</option>
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
                            <td><input type="text" name="sphere" class="form-control text-center bg-light border-0 fw-medium" value="{{ old('sphere', $prescription->sphere) }}"></td>
                            <td><input type="text" name="cylinder" class="form-control text-center bg-light border-0 fw-medium" value="{{ old('cylinder', $prescription->cylinder) }}"></td>
                            <td><input type="text" name="axis" class="form-control text-center bg-light border-0 fw-medium" value="{{ old('axis', $prescription->axis) }}"></td>
                            <td><input type="text" name="h_prism" class="form-control text-center bg-light border-0 fw-medium" value="{{ old('h_prism', $prescription->h_prism) }}"></td>
                            <td><input type="text" name="v_prism" class="form-control text-center bg-light border-0 fw-medium" value="{{ old('v_prism', $prescription->v_prism) }}"></td>
                            <td><input type="text" name="add" class="form-control text-center bg-light border-0 fw-medium" value="{{ old('add', $prescription->add) }}"></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mb-4">
                <label class="form-label text-muted fw-medium">Notes / Comments</label>
                <textarea name="comments" rows="3" class="form-control bg-light border-0">{{ old('comments', $prescription->comments) }}</textarea>
            </div>

            <div class="text-end pt-3 mt-4 border-top">
                <a href="{{ route('prescriptions.show', $prescription) }}" class="btn btn-light me-2 px-4">Cancel</a>
                <button type="submit" class="btn btn-primary px-4 shadow-sm">Update Prescription</button>
            </div>
        </form>
    </div>
</div>
@endsection
