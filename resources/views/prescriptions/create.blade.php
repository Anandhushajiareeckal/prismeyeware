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
                    <label class="form-label text-muted fw-medium">Expiry Date</label>
                    <input type="date" name="recall_date" class="form-control bg-light border-0" value="{{ old('recall_date') }}">
                </div>
                <div class="col-md-12">
                    <label class="form-label text-muted fw-medium">Optometrist / Practice Name</label>
                    <input type="text" name="doctor_name" class="form-control bg-light border-0" value="{{ old('doctor_name') }}">
                </div>
            </div>

            <h5 class="mb-3 mt-4 text-primary fw-semibold border-bottom pb-2">Measurements</h5>
            <div class="table-responsive mb-4">
                <table class="table table-bordered align-middle">
                    <thead class="table-light text-center">
                        <tr>
                            <th>Eye</th>
                            <th>SPH</th>
                            <th>CYL</th>
                            <th>AXIS</th>
                            <th>PRISM (H)</th>
                            <th>PRISM (V)</th>
                            <th>ADD</th>
                            <th>PD</th>
                            <th>FH</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-bold align-middle">Right (OD)</td>
                            <td><input type="text" name="od_sphere" class="form-control text-center bg-light border-0" value="{{ old('od_sphere') }}"></td>
                            <td><input type="text" name="od_cylinder" class="form-control text-center bg-light border-0" value="{{ old('od_cylinder') }}"></td>
                            <td><input type="text" name="od_axis" class="form-control text-center bg-light border-0" value="{{ old('od_axis') }}"></td>
                            <td><input type="text" name="od_h_prism" class="form-control text-center bg-light border-0" value="{{ old('od_h_prism') }}"></td>
                            <td><input type="text" name="od_v_prism" class="form-control text-center bg-light border-0" value="{{ old('od_v_prism') }}"></td>
                            <td><input type="text" name="od_add" class="form-control text-center bg-light border-0" value="{{ old('od_add') }}"></td>
                            <td><input type="text" name="od_pd" class="form-control text-center bg-light border-0" value="{{ old('od_pd') }}"></td>
                            <td><input type="text" name="od_fh" class="form-control text-center bg-light border-0" value="{{ old('od_fh') }}"></td>
                        </tr>
                        <tr>
                            <td class="fw-bold align-middle">Left (OS)</td>
                            <td><input type="text" name="os_sphere" class="form-control text-center bg-light border-0" value="{{ old('os_sphere') }}"></td>
                            <td><input type="text" name="os_cylinder" class="form-control text-center bg-light border-0" value="{{ old('os_cylinder') }}"></td>
                            <td><input type="text" name="os_axis" class="form-control text-center bg-light border-0" value="{{ old('os_axis') }}"></td>
                            <td><input type="text" name="os_h_prism" class="form-control text-center bg-light border-0" value="{{ old('os_h_prism') }}"></td>
                            <td><input type="text" name="os_v_prism" class="form-control text-center bg-light border-0" value="{{ old('os_v_prism') }}"></td>
                            <td><input type="text" name="os_add" class="form-control text-center bg-light border-0" value="{{ old('os_add') }}"></td>
                            <td><input type="text" name="os_pd" class="form-control text-center bg-light border-0" value="{{ old('os_pd') }}"></td>
                            <td><input type="text" name="os_fh" class="form-control text-center bg-light border-0" value="{{ old('os_fh') }}"></td>
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
