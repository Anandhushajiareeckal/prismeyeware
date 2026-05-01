@extends('layouts.app')

@section('title', 'Customer Reports')

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center">
    <h3 class="page-title mb-0">Customer Reports</h3>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body bg-light rounded-top border-bottom p-4">
        <form action="{{ route('reports.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-5 position-relative">
                <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                <input type="text" name="search" class="form-control bg-white border-0 ps-5" placeholder="Search by name, phone, or customer number..." value="{{ request('search') }}">
            </div>
            <div class="col-md-auto">
                <button type="submit" class="btn btn-primary px-4">Search</button>
                @if(request()->has('search'))
                    <a href="{{ route('reports.index') }}" class="btn btn-light ms-2">Clear</a>
                @endif
            </div>
        </form>
    </div>
    
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="border-bottom-0 ps-4">Customer</th>
                        <th class="border-bottom-0">Phone</th>
                        <th class="border-bottom-0">Email</th>
                        <th class="text-end border-bottom-0 pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                    <tr style="cursor: pointer;" onclick="window.location='{{ route('reports.customer', $customer) }}'">
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold me-3" style="width: 40px; height: 40px;">
                                    {{ strtoupper(substr($customer->first_name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $customer->full_name }}</div>
                                    <div class="text-muted small">{{ $customer->customer_number }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $customer->phone_number ?? '-' }}</td>
                        <td>{{ $customer->email ?? '-' }}</td>
                        <td class="text-end pe-4">
                            <a href="{{ route('reports.customer', $customer) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">View Report</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center py-5 text-muted">No customers found matching your criteria.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($customers->hasPages())
    <div class="card-footer bg-white border-0 pt-3 pb-3">
        {{ $customers->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
