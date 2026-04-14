@extends('layouts.app')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <h3 class="page-title mb-0">Repair Jobs</h3>
    <a href="{{ route('repairs.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> New Repair</a>
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
                        <td>{{ $repair->repair_type ?: '-' }}</td>
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
@endsection
