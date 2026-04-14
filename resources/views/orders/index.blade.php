@extends('layouts.app')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <h3 class="page-title mb-0">Orders</h3>
    <a href="{{ route('orders.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> New Order</a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="border-bottom-0">Order No.</th>
                        <th class="border-bottom-0">Date</th>
                        <th class="border-bottom-0">Customer</th>
                        <th class="border-bottom-0">Total</th>
                        <th class="border-bottom-0">Status</th>
                        <th class="text-end border-bottom-0">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td class="fw-medium">
                            <a href="{{ route('orders.show', $order) }}" class="text-decoration-none text-dark">{{ $order->order_number }}</a>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($order->order_date)->format('M d, Y') }}</td>
                        <td>
                            @if($order->customer)
                                <a href="{{ route('customers.show', $order->customer) }}" class="text-decoration-none fw-medium text-primary">{{ $order->customer->full_name }}</a>
                            @else
                                <span class="text-muted">Unknown</span>
                            @endif
                        </td>
                        <td class="fw-medium">${{ number_format($order->total_amount, 2) }}</td>
                        <td>
                            @php
                                $statusColors = [
                                    'Pending' => 'warning',
                                    'Processing' => 'info',
                                    'Completed' => 'success',
                                    'Cancelled' => 'danger'
                                ];
                                $color = $statusColors[$order->order_status] ?? 'light';
                            @endphp
                            <span class="badge bg-{{ $color }}-subtle text-{{ $color }} border border-{{ $color }}-subtle px-2 py-1 rounded-pill">{{ $order->order_status }}</span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-light"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('orders.edit', $order) }}" class="btn btn-sm btn-light"><i class="bi bi-pencil"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4 text-muted">No orders found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($orders->hasPages())
    <div class="card-footer bg-white border-0 pt-3 pb-3">
        {{ $orders->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
