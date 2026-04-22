@extends('layouts.app')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <h3 class="page-title mb-0">Repair Jobs</h3>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary" type="button" data-bs-toggle="offcanvas" data-bs-target="#repairTypesPanel">
            <i class="bi bi-tools me-1"></i> Repair Types
        </button>
        <a href="{{ route('repairs.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> New Repair</a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="border-bottom-0">Repair No.</th>
                        <th class="border-bottom-0">Date</th>
                        <th class="border-bottom-0">Customer</th>
                        <th class="border-bottom-0">Type</th>
                        <th class="border-bottom-0">Status</th>
                        <th class="text-end border-bottom-0">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($repairs as $repair)
                    <tr>
                        <td class="fw-medium">
                            <a href="{{ route('repairs.show', $repair) }}" class="text-decoration-none text-dark">{{ $repair->repair_number }}</a>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($repair->repair_date)->format('M d, Y') }}</td>
                        <td>
                            @if($repair->customer)
                                <a href="{{ route('customers.show', $repair->customer) }}" class="text-decoration-none fw-medium text-primary">{{ $repair->customer->full_name }}</a>
                            @else
                                <span class="text-muted">Unknown</span>
                            @endif
                        </td>
                        <td>
                            @if($repair->items && $repair->items->count())
                                <span class="small text-muted">{{ $repair->items->pluck('repair_type')->join(', ') }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
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
                            <span class="badge bg-{{ $color }}-subtle text-{{ $color }} border border-{{ $color }}-subtle px-2 py-1 rounded-pill">{{ $repair->status }}</span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('repairs.show', $repair) }}" class="btn btn-sm btn-light"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('repairs.edit', $repair) }}" class="btn btn-sm btn-light"><i class="bi bi-pencil"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4 text-muted">No repair jobs found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($repairs->hasPages())
    <div class="card-footer bg-white border-0 pt-3 pb-3">
        {{ $repairs->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

{{-- Repair Types Offcanvas Panel --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="repairTypesPanel" style="width: 420px;">
    <div class="offcanvas-header border-bottom py-3">
        <h5 class="offcanvas-title fw-semibold text-primary"><i class="bi bi-tools me-2"></i>Repair Types</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-0 d-flex flex-column">

        {{-- Add Form --}}
        <div class="p-4 border-bottom bg-light">
            <p class="text-muted small mb-3">Add new repair types that will appear as options when creating repair jobs.</p>
            <form action="{{ route('repair-types.store') }}" method="POST">
                @csrf
                <div class="row g-2 align-items-end">
                    <div class="col-6">
                        <label class="form-label text-muted fw-medium small mb-1">Type Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control-sm bg-white shadow-sm border-0" placeholder="e.g., Welding..." required>
                    </div>
                    <div class="col-4">
                        <label class="form-label text-muted fw-medium small mb-1">Status</label>
                        <select name="status" class="form-select form-select-sm bg-white shadow-sm border-0">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="col-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100">Add</button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Repair Types List --}}
        <div class="flex-grow-1 overflow-auto">
            @php $repairTypes = \App\Models\RepairType::orderBy('name')->get(); @endphp
            @if($repairTypes->count())
            <ul class="list-group list-group-flush">
                @foreach($repairTypes as $type)
                <li class="list-group-item d-flex justify-content-between align-items-center py-3 px-4">
                    <div>
                        <span class="fw-medium text-dark">{{ $type->name }}</span>
                        <span class="ms-2 badge {{ $type->status === 'Active' ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-secondary-subtle text-secondary border border-secondary-subtle' }} rounded-pill px-2 small">
                            {{ $type->status ?? 'Active' }}
                        </span>
                    </div>
                    <form action="{{ route('repair-types.destroy', $type) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this repair type?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-light text-danger rounded-circle border-0"><i class="bi bi-trash"></i></button>
                    </form>
                </li>
                @endforeach
            </ul>
            @else
            <div class="text-center text-muted py-5">
                <i class="bi bi-tools fs-1 opacity-25 d-block mb-3"></i>
                <span class="small">No repair types configured yet.</span>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Re-open offcanvas if we just came back from a successful form submit
    @if(session('success') && str_contains(url()->previous(), 'repair-types'))
        document.addEventListener('DOMContentLoaded', function() {
            new bootstrap.Offcanvas(document.getElementById('repairTypesPanel')).show();
        });
    @endif
</script>
@endpush
@endsection
