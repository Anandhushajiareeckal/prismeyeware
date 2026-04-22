@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="page-title mb-0">Prescriptions</h3>
    <a href="{{ route('prescriptions.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> New Prescription</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Type</th>
                        <th>Doctor</th>
                        <th>Expiry Date</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($prescriptions as $rx)
                    <tr>
                        <td class="fw-medium text-primary"><a href="{{ route('prescriptions.show', $rx) }}" class="text-decoration-none">{{ \Carbon\Carbon::parse($rx->prescription_date)->format('M d, Y') }}</a></td>
                        <td>
                            @if($rx->customer)
                                <a href="{{ route('customers.show', $rx->customer) }}" class="text-decoration-none fw-medium text-dark">{{ $rx->customer->full_name }}</a>
                            @else
                                <span class="text-muted">Unknown</span>
                            @endif
                        </td>
                        <td><span class="badge bg-light text-dark border">{{ $rx->type }}</span></td>
                        <td>{{ $rx->doctor_name ?? '-' }}</td>
                        <td class="text-muted">{{ $rx->recall_date ? \Carbon\Carbon::parse($rx->recall_date)->format('M d, Y') : '-' }}</td>
                        <td class="text-end">
                            <a href="{{ route('prescriptions.show', $rx) }}" class="btn btn-sm btn-light"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('prescriptions.edit', $rx) }}" class="btn btn-sm btn-light"><i class="bi bi-pencil"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4 text-muted">No prescriptions found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($prescriptions->hasPages())
    <div class="card-footer bg-white border-0 pt-3">
        {{ $prescriptions->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
