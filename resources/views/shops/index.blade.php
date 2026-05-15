@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="page-title mb-0">Shops</h3>
    <a href="{{ route('shops.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> New Shop</a>
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
                            <a href="{{ route('shops.show', $customer) }}" class="text-decoration-none fw-medium text-dark">
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
                            <a href="{{ route('shops.show', $customer) }}" class="btn btn-sm btn-light"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('shops.edit', $customer) }}" class="btn btn-sm btn-light"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('shops.destroy', $customer) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete {{ addslashes($customer->full_name) }}? This cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-light text-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4 text-muted">No shops found.</td></tr>
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
