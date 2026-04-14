@extends('layouts.app')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <h3 class="page-title mb-0">Billing & Invoices</h3>
    <a href="{{ route('invoices.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Create Invoice</a>
</div>

<div class="card mb-4 shadow-sm border-0">
    <div class="card-body">
        <form action="{{ route('invoices.index') }}" method="GET" class="row g-3 align-items-center">
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Invoice No.</label>
                <input type="text" name="invoice_number" class="form-control form-control-sm" placeholder="INV-..." value="{{ request('invoice_number') }}">
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
                <input type="text" name="customer_name" class="form-control form-control-sm" placeholder="Search customer..." value="{{ request('customer_name') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    <option value="Paid" {{ request('status') === 'Paid' ? 'selected' : '' }}>Paid</option>
                    <option value="Unpaid" {{ request('status') === 'Unpaid' ? 'selected' : '' }}>Unpaid</option>
                    <option value="Partial" {{ request('status') === 'Partial' ? 'selected' : '' }}>Partial</option>
                </select>
            </div>
            <div class="col-md-1 d-flex gap-2 mt-auto">
                <button type="submit" class="btn btn-sm btn-primary w-100" title="Filter"><i class="bi bi-funnel"></i></button>
                <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-light w-100" title="Clear"><i class="bi bi-x-circle"></i></a>
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
                        <th class="border-bottom-0">Invoice No.</th>
                        <th class="border-bottom-0">Date</th>
                        <th class="border-bottom-0">Customer</th>
                        <th class="border-bottom-0">Total</th>
                        <th class="border-bottom-0">Status</th>
                        <th class="text-end border-bottom-0">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                    <tr>
                        <td class="fw-medium">
                            <a href="{{ route('invoices.show', $invoice) }}" class="text-decoration-none text-dark"><i class="bi bi-receipt text-muted me-1"></i> {{ $invoice->invoice_number }}</a>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('M d, Y') }}</td>
                        <td>
                            @if($invoice->customer)
                                <a href="{{ route('customers.show', $invoice->customer) }}" class="text-decoration-none fw-medium text-primary">{{ $invoice->customer->full_name }}</a>
                            @else
                                <span class="text-muted">Walk-in / Unknown</span>
                            @endif
                        </td>
                        <td class="fw-bold">${{ number_format($invoice->total_amount, 2) }}</td>
                        <td>
                            @if($invoice->payment_status === 'Paid')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill">Paid</span>
                            @elseif($invoice->payment_status === 'Partial')
                                <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 rounded-pill">Partial</span>
                            @else
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 rounded-pill">{{ $invoice->payment_status }}</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('invoices.print.a4', $invoice) }}" target="_blank" class="btn btn-sm btn-light" title="Print A4"><i class="bi bi-printer"></i></a>
                            <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-sm btn-light"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-sm btn-light"><i class="bi bi-pencil"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4 text-muted">No invoices found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($invoices->hasPages())
    <div class="card-footer bg-white border-0 pt-3 pb-3">
        {{ $invoices->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
