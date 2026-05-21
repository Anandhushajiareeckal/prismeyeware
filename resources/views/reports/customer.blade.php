@extends('layouts.app')

@section('title', 'Report: ' . $customer->full_name)

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <a href="{{ route('reports.index') }}" class="text-decoration-none text-muted"><i class="bi bi-arrow-left"></i> Back to Shops</a>
        <h3 class="page-title mt-2 mb-0">Report: {{ $customer->full_name }}</h3>
    </div>
    <div class="d-flex gap-2">
        {{-- Download ALL (filtered) records as CSV --}}
        <a href="{{ route('reports.download.customer.all', array_merge(['customer' => $customer->id], request()->only(['date_from','date_to','invoice_number','reference','status']))) }}"
           class="btn btn-outline-success">
            <i class="bi bi-file-earmark-pdf me-1"></i> Download All (PDF)
        </a>
    </div>
</div>

{{-- ── Filter Form ── --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body bg-light rounded-top border-bottom p-4">
        <form action="{{ route('reports.customer', $customer) }}" method="GET" id="filterForm" class="row g-3 align-items-end">
            <div class="col-md-2">
                <label class="form-label small text-muted fw-bold">Date From</label>
                <input type="date" name="date_from" class="form-control border-0" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted fw-bold">Date To</label>
                <input type="date" name="date_to" class="form-control border-0" value="{{ request('date_to') }}">
            </div>

            <div class="col-md-2">
                <label class="form-label small text-muted fw-bold">Reference #</label>
                <input type="text" name="reference" class="form-control border-0" value="{{ request('reference') }}" placeholder="Order/Repair No.">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted fw-bold">Status</label>
                <select name="status" class="form-select border-0">
                    <option value="All" {{ request('status', 'All') === 'All' ? 'selected' : '' }}>All</option>
                    <option value="Paid" {{ request('status') === 'Paid' ? 'selected' : '' }}>Paid</option>
                    <option value="Unpaid" {{ request('status') === 'Unpaid' ? 'selected' : '' }}>Unpaid</option>
                    <option value="Partial" {{ request('status') === 'Partial' ? 'selected' : '' }}>Partial</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">Filter</button>
                <a href="{{ route('reports.customer', $customer) }}" class="btn btn-light"><i class="bi bi-x-circle"></i></a>
            </div>
        </form>
    </div>

    {{-- ── Bulk Actions Bar ── --}}
    <div class="card-body border-bottom bg-white d-flex justify-content-between align-items-center py-2 px-4">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="selectAll">
            <label class="form-check-label fw-bold" for="selectAll">Select All</label>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-outline-primary" id="btn-print-selected" disabled onclick="submitBulkAction('print')">
                <i class="bi bi-printer me-1"></i> Print Selected (A4)
            </button>
            <button type="button" class="btn btn-sm btn-outline-success" id="btn-download-selected" disabled onclick="submitBulkAction('download')">
                <i class="bi bi-file-earmark-pdf me-1"></i> Download Selected (PDF)
            </button>
        </div>
    </div>

    {{-- Hidden bulk-action forms that carry checkbox values + filter params --}}
    @php $filterParams = request()->only(['date_from','date_to','invoice_number','reference','status']); @endphp

    <form id="form-print-bulk" action="{{ route('reports.print') }}" method="GET" target="_blank">
        @foreach($filterParams as $k => $v)
            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
        @endforeach
        <div id="print-checkboxes-hidden"></div>
    </form>

    <form id="form-download-bulk" action="{{ route('reports.download.bulk') }}" method="GET">
        @foreach($filterParams as $k => $v)
            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
        @endforeach
        <div id="download-checkboxes-hidden"></div>
    </form>

    {{-- ── Invoice Table ── --}}
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="border-bottom-0 ps-4" style="width: 40px;"></th>
                        <th class="border-bottom-0">Date</th>

                        <th class="border-bottom-0">Reference</th>
                        <th class="border-bottom-0 text-end">Amount</th>
                        <th class="border-bottom-0 text-center">Status</th>
                        <th class="text-end border-bottom-0 pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                    <tr>
                        <td class="ps-4">
                            <div class="form-check">
                                <input class="form-check-input row-checkbox" type="checkbox" value="{{ $invoice->id }}" id="chk-{{ $invoice->id }}">
                            </div>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('M d, Y') }}</td>

                        <td>
                            @if($invoice->repair_id)
                                @if($invoice->repair->reference)
                                    <a href="{{ route('repairs.show', $invoice->repair_id) }}" class="fw-medium text-decoration-none">{{ $invoice->repair->reference }}</a>
                                @else
                                    <a href="{{ route('repairs.show', $invoice->repair_id) }}" class="fw-medium text-decoration-none">{{ $invoice->repair->repair_number }}</a>
                                @endif
                            @elseif($invoice->order_id)
                                <a href="{{ route('orders.show', $invoice->order_id) }}" class="fw-medium text-decoration-none">{{ $invoice->order->order_number }}</a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-end fw-medium">${{ number_format($invoice->total_amount, 2) }}</td>
                        <td class="text-center">
                            @if($invoice->payment_status === 'Paid')
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2">Paid</span>
                            @elseif($invoice->payment_status === 'Partial')
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2">Partial</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2">Unpaid</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group">
                                <a href="{{ route('invoices.print.a4', $invoice) }}" target="_blank"
                                   class="btn btn-sm btn-light text-primary" title="Print A4">
                                    <i class="bi bi-printer"></i>
                                </a>
                                <a href="{{ route('reports.download.single', $invoice) }}"
                                   class="btn btn-sm btn-light text-success" title="Download PDF">
                                    <i class="bi bi-file-earmark-pdf"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-5 text-muted">No invoices found with the current filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($invoices->hasPages())
    <div class="card-footer bg-white border-0 pt-3 pb-3">
        {{ $invoices->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAll     = document.getElementById('selectAll');
    const checkboxes    = document.querySelectorAll('.row-checkbox');
    const printBtn      = document.getElementById('btn-print-selected');
    const downloadBtn   = document.getElementById('btn-download-selected');

    function updateButtonState() {
        const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
        printBtn.disabled    = !anyChecked;
        downloadBtn.disabled = !anyChecked;
    }

    selectAll.addEventListener('change', function () {
        checkboxes.forEach(cb => cb.checked = selectAll.checked);
        updateButtonState();
    });

    checkboxes.forEach(cb => {
        cb.addEventListener('change', function () {
            selectAll.checked = Array.from(checkboxes).every(cb => cb.checked);
            updateButtonState();
        });
    });

    /**
     * Collect checked IDs, inject hidden inputs into the right form, then submit.
     * @param {'print'|'download'} action
     */
    window.submitBulkAction = function (action) {
        const checkedIds = Array.from(checkboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);

        if (!checkedIds.length) return;

        const formId     = action === 'print' ? 'form-print-bulk'    : 'form-download-bulk';
        const containerId = action === 'print' ? 'print-checkboxes-hidden' : 'download-checkboxes-hidden';
        const form       = document.getElementById(formId);
        const container  = document.getElementById(containerId);

        // Clear previous hidden inputs
        container.innerHTML = '';

        // Inject fresh ones
        checkedIds.forEach(id => {
            const input = document.createElement('input');
            input.type  = 'hidden';
            input.name  = 'invoices[]';
            input.value = id;
            container.appendChild(input);
        });

        form.submit();
    };
});
</script>
@endpush
@endsection
