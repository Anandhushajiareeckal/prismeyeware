@extends('layouts.app')

@section('content')
<div class="mb-4">
    <a href="{{ $repair ? route('repairs.show', $repair) : ($order ? route('orders.show', $order) : route('quotes.index')) }}" class="text-decoration-none text-muted"><i class="bi bi-arrow-left"></i> {{ $repair ? 'Back to Repair #' . $repair->repair_number : ($order ? 'Back to Order ' . $order->order_number : 'Back to Quotes') }}</a>
    <h3 class="page-title mt-2 mb-0">Create Quote</h3>
</div>

@if($repair)
<div class="alert alert-info border-0 shadow-sm d-flex align-items-center gap-3 mb-4" role="alert">
    <i class="bi bi-tools fs-4"></i>
    <div>
        <strong>Generating quote for Repair Job #{{ $repair->repair_number }}</strong><br>
        <span class="text-muted small">Items have been pre-filled from the repair. You can adjust them before saving.</span>
    </div>
</div>
@elseif($order)
<div class="alert alert-info border-0 shadow-sm d-flex align-items-center gap-3 mb-4" role="alert">
    <i class="bi bi-cart fs-4"></i>
    <div>
        <strong>Generating quote for Order {{ $order->order_number }}</strong><br>
        <span class="text-muted small">Items have been pre-filled from the order. You can adjust them before saving.</span>
    </div>
</div>
@endif

