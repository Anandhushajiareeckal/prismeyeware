@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h3 class="page-title mt-2 mb-0">Repair Types Configuration</h3>
</div>

<div class="row">
    <div class="col-md-5">
        <div class="card shadow-sm border-0 mb-4 mb-md-0">
            <div class="card-header bg-white border-bottom pb-3 pt-4">
                <h5 class="mb-0 fw-semibold text-primary">Add New Repair Type</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('repair-types.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-muted fw-medium">Repair Type Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control bg-light border-0" placeholder="e.g., Frame Adjustment" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted fw-medium">Status</label>
                        <select name="status" class="form-select bg-light border-0">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary shadow-sm px-4">Add Type</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-7">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom pb-3 pt-4">
                <h5 class="mb-0 fw-semibold text-primary">Managed Repair Types</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-muted fw-bold text-uppercase small py-3 ps-4 border-bottom-0">Name</th>
                                <th class="text-muted fw-bold text-uppercase small py-3 border-bottom-0">Status</th>
                                <th class="text-end py-3 pe-4 border-bottom-0"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($repairTypes as $type)
                            <tr>
                                <td class="fw-medium text-dark ps-4">{{ $type->name }}</td>
                                <td>
                                    <span class="badge {{ $type->status === 'Active' ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-secondary-subtle text-secondary border border-secondary-subtle' }} rounded-pill px-2">
                                        {{ $type->status ?? 'Active' }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <form action="{{ route('repair-types.destroy', $type) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this repair type?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light text-danger rounded-circle"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted py-5">No repair types configured yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
