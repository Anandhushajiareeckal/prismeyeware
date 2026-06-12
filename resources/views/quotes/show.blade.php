@extends('layouts.app')

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <a href="{{ route('quotes.index') }}" class="text-decoration-none text-muted"><i class="bi bi-arrow-left"></i> All Quotes</a>
        <h3 class="page-title mt-2 mb-0">Quote: {{ $quote->quote_number }}</h3>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('quotes.print.a4', $quote) }}" target="_blank" class="btn btn-outline-primary"><i class="bi bi-printer"></i> Print A4</a>
        <a href="{{ route('reports.download.quote', $quote) }}" class="btn btn-outline-success"><i class="bi bi-file-earmark-pdf"></i> Download PDF</a>
        <a href="{{ route('quotes.edit', $quote) }}" class="btn btn-primary"><i class="bi bi-pencil"></i> Edit</a>
        <form action="{{ route('quotes.destroy', $quote) }}" method="POST" onsubmit="return confirm('Delete this quote?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
        </form>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header bg-white border-bottom pb-3 pt-4">
                <h5 class="mb-0 fw-semibold text-primary">Details</h5>
            </div>
            <div class="card-body p-4">
                <div class="mb-4">
                    <span class="text-muted small fw-bold text-uppercase tracking-wide">Customer</span><br>
                    @if($quote->customer)
                        <a href="{{ route('customers.show', $quote->customer_id) }}" class="fw-medium fs-5 text-dark text-decoration-none">{{ $quote->customer->full_name }}</a>
                    @else
                        <span class="fw-medium fs-5 text-dark">Walk-in</span>
                    @endif
                </div>
                <div class="mb-3">
                    <span class="text-muted small fw-bold text-uppercase tracking-wide">Status</span><br>
                    @if($quote->status === 'Accepted')
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fs-6">Accepted</span>
                    @elseif($quote->status === 'Sent')
                        <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-2 rounded-pill fs-6">Sent</span>
                    @elseif($quote->status === 'Rejected')
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill fs-6">Rejected</span>
                    @else
                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2 rounded-pill fs-6">{{ $quote->status }}</span>
                    @endif
                </div>
                <div class="mb-3">
                    <span class="text-muted small fw-bold text-uppercase tracking-wide">Date</span><br>
                    <span class="fw-medium text-dark">{{ \Carbon\Carbon::parse($quote->quote_date)->format('M d, Y') }}</span>
                </div>
                @if($quote->order_id)
                <div class="mb-3">
                    <span class="text-muted small fw-bold text-uppercase tracking-wide">Linked Order</span><br>
                    <a href="{{ route('orders.show', $quote->order_id) }}" class="fw-medium">{{ $quote->order->order_number ?? 'Order #' . $quote->order_id }}</a>
                </div>
                @endif
                
                @if($quote->repair_id)
                <div class="mb-3">
                    <span class="text-muted small fw-bold text-uppercase tracking-wide">Linked Repair</span><br>
                    <a href="{{ route('repairs.show', $quote->repair_id) }}" class="fw-medium">{{ $quote->repair->repair_number ?? 'Repair #' . $quote->repair_id }}</a>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header bg-white border-bottom pb-3 pt-4 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold text-primary">Quote Items</h5>
                <span class="badge bg-light text-dark">{{ $quote->items->count() }} items</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Item / Description</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Rate</th>
                                <th class="text-end text-danger">Discount</th>
                                <th class="text-end">Tax</th>
                                <th class="text-end pe-4">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($quote->items as $item)
                            <tr>
                                <td class="ps-4 fw-medium text-dark">
                                    {{ $item->item_name }}
                                    @if($item->sku)
                                    <div class="small text-muted">{{ $item->sku }}</div>
                                    @endif
                                </td>
                                <td class="text-center fw-medium">{{ $item->quantity }}</td>
                                <td class="text-end">${{ number_format($item->rate, 2) }}</td>
                                <td class="text-end text-danger">{{ $item->discount > 0 ? '-$'.number_format($item->discount, 2) : '-' }}</td>
                                <td class="text-end">{{ $item->tax > 0 ? '$'.number_format($item->tax, 2) : '-' }}</td>
                                <td class="text-end pe-4 fw-bold text-dark">${{ number_format(($item->quantity * $item->rate) - $item->discount + $item->tax, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-light border-0 p-4">
                <div class="row">
                    <div class="col-md-6 offset-md-6">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal</span>
                            <span class="fw-medium text-dark">${{ number_format($quote->subtotal, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 text-danger">
                            <span>Total Discount</span>
                            <span>-${{ number_format($quote->discount_amount, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 text-muted">
                            <span>Total Tax</span>
                            <span>+${{ number_format($quote->tax_amount, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between border-top border-secondary pt-3">
                            <span class="fw-bold fs-5 text-dark">Total Due</span>
                            <span class="fw-bold fs-4 text-primary">${{ number_format($quote->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($quote->notes)
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom pb-3 pt-4 border-0">
                <h5 class="mb-0 fw-semibold text-primary">Notes / Terms</h5>
            </div>
            <div class="card-body p-4">
                <p class="mb-0 text-dark" style="white-space: pre-wrap;">{{ $quote->notes }}</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
