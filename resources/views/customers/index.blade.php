@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="page-title mb-0">Customers</h3>
    <a href="{{ route('customers.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> New Customer</a>
</div>

<div class="card">
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
                        </td>
                        <td>{{ $customer->phone_number ?? '-' }}</td>
                        <td>{{ $customer->email ?? '-' }}</td>
                        <td>{{ $customer->created_at->format('M d, Y') }}</td>
                        <td class="text-end">
                            <a href="{{ route('customers.show', $customer) }}" class="btn btn-sm btn-light"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-light"><i class="bi bi-pencil"></i></a>
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
