@extends('layouts.app')

@section('title', 'Report: ' . $customer->full_name)

@push('styles')
<style>
    /* Bulk Action Bar Styles */
    .bulk-bar {
        transition: all 0.25s ease-in-out;
        background-color: #ffffff;
    }
    .bulk-bar.has-selection {
        background: linear-gradient(135deg, #f0f7ff 0%, #e6f0fa 100%) !important;
        border-bottom-color: #b6d4fe !important;
        box-shadow: inset 0 2px 4px rgba(26, 108, 219, 0.04);
    }

    /* Row highlight when selected */
    .table.invoice-table tr.table-row-selected {
        background-color: #f0f7ff !important;
        transition: background-color 0.15s ease-in-out;
    }
    .table.invoice-table tr.table-row-selected td:first-child {
        border-left: 3px solid #1a6cdb !important;
    }

    /* Pill Counter Badge */
    .selection-counter-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #eef5ff;
        color: #0d6efd;
        font-weight: 600;
        font-size: 13px;
        padding: 6px 16px;
        border-radius: 50rem;
        border: 1px solid #cce0ff;
        animation: pillPop 0.25s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .selection-counter-pill .badge-context {
        background: #0d6efd;
        color: #ffffff;
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 11.5px;
        font-weight: 600;
    }

    /* Select all pages notification banner */
    .selection-banner-alert {
        background: #f8faff;
        border: 1px dashed #a5c8ff;
        color: #084298;
        border-radius: 10px;
        padding: 12px 20px;
        font-size: 14px;
        margin-top: 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        animation: bannerSlideDown 0.25s ease-out;
    }

    /* Floating bottom action bar */
    .floating-bulk-bar {
        position: fixed;
        bottom: 24px;
        left: 50%;
        transform: translateX(-50%) translateY(100px);
        z-index: 1050;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid #cce0ff;
        box-shadow: 0 12px 36px rgba(15, 23, 42, 0.18);
        border-radius: 50rem;
        padding: 8px 16px 8px 20px;
        display: flex;
        align-items: center;
        gap: 14px;
        transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.25s ease;
        opacity: 0;
        pointer-events: none;
    }
    .floating-bulk-bar.show {
        transform: translateX(-50%) translateY(0);
        opacity: 1;
        pointer-events: auto;
    }

    /* Keyframes */
    @keyframes pillPop {
        0% { transform: scale(0.85); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }
    @keyframes bannerSlideDown {
        0% { opacity: 0; transform: translateY(-8px); }
        100% { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

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
    <div class="card-body border-bottom py-3 px-4 bulk-bar" id="bulk-bar">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <div class="form-check me-1 mb-0 d-flex align-items-center">
                    <input class="form-check-input mt-0" type="checkbox" id="selectAll" style="cursor: pointer; width: 1.15em; height: 1.15em;">
                    <label class="form-check-label fw-bold text-dark ms-2" for="selectAll" style="cursor: pointer;">
                        Select Page
                    </label>
                </div>

                {{-- Counter Pill --}}
                <div id="selection-pill" class="selection-counter-pill" style="display: none;">
                    <i class="bi bi-check2-circle fs-6"></i>
                    <span id="selected-count-text">0 selected</span>
                    <span class="badge-context" id="selected-total-context"> of {{ count($allInvoiceIds) }}</span>
                </div>

                <button type="button" class="btn btn-link btn-sm p-0 text-danger text-decoration-none fw-medium" id="btn-clear-selection" style="display: none; font-size: 13px;">
                    <i class="bi bi-x-circle me-1"></i>Clear selection
                </button>
            </div>

            <div class="d-flex gap-2 align-items-center flex-wrap mt-1 mt-md-0">
                <button type="button" class="btn btn-sm btn-primary px-3 rounded-2 fw-medium shadow-sm d-flex align-items-center gap-2" id="btn-print-selected" disabled onclick="submitBulkAction('print')">
                    <i class="bi bi-printer"></i>
                    <span>Print Selected</span>
                    <span class="badge bg-white text-primary ms-1 rounded-pill px-2 py-1" id="badge-print-count" style="display:none; font-size: 11px;">0</span>
                </button>
                <button type="button" class="btn btn-sm btn-success px-3 rounded-2 fw-medium shadow-sm d-flex align-items-center gap-2" id="btn-download-selected" disabled onclick="submitBulkAction('download')">
                    <i class="bi bi-file-earmark-pdf"></i>
                    <span>Download PDF</span>
                    <span class="badge bg-white text-success ms-1 rounded-pill px-2 py-1" id="badge-download-count" style="display:none; font-size: 11px;">0</span>
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger px-3 rounded-2 fw-medium d-flex align-items-center gap-2 bg-white" id="btn-delete-selected" disabled onclick="submitBulkAction('delete')">
                    <i class="bi bi-trash"></i>
                    <span>Delete</span>
                    <span class="badge bg-danger text-white ms-1 rounded-pill px-2 py-1" id="badge-delete-count" style="display:none; font-size: 11px;">0</span>
                </button>
            </div>
        </div>

        {{-- Banner Alert Strip for Select All Pages --}}
        <div id="selection-banner-alert" class="selection-banner-alert" style="display: none;">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-info-circle-fill text-primary fs-5"></i>
                <span id="banner-text">All items on this page are selected.</span>
            </div>
            <button type="button" class="btn btn-primary btn-sm rounded-pill px-4 py-1 fw-semibold shadow-sm text-nowrap" id="btn-select-all-pages">
                Select all {{ count($allInvoiceIds) }} items
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

    <form id="form-delete-bulk" action="{{ route('reports.delete') }}" method="POST">
        @csrf
        <div id="delete-checkboxes-hidden"></div>
    </form>

    {{-- ── Invoice Table ── --}}
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 invoice-table">
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
                    <tr id="row-{{ $invoice->id }}" class="invoice-row-tr">
                        <td class="ps-4">
                            <div class="form-check">
                                <input class="form-check-input row-checkbox" type="checkbox" value="{{ $invoice->id }}" id="chk-{{ $invoice->id }}" style="cursor: pointer;">
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
                                <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this report? This action cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light text-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
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

{{-- ── Floating Bottom Action Bar (Appears when scrolling down) ── --}}
<div id="floating-bulk-bar" class="floating-bulk-bar">
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-primary rounded-pill px-3 py-2 fw-semibold fs-7" id="floating-count-badge">0 selected</span>
        <button type="button" class="btn btn-link btn-sm text-muted p-0 text-decoration-none ms-1" id="floating-btn-clear">
            <i class="bi bi-x-circle me-1"></i>Clear
        </button>
    </div>
    <div class="vr opacity-25" style="height: 20px;"></div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm" onclick="submitBulkAction('print')">
            <i class="bi bi-printer me-1"></i> Print (A4)
        </button>
        <button type="button" class="btn btn-sm btn-success rounded-pill px-3 shadow-sm" onclick="submitBulkAction('download')">
            <i class="bi bi-file-earmark-pdf me-1"></i> Download PDF
        </button>
        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 shadow-sm" onclick="submitBulkAction('delete')">
            <i class="bi bi-trash me-1"></i> Delete
        </button>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const customerId = {{ $customer->id }};
    const storageKey = 'selected_invoices_cust_' + customerId;

    const ALL_INVOICE_IDS  = @json($allInvoiceIds);
    const PAGE_INVOICE_IDS = @json($invoices->pluck('id'));

    const bulkBar            = document.getElementById('bulk-bar');
    const selectAll          = document.getElementById('selectAll');
    const checkboxes         = document.querySelectorAll('.row-checkbox');
    const printBtn           = document.getElementById('btn-print-selected');
    const downloadBtn        = document.getElementById('btn-download-selected');
    const deleteBtn          = document.getElementById('btn-delete-selected');

    const selectionPill      = document.getElementById('selection-pill');
    const selectedCountText  = document.getElementById('selected-count-text');
    const selectedTotalContext = document.getElementById('selected-total-context');

    const selectionBanner    = document.getElementById('selection-banner-alert');
    const bannerText         = document.getElementById('banner-text');
    const btnSelectAllPages  = document.getElementById('btn-select-all-pages');
    const btnClearSelection  = document.getElementById('btn-clear-selection');

    const badgePrint         = document.getElementById('badge-print-count');
    const badgeDownload      = document.getElementById('badge-download-count');
    const badgeDelete        = document.getElementById('badge-delete-count');
    const filterForm         = document.getElementById('filterForm');

    const floatingBar        = document.getElementById('floating-bulk-bar');
    const floatingCountBadge = document.getElementById('floating-count-badge');
    const floatingBtnClear   = document.getElementById('floating-btn-clear');

    // When filter form is submitted, clear stored selection so new search starts fresh
    if (filterForm) {
        filterForm.addEventListener('submit', function () {
            sessionStorage.removeItem(storageKey);
        });
    }

    function getSelectedSet() {
        try {
            const raw = sessionStorage.getItem(storageKey);
            return raw ? new Set(JSON.parse(raw)) : new Set();
        } catch (e) {
            return new Set();
        }
    }

    function saveSelectedSet(set) {
        try {
            sessionStorage.setItem(storageKey, JSON.stringify(Array.from(set)));
        } catch (e) {}
    }

    function updateUI() {
        const selectedSet = getSelectedSet();
        const count = selectedSet.size;
        const totalCount = ALL_INVOICE_IDS.length;
        const currentPageCount = PAGE_INVOICE_IDS.length;

        // 1. Sync row checkboxes and row background highlight on current page
        checkboxes.forEach(cb => {
            const isChecked = selectedSet.has(Number(cb.value));
            cb.checked = isChecked;
            const tr = document.getElementById('row-' + cb.value);
            if (tr) {
                if (isChecked) {
                    tr.classList.add('table-row-selected');
                } else {
                    tr.classList.remove('table-row-selected');
                }
            }
        });

        // 2. Sync selectAll checkbox for current page
        const pageAllChecked = currentPageCount > 0 && PAGE_INVOICE_IDS.every(id => selectedSet.has(Number(id)));
        selectAll.checked = pageAllChecked;

        // 3. Update buttons, badges and bar states
        const hasSelection = count > 0;

        printBtn.disabled    = !hasSelection;
        downloadBtn.disabled = !hasSelection;
        deleteBtn.disabled   = !hasSelection;

        if (hasSelection) {
            bulkBar.classList.add('has-selection');

            selectionPill.style.display = 'inline-flex';
            selectedCountText.textContent = count + (count === 1 ? ' item selected' : ' items selected');
            selectedTotalContext.textContent = 'of ' + totalCount;

            btnClearSelection.style.display = 'inline-block';

            badgePrint.textContent = count;
            badgePrint.style.display = 'inline-block';
            badgeDownload.textContent = count;
            badgeDownload.style.display = 'inline-block';
            badgeDelete.textContent = count;
            badgeDelete.style.display = 'inline-block';

            floatingCountBadge.textContent = count + ' selected';

            // Show Select All Pages alert banner if all items on page are checked but total > page
            if (pageAllChecked && count < totalCount) {
                selectionBanner.style.display = 'flex';
                bannerText.innerHTML = 'All <strong>' + currentPageCount + '</strong> items on this page are selected.';
                btnSelectAllPages.style.display = 'inline-block';
                btnSelectAllPages.textContent = 'Select all ' + totalCount + ' items in this report';
            } else if (count === totalCount && totalCount > currentPageCount) {
                selectionBanner.style.display = 'flex';
                bannerText.innerHTML = 'All <strong>' + totalCount + '</strong> items across all pages are selected.';
                btnSelectAllPages.style.display = 'none';
            } else {
                selectionBanner.style.display = 'none';
            }
        } else {
            bulkBar.classList.remove('has-selection');
            selectionPill.style.display = 'none';
            btnClearSelection.style.display = 'none';
            badgePrint.style.display = 'none';
            badgeDownload.style.display = 'none';
            badgeDelete.style.display = 'none';
            selectionBanner.style.display = 'none';
        }

        handleScroll();
    }

    function handleScroll() {
        if (!floatingBar) return;
        const selectedSet = getSelectedSet();
        if (selectedSet.size > 0 && window.scrollY > 220) {
            floatingBar.classList.add('show');
        } else {
            floatingBar.classList.remove('show');
        }
    }

    window.addEventListener('scroll', handleScroll);

    // Row checkbox change
    checkboxes.forEach(cb => {
        cb.addEventListener('change', function () {
            const selectedSet = getSelectedSet();
            const id = Number(cb.value);
            if (cb.checked) {
                selectedSet.add(id);
            } else {
                selectedSet.delete(id);
            }
            saveSelectedSet(selectedSet);
            updateUI();
        });
    });

    // Header Select All change
    selectAll.addEventListener('change', function () {
        const selectedSet = getSelectedSet();
        if (selectAll.checked) {
            PAGE_INVOICE_IDS.forEach(id => selectedSet.add(Number(id)));
        } else {
            if (selectedSet.size === ALL_INVOICE_IDS.length) {
                selectedSet.clear();
            } else {
                PAGE_INVOICE_IDS.forEach(id => selectedSet.delete(Number(id)));
            }
        }
        saveSelectedSet(selectedSet);
        updateUI();
    });

    // Select all across all pages button
    if (btnSelectAllPages) {
        btnSelectAllPages.addEventListener('click', function () {
            const selectedSet = new Set(ALL_INVOICE_IDS.map(Number));
            saveSelectedSet(selectedSet);
            updateUI();
        });
    }

    // Clear selection button
    function clearAllSelection() {
        const selectedSet = getSelectedSet();
        selectedSet.clear();
        saveSelectedSet(selectedSet);
        updateUI();
    }

    if (btnClearSelection) {
        btnClearSelection.addEventListener('click', clearAllSelection);
    }
    if (floatingBtnClear) {
        floatingBtnClear.addEventListener('click', clearAllSelection);
    }

    /**
     * Collect checked IDs, inject hidden inputs into the right form, then submit.
     * @param {'print'|'download'|'delete'} action
     */
    window.submitBulkAction = function (action) {
        const selectedSet = getSelectedSet();
        const checkedIds = Array.from(selectedSet);

        if (!checkedIds.length) return;

        if (action === 'delete') {
            if (!confirm('Are you sure you want to delete ' + checkedIds.length + ' selected report(s)? This action cannot be undone.')) {
                return;
            }
        }

        const formId     = action === 'print' ? 'form-print-bulk'    : (action === 'download' ? 'form-download-bulk' : 'form-delete-bulk');
        const containerId = action === 'print' ? 'print-checkboxes-hidden' : (action === 'download' ? 'download-checkboxes-hidden' : 'delete-checkboxes-hidden');
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

        if (action === 'delete') {
            sessionStorage.removeItem(storageKey);
        }

        form.submit();
    };

    // Initialize UI on page load
    updateUI();
});
</script>
@endpush
@endsection
