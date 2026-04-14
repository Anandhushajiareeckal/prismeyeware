@extends('layouts.app')

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <a href="{{ route('orders.index') }}" class="text-decoration-none text-muted"><i class="bi bi-arrow-left"></i> All Orders</a>
        <h3 class="page-title mt-2 mb-0">Order: {{ $order->order_number }}</h3>
    </div>
    <div>
        @if($order->order_status !== 'Completed')
            <a href="{{ route('invoices.create', ['customer_id' => $order->customer_id, 'order_id' => $order->id]) }}" class="btn btn-success me-2"><i class="bi bi-receipt"></i> Generate Invoice</a>
        @endif
        <a href="{{ route('orders.edit', $order) }}" class="btn btn-primary"><i class="bi bi-pencil"></i> Edit</a>
        <form action="{{ route('orders.destroy', $order) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this order?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger"><i class="bi bi-trash"></i> Delete</button>
        </form>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header bg-white border-bottom pb-3 pt-4">
                <h5 class="mb-0 fw-semibold text-primary">Order Summary</h5>
            </div>
            <div class="card-body p-4">
                <div class="mb-4">
                    <span class="text-muted small fw-bold text-uppercase tracking-wide">Customer</span><br>
                    <a href="{{ route('customers.show', $order->customer_id) }}" class="fw-medium fs-5 text-dark text-decoration-none">{{ $order->customer->full_name ?? 'Unknown' }}</a>
                </div>
                <div class="mb-3">
                    <span class="text-muted small fw-bold text-uppercase tracking-wide">Status</span><br>
                    @php
                        $statusColors = [
                            'Pending' => 'warning',
                            'Processing' => 'info',
                            'Completed' => 'success',
                            'Cancelled' => 'danger'
                        ];
                        $color = $statusColors[$order->order_status] ?? 'light';
                    @endphp
                    <span class="badge bg-{{ $color }}-subtle text-{{ $color }} border border-{{ $color }}-subtle px-3 py-2 rounded-pill fs-6">{{ $order->order_status }}</span>
                </div>
                <div class="mb-3">
                    <span class="text-muted small fw-bold text-uppercase tracking-wide">Order Date</span><br>
                    <span class="fw-medium text-dark">{{ \Carbon\Carbon::parse($order->order_date)->format('M d, Y') }}</span>
                </div>
                <div class="mb-0">
                    <span class="text-muted small fw-bold text-uppercase tracking-wide">Sales Staff</span><br>
                    <span class="fw-medium text-dark">{{ $order->sales_staff ?? 'Unassigned' }}</span>
                </div>
            </div>
            <div class="card-footer bg-light border-0 p-4">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal</span>
                    <span class="fw-medium">${{ number_format($order->total_amount + $order->discount_amount - $order->tax_amount, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2 text-danger">
                    <span>Discount</span>
                    <span>-${{ number_format($order->discount_amount, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-3 text-muted">
                    <span>Tax</span>
                    <span>+${{ number_format($order->tax_amount, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between border-top pt-3">
                    <span class="fw-bold fs-5 text-dark">Total</span>
                    <span class="fw-bold fs-5 text-primary">${{ number_format($order->total_amount, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header bg-white border-bottom pb-3 pt-4 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold text-primary">Line Items</h5>
                <span class="badge bg-light text-dark">{{ $order->items->count() }} items</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Product / Service</th>
                                <th>Category</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Rate</th>
                                <th class="text-end text-danger">Discount</th>
                                <th class="text-end pe-4">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td class="ps-4 fw-medium text-dark">
                                    {{ $item->product_name }}
                                    @if($item->sku)
                                    <div class="small text-muted">{{ $item->sku }}</div>
                                    @endif
                                </td>
                                <td><span class="badge bg-light text-dark border">{{ $item->category ?: 'General' }}</span></td>
                                <td class="text-center fw-medium">{{ $item->quantity }}</td>
                                <td class="text-end">${{ number_format($item->unit_price, 2) }}</td>
                                <td class="text-end text-danger">{{ $item->discount > 0 ? '-$'.number_format($item->discount, 2) : '-' }}</td>
                                <td class="text-end pe-4 fw-bold text-dark">${{ number_format(($item->quantity * $item->unit_price) - $item->discount + $item->tax, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        @if($order->invoices && $order->invoices->count() > 0)
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom pb-3 pt-4">
                <h5 class="mb-0 fw-semibold text-primary">Related Invoices</h5>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @foreach($order->invoices as $invoice)
                    <a href="{{ route('invoices.show', $invoice) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-4">
                        <div>
                            <div class="fw-bold text-dark mb-1">{{ $invoice->invoice_number }}</div>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('M d, Y') }}</small>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold text-primary mb-1">${{ number_format($invoice->total_amount, 2) }}</div>
                            @if($invoice->payment_status === 'Paid')
                                <span class="badge bg-success-subtle text-success">Paid via {{ $invoice->payment_mode ?? 'System' }}</span>
                            @else
                                <span class="badge bg-warning-subtle text-warning">{{ $invoice->payment_status }}</span>
                            @endif
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
