@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h3 class="page-title mb-0">Search Results for "{{ $query }}"</h3>
    <p class="text-muted">Found {{ $customers->count() }} customers, {{ $orders->count() }} orders, {{ $repairs->count() }} repairs, and {{ $invoices->count() }} invoices.</p>
</div>

<div class="row g-4">
    @if($customers->count())
    <div class="col-12">
        <h5 class="fw-bold mb-3"><i class="bi bi-people text-primary me-2"></i> Customers ({{ $customers->count() }})</h5>
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="list-group list-group-flush rounded border-0">
                    @foreach($customers as $customer)
                    <a href="{{ route('customers.show', $customer) }}" class="list-group-item list-group-item-action p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <span class="fw-bold text-dark d-block">{{ $customer->full_name }} <span class="badge bg-light text-dark ms-2 fw-normal">{{ $customer->customer_number }}</span></span>
                            <span class="small text-muted d-block mt-1"><i class="bi bi-envelope me-1"></i> {{ $customer->email ?: 'No email' }} &bull; <i class="bi bi-telephone me-1"></i> {{ $customer->phone_number ?: 'No phone' }} &bull; <i class="bi bi-calendar3 me-1"></i> {{ $customer->date_of_birth ? date('M d, Y', strtotime($customer->date_of_birth)) : 'No DOB' }}</span>
                        </div>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($orders->count())
    <div class="col-12">
        <h5 class="fw-bold mb-3"><i class="bi bi-bag text-success me-2"></i> Orders ({{ $orders->count() }})</h5>
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="list-group list-group-flush rounded border-0">
                    @foreach($orders as $order)
                    <a href="{{ route('orders.show', $order) }}" class="list-group-item list-group-item-action p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <span class="fw-bold text-dark d-block">Order #{{ $order->order_number }}</span>
                            <span class="small text-muted d-block mt-1">Date: {{ \Carbon\Carbon::parse($order->order_date)->format('M d, Y') }} &bull; Customer: {{ $order->customer->full_name ?? 'Unknown' }}</span>
                        </div>
                        <span class="badge bg-light text-dark">{{ $order->order_status }}</span>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($repairs->count())
    <div class="col-12">
        <h5 class="fw-bold mb-3"><i class="bi bi-tools text-info me-2"></i> Repairs ({{ $repairs->count() }})</h5>
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="list-group list-group-flush rounded border-0">
                    @foreach($repairs as $repair)
                    <a href="{{ route('repairs.show', $repair) }}" class="list-group-item list-group-item-action p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <span class="fw-bold text-dark d-block">Repair #{{ $repair->repair_number }}</span>
                            <span class="small text-muted d-block mt-1">Type: {{ $repair->repair_type ?: 'Unknown' }} &bull; Item: {{ $repair->sku ?: 'Unknown' }} &bull; Customer: {{ $repair->customer->full_name ?? '-' }}</span>
                        </div>
                        <span class="badge bg-light text-dark">{{ $repair->status }}</span>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($invoices->count())
    <div class="col-12">
        <h5 class="fw-bold mb-3"><i class="bi bi-receipt text-warning me-2"></i> Invoices ({{ $invoices->count() }})</h5>
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="list-group list-group-flush rounded border-0">
                    @foreach($invoices as $invoice)
                    <a href="{{ route('invoices.show', $invoice) }}" class="list-group-item list-group-item-action p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <span class="fw-bold text-dark d-block">Invoice #{{ $invoice->invoice_number }}</span>
                            <span class="small text-muted d-block mt-1">Date: {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('M d, Y') }} &bull; Total: ${{ number_format($invoice->total_amount, 2) }}</span>
                        </div>
                        <span class="badge bg-light text-dark">{{ $invoice->payment_status }}</span>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($customers->isEmpty() && $orders->isEmpty() && $repairs->isEmpty() && $invoices->isEmpty())
    <div class="col-12 text-center py-5">
        <i class="bi bi-search text-muted" style="font-size: 3rem;"></i>
        <h5 class="mt-3">No results found</h5>
        <p class="text-muted">We couldn't find anything matching "{{ $query }}".</p>
        <a href="{{ route('dashboard') }}" class="btn btn-primary mt-2">Back to Dashboard</a>
    </div>
    @endif
</div>
@endsection
