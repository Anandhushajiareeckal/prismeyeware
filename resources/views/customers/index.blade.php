@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="page-title mb-0">Customers</h3>
    <a href="{{ route('customers.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> New Customer</a>
</div>

<div class="card mb-4 shadow-sm border-0">
    <div class="card-body">
        <form action="{{ route('customers.index') }}" method="GET" class="row g-3 align-items-center">
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Customer No.</label>
                <input type="text" name="customer_number" class="form-control form-control-sm" placeholder="CUST-..." value="{{ request('customer_number') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Date From</label>
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Date To</label>
                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Customer Name</label>
                <input type="text" name="name" class="form-control form-control-sm" placeholder="Search name..." value="{{ request('name') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Phone</label>
                <input type="text" name="phone" class="form-control form-control-sm" placeholder="Search phone..." value="{{ request('phone') }}">
            </div>
            <div class="col-md-1 d-flex gap-2 mt-auto">
                <button type="submit" class="btn btn-sm btn-primary w-100" title="Filter"><i class="bi bi-funnel"></i></button>
                <a href="{{ route('customers.index') }}" class="btn btn-sm btn-light w-100" title="Clear"><i class="bi bi-x-circle"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Customer No.</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Added</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                    <tr>
                        <td class="fw-medium text-muted">{{ $customer->customer_number }}</td>
                        <td>
                            <a href="{{ route('customers.show', $customer) }}" class="text-decoration-none fw-medium text-dark">
                                {{ $customer->full_name }}
                            </a>
                            @if(($customer->category ?? 'Customer') === 'Shop')
                                <span class="badge bg-secondary-subtle text-secondary ms-1">Shop</span>
                            @endif
                        </td>
                        <td>{{ $customer->phone_number ?? '-' }}</td>
                        <td>{{ $customer->email ?? '-' }}</td>
                        <td>{{ $customer->created_at->format('M d, Y') }}</td>
                        <td class="text-end">
                            <a href="{{ route('customers.show', $customer) }}" class="btn btn-sm btn-light"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-light"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete {{ addslashes($customer->full_name) }}? This cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-light text-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4 text-muted">No customers found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($customers->hasPages())
    <div class="card-footer bg-white border-0 pt-3">
        {{ $customers->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