<div class="card shadow-sm border-0">
    <div class="card-body p-4 p-md-5">
        <form action="{{ route('quotes.store') }}" method="POST" id="quoteForm">
            @csrf
            
            <div class="row g-3 mb-4 border-bottom pb-4">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-medium text-muted">Customer <span class="text-danger">*</span></label>
                    <select name="customer_id" class="form-select bg-light border-0" required>
                        <option value="">Select a customer...</option>
                        @foreach(App\Models\Customer::orderBy('first_name')->get() as $c)
                            <option value="{{ $c->id }}" {{ request('customer_id') == $c->id || old('customer_id') == $c->id ? 'selected' : '' }}>{{ $c->full_name }} ({{ $c->customer_number }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-medium text-muted">Link Order (Optional)</label>
                    <input type="number" name="order_id" class="form-control bg-light border-0" value="{{ request('order_id') ?? old('order_id') }}" placeholder="Order ID">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-medium text-muted">Link Repair (Optional)</label>
                    <input type="number" name="repair_id" class="form-control bg-light border-0" value="{{ request('repair_id') ?? old('repair_id') }}" placeholder="Repair ID">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-medium text-muted">Quote Date <span class="text-danger">*</span></label>
                    <input type="date" name="quote_date" class="form-control bg-light border-0" value="{{ old('quote_date', date('Y-m-d')) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium text-muted">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select bg-light border-0" required>
                        <option value="Draft" {{ old('status') == 'Draft' ? 'selected' : '' }}>Draft</option>
                        <option value="Sent" {{ old('status') == 'Sent' ? 'selected' : '' }}>Sent</option>
                        <option value="Accepted" {{ old('status') == 'Accepted' ? 'selected' : '' }}>Accepted</option>
                        <option value="Rejected" {{ old('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3 pt-2">
                <h5 class="mb-0 text-primary fw-semibold">Quote Items</h5>
                <button type="button" class="btn btn-sm btn-outline-primary shadow-sm" id="addItemBtn"><i class="bi bi-plus-lg"></i> Add Item</button>
            </div>

            <div class="table-responsive mb-4">
                <table class="table table-bordered align-middle" id="itemsTable">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 35%" class="text-muted fw-bold small text-uppercase tracking-wide border-bottom-0">Item / Description <span class="text-danger">*</span></th>
                            <th style="width: 10%" class="text-muted fw-bold small text-uppercase tracking-wide border-bottom-0 text-center">Qty <span class="text-danger">*</span></th>
                            <th style="width: 15%" class="text-muted fw-bold small text-uppercase tracking-wide border-bottom-0 text-end">Rate ($)<span class="text-danger">*</span></th>
                            <th style="width: 10%" class="text-muted fw-bold small text-uppercase tracking-wide border-bottom-0 text-end">Discount ($)</th>
                            <th style="width: 10%" class="text-muted fw-bold small text-uppercase tracking-wide border-bottom-0 text-end">Tax ($)</th>
                            <th style="width: 15%" class="text-muted fw-bold small text-uppercase tracking-wide border-bottom-0 text-end">Amount</th>
                            <th style="width: 5%" class="border-bottom-0"></th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        @if($repair && $repair->items->count() > 0)
                            @foreach($repair->items as $i => $repairItem)
                            <tr class="item-row">
                                <td>
                                    <input type="text" name="items[{{ $i }}][item_name]" class="form-control bg-light border-0 item-name" required placeholder="Item name" value="{{ $repairItem->repair_type }}">
                                    <input type="text" name="items[{{ $i }}][sku]" class="form-control bg-light border-0 item-sku mt-1 form-control-sm" placeholder="SKU (optional)" value="{{ $repair->sku ?? '' }}">
                                </td>
                                <td><input type="number" name="items[{{ $i }}][quantity]" class="form-control bg-light border-0 text-center qty fw-medium" value="1" min="1" required></td>
                                <td><input type="number" step="0.01" name="items[{{ $i }}][rate]" class="form-control bg-light border-0 text-end rate fw-medium" value="{{ $repairItem->price ?? '0.00' }}" min="0" required></td>
                                <td><input type="number" step="0.01" name="items[{{ $i }}][discount]" class="form-control bg-light border-0 text-end discount fw-medium text-danger" value="0.00" min="0"></td>
                                <td><input type="number" step="0.01" name="items[{{ $i }}][tax]" class="form-control bg-light text-muted border-0 text-end tax fw-medium" value="0.00" min="0" readonly></td>
                                <td class="text-end fw-bold line-total p-3 text-dark">{{ number_format($repairItem->price ?? 0, 2) }}</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-light text-danger remove-item rounded-circle" {{ $repair->items->count() === 1 ? 'disabled' : '' }}><i class="bi bi-x-lg"></i></button>
                                </td>
                            </tr>
                            @endforeach
                        @elseif($order && $order->items->count() > 0)
                            @foreach($order->items as $i => $orderItem)
                            <tr class="item-row">
                                <td>
                                    <input type="text" name="items[{{ $i }}][item_name]" class="form-control bg-light border-0 item-name" required placeholder="Item name" value="{{ $orderItem->product_name }}">
                                    <input type="text" name="items[{{ $i }}][sku]" class="form-control bg-light border-0 item-sku mt-1 form-control-sm" placeholder="SKU (optional)" value="{{ $orderItem->sku ?? '' }}">
                                </td>
                                <td><input type="number" name="items[{{ $i }}][quantity]" class="form-control bg-light border-0 text-center qty fw-medium" value="{{ $orderItem->quantity }}" min="1" required></td>
                                <td><input type="number" step="0.01" name="items[{{ $i }}][rate]" class="form-control bg-light border-0 text-end rate fw-medium" value="{{ $orderItem->unit_price ?? '0.00' }}" min="0" required></td>
                                <td><input type="number" step="0.01" name="items[{{ $i }}][discount]" class="form-control bg-light border-0 text-end discount fw-medium text-danger" value="{{ $orderItem->discount ?? '0.00' }}" min="0"></td>
                                <td><input type="number" step="0.01" name="items[{{ $i }}][tax]" class="form-control bg-light text-muted border-0 text-end tax fw-medium" value="0.00" min="0" readonly></td>
                                <td class="text-end fw-bold line-total p-3 text-dark">{{ number_format(($orderItem->unit_price * $orderItem->quantity) - ($orderItem->discount ?? 0), 2) }}</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-light text-danger remove-item rounded-circle" {{ $order->items->count() === 1 ? 'disabled' : '' }}><i class="bi bi-x-lg"></i></button>
                                </td>
                            </tr>
                            @endforeach
                        @else
                        <tr class="item-row">
                            <td>
                                <input type="text" name="items[0][item_name]" class="form-control bg-light border-0 item-name" required placeholder="Item name">
                                <input type="text" name="items[0][sku]" class="form-control bg-light border-0 item-sku mt-1 form-control-sm" placeholder="SKU (optional)">
                            </td>
                            <td><input type="number" name="items[0][quantity]" class="form-control bg-light border-0 text-center qty fw-medium" value="1" min="1" required></td>
                            <td><input type="number" step="0.01" name="items[0][rate]" class="form-control bg-light border-0 text-end rate fw-medium" value="0.00" min="0" required></td>
                            <td><input type="number" step="0.01" name="items[0][discount]" class="form-control bg-light border-0 text-end discount fw-medium text-danger" value="0.00" min="0"></td>
                            <td><input type="number" step="0.01" name="items[0][tax]" class="form-control bg-light text-muted border-0 text-end tax fw-medium" value="0.00" min="0" readonly></td>
                            <td class="text-end fw-bold line-total p-3 text-dark">0.00</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-light text-danger remove-item rounded-circle" disabled><i class="bi bi-x-lg"></i></button>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-6 pt-3">
                    <label class="form-label fw-medium text-muted">Notes / Terms</label>
                    <textarea name="notes" rows="3" class="form-control bg-light border-0" placeholder="Thank you for your business.">{{ old('notes') }}</textarea>
                </div>
                <div class="col-md-6">
                    <div class="card bg-light border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Subtotal:</span>
                                <span class="fw-medium text-dark" id="quoteSubtotal">$0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Total Discount:</span>
                                <span class="fw-medium text-danger" id="quoteDiscount">-$0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted">Total Tax:</span>
                                <span class="fw-medium text-dark" id="quoteTax">+$0.00</span>
                            </div>
                            <div class="d-flex justify-content-between border-top border-secondary pt-3">
                                <span class="fw-bold fs-5 text-dark">Total Due:</span>
                                <span class="fw-bold fs-4 text-success" id="quoteTotal">$0.00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-end pt-3 border-top">
                <a href="{{ route('quotes.index') }}" class="btn btn-light me-2 px-4">Cancel</a>
                <button type="submit" class="btn btn-success px-4 shadow-sm"><i class="bi bi-check2-circle"></i> Save Quote</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Start itemIndex from existing row count (handles pre-filled repair items)
    let itemIndex = document.querySelectorAll('.item-row').length;
    const itemsBody = document.getElementById('itemsBody');

    function calculateTotals() {
        let subtotal = 0;
        let totalDiscount = 0;
        let totalTax = 0;
        
        document.querySelectorAll('.item-row').forEach(row => {
            const qty = parseFloat(row.querySelector('.qty').value) || 0;
            const rate = parseFloat(row.querySelector('.rate').value) || 0;
            const discount = parseFloat(row.querySelector('.discount').value) || 0;
            
            const lineSubtotal = qty * rate;
            const afterDiscount = lineSubtotal - discount;
            // Inclusive tax: Tax is already inside the price
            const tax = afterDiscount - (afterDiscount / 1.15);
            
            row.querySelector('.tax').value = tax.toFixed(2);
            // Line total = after discount (tax is included)
            const lineTotal = afterDiscount;
            
            row.querySelector('.line-total').textContent = lineTotal.toFixed(2);
            
            subtotal += lineSubtotal;
            totalDiscount += discount;
            totalTax += tax;
        });
        
        // Total = subtotal - discount (tax is inclusive, no addition)
        const finalTotal = subtotal - totalDiscount;
        
        document.getElementById('quoteSubtotal').textContent = '$' + subtotal.toFixed(2);
        document.getElementById('quoteDiscount').textContent = '-$' + totalDiscount.toFixed(2);
        document.getElementById('quoteTax').textContent = '$' + totalTax.toFixed(2) + ' (incl.)';
        document.getElementById('quoteTotal').textContent = '$' + finalTotal.toFixed(2);
        
        const rows = document.querySelectorAll('.item-row');
        rows.forEach(row => {
            row.querySelector('.remove-item').disabled = rows.length === 1;
        });
    }

    document.getElementById('addItemBtn').addEventListener('click', function() {
        const tr = document.createElement('tr');
        tr.className = 'item-row';
        tr.innerHTML = `
            <td>
                <input type="text" name="items[${itemIndex}][item_name]" class="form-control bg-light border-0 item-name" required placeholder="Item name">
                <input type="text" name="items[${itemIndex}][sku]" class="form-control bg-light border-0 item-sku mt-1 form-control-sm" placeholder="SKU (optional)">
            </td>
            <td><input type="number" name="items[${itemIndex}][quantity]" class="form-control bg-light border-0 text-center qty fw-medium" value="1" min="1" required></td>
            <td><input type="number" step="0.01" name="items[${itemIndex}][rate]" class="form-control bg-light border-0 text-end rate fw-medium" value="0.00" min="0" required></td>
            <td><input type="number" step="0.01" name="items[${itemIndex}][discount]" class="form-control bg-light border-0 text-end discount fw-medium text-danger" value="0.00" min="0"></td>
            <td><input type="number" step="0.01" name="items[${itemIndex}][tax]" class="form-control bg-light text-muted border-0 text-end tax fw-medium" value="0.00" min="0" readonly></td>
            <td class="text-end fw-bold line-total p-3 text-dark">0.00</td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-light text-danger remove-item rounded-circle"><i class="bi bi-x-lg"></i></button>
            </td>
        `;
        itemsBody.appendChild(tr);
        itemIndex++;
        calculateTotals();
    });

    itemsBody.addEventListener('input', function(e) {
        if(e.target.classList.contains('qty') || e.target.classList.contains('rate') || e.target.classList.contains('discount') || e.target.classList.contains('tax')) {
            calculateTotals();
        }
    });

    itemsBody.addEventListener('click', function(e) {
        if(e.target.closest('.remove-item')) {
            const btn = e.target.closest('.remove-item');
            if(!btn.disabled) {
                btn.closest('tr').remove();
                calculateTotals();
            }
        }
    });

    calculateTotals();
});
</script>
@endpush
@endsection
