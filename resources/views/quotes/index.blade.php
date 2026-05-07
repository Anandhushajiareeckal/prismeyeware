@extends('layouts.app')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <h3 class="page-title mb-0">Quotes</h3>
    <a href="{{ route('quotes.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Create Quote</a>
</div>

<div class="card mb-4 shadow-sm border-0">
    <div class="card-body">
        <form action="{{ route('quotes.index') }}" method="GET" class="row g-3 align-items-center">
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Quote No.</label>
                <input type="text" name="quote_number" class="form-control form-control-sm" placeholder="QT-..." value="{{ request('quote_number') }}">
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
                    <option value="Draft" {{ request('status') === 'Draft' ? 'selected' : '' }}>Draft</option>
                    <option value="Sent" {{ request('status') === 'Sent' ? 'selected' : '' }}>Sent</option>
                    <option value="Accepted" {{ request('status') === 'Accepted' ? 'selected' : '' }}>Accepted</option>
                    <option value="Rejected" {{ request('status') === 'Rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="col-md-1 d-flex gap-2 mt-auto">
                <button type="submit" class="btn btn-sm btn-primary w-100" title="Filter"><i class="bi bi-funnel"></i></button>
                <a href="{{ route('quotes.index') }}" class="btn btn-sm btn-light w-100" title="Clear"><i class="bi bi-x-circle"></i></a>
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
                        <th class="border-bottom-0">Quote No.</th>
                        <th class="border-bottom-0">Date</th>
                        <th class="border-bottom-0">Customer</th>
                        <th class="border-bottom-0">Total</th>
                        <th class="border-bottom-0">Status</th>
                        <th class="text-end border-bottom-0">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quotes as $quote)
                    <tr>
                        <td class="fw-medium">
                            <a href="{{ route('quotes.show', $quote) }}" class="text-decoration-none text-dark"><i class="bi bi-receipt text-muted me-1"></i> {{ $quote->quote_number }}</a>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($quote->quote_date)->format('M d, Y') }}</td>
                        <td>
                            @if($quote->customer)
                                <a href="{{ route('customers.show', $quote->customer) }}" class="text-decoration-none fw-medium text-primary">{{ $quote->customer->full_name }}</a>
                            @else
                                <span class="text-muted">Walk-in / Unknown</span>
                            @endif
                        </td>
                        <td class="fw-bold">${{ number_format($quote->total_amount, 2) }}</td>
                        <td>
                            @if($quote->status === 'Accepted')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill">Accepted</span>
                            @elseif($quote->status === 'Sent')
                                <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 rounded-pill">Sent</span>
                            @elseif($quote->status === 'Rejected')
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 rounded-pill">Rejected</span>
                            @else
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 rounded-pill">{{ $quote->status }}</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('quotes.print.a4', $quote) }}" target="_blank" class="btn btn-sm btn-light" title="Print A4"><i class="bi bi-printer"></i></a>
                            <a href="{{ route('quotes.show', $quote) }}" class="btn btn-sm btn-light"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('quotes.edit', $quote) }}" class="btn btn-sm btn-light"><i class="bi bi-pencil"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4 text-muted">No quotes found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($quotes->hasPages())
    <div class="card-footer bg-white border-0 pt-3 pb-3">
        {{ $quotes->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
