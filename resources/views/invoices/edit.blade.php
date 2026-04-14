@extends('layouts.app')

@section('content')
<div class="mb-4">
    <a href="{{ route('invoices.show', $invoice) }}" class="text-decoration-none text-muted"><i class="bi bi-arrow-left"></i> Back to Invoice</a>
    <h3 class="page-title mt-2 mb-0">Edit Invoice: {{ $invoice->invoice_number }}</h3>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-4 p-md-5">
        <form action="{{ route('invoices.update', $invoice) }}" method="POST" id="invoiceForm">
            @csrf
            @method('PUT')
            
            <div class="row g-3 mb-4 border-bottom pb-4">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-medium text-muted">Customer</label>
                    <input type="text" class="form-control bg-light border-0 fw-medium text-dark" value="{{ $invoice->customer->full_name ?? 'Unknown' }}" readonly>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-medium text-muted">Link Order (Optional)</label>
                    <input type="number" name="order_id" class="form-control bg-light border-0 text-muted" value="{{ old('order_id', $invoice->order_id) }}" readonly>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-medium text-muted">Link Repair (Optional)</label>
                    <input type="number" name="repair_id" class="form-control bg-light border-0 text-muted" value="{{ old('repair_id', $invoice->repair_id) }}" readonly>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-medium text-muted">Invoice Date <span class="text-danger">*</span></label>
                    <input type="date" name="invoice_date" class="form-control bg-light border-0" value="{{ old('invoice_date', \Carbon\Carbon::parse($invoice->invoice_date)->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium text-muted">Payment Status <span class="text-danger">*</span></label>
                    <select name="payment_status" class="form-select bg-light border-0" required>
                        <option value="Unpaid" {{ old('payment_status', $invoice->payment_status) == 'Unpaid' ? 'selected' : '' }}>Unpaid</option>
                        <option value="Partial" {{ old('payment_status', $invoice->payment_status) == 'Partial' ? 'selected' : '' }}>Partial</option>
                        <option value="Paid" {{ old('payment_status', $invoice->payment_status) == 'Paid' ? 'selected' : '' }}>Paid</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium text-muted">Payment Mode</label>
                    <select name="payment_mode" class="form-select bg-light border-0">
                        <option value="">Select...</option>
                        <option value="Cash" {{ old('payment_mode', $invoice->payment_mode) == 'Cash' ? 'selected' : '' }}>Cash</option>
                        <option value="Card" {{ old('payment_mode', $invoice->payment_mode) == 'Card' ? 'selected' : '' }}>Card</option>
                        <option value="Bank Transfer" {{ old('payment_mode', $invoice->payment_mode) == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="Insurance" {{ old('payment_mode', $invoice->payment_mode) == 'Insurance' ? 'selected' : '' }}>Insurance</option>
                    </select>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3 pt-2">
                <h5 class="mb-0 text-primary fw-semibold">Invoice Items</h5>
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
                        @foreach($invoice->items as $index => $item)
                        <tr class="item-row">
                            <td>
                                <input type="text" name="items[{{ $index }}][item_name]" class="form-control bg-light border-0 item-name" required value="{{ old('items.'.$index.'.item_name', $item->item_name) }}">
                                <input type="text" name="items[{{ $index }}][sku]" class="form-control bg-light border-0 item-sku mt-1 form-control-sm" value="{{ old('items.'.$index.'.sku', $item->sku) }}">
                            </td>
                            <td><input type="number" name="items[{{ $index }}][quantity]" class="form-control bg-light border-0 text-center qty fw-medium" value="{{ old('items.'.$index.'.quantity', $item->quantity) }}" min="1" required></td>
                            <td><input type="number" step="0.01" name="items[{{ $index }}][rate]" class="form-control bg-light border-0 text-end rate fw-medium" value="{{ old('items.'.$index.'.rate', $item->rate) }}" min="0" required></td>
                            <td><input type="number" step="0.01" name="items[{{ $index }}][discount]" class="form-control bg-light border-0 text-end discount fw-medium text-danger" value="{{ old('items.'.$index.'.discount', $item->discount) }}" min="0"></td>
                            <td><input type="number" step="0.01" name="items[{{ $index }}][tax]" class="form-control bg-light border-0 text-end tax fw-medium" value="{{ old('items.'.$index.'.tax', $item->tax) }}" min="0"></td>
                            <td class="text-end fw-bold line-total p-3 text-dark">0.00</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-light text-danger remove-item rounded-circle" {{ $loop->count == 1 ? 'disabled' : '' }}><i class="bi bi-x-lg"></i></button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-6 pt-3">
                    <label class="form-label fw-medium text-muted">Notes / Terms</label>
                    <textarea name="notes" rows="3" class="form-control bg-light border-0">{{ old('notes', $invoice->notes) }}</textarea>
                </div>
                <div class="col-md-6">
                    <div class="card bg-light border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Subtotal:</span>
                                <span class="fw-medium text-dark" id="invoiceSubtotal">$0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Total Discount:</span>
                                <span class="fw-medium text-danger" id="invoiceDiscount">-$0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted">Total Tax:</span>
                                <span class="fw-medium text-dark" id="invoiceTax">+$0.00</span>
                            </div>
                            <div class="d-flex justify-content-between border-top border-secondary pt-3">
                                <span class="fw-bold fs-5 text-dark">Total Due:</span>
                                <span class="fw-bold fs-4 text-success" id="invoiceTotal">$0.00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-end pt-3 border-top">
                <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-light me-2 px-4">Cancel</a>
                <button type="submit" class="btn btn-primary px-4 shadow-sm">Update Invoice</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let itemIndex = {{ $invoice->items->count() }};
    const itemsBody = document.getElementById('itemsBody');

    function calculateTotals() {
        let subtotal = 0;
        let totalDiscount = 0;
        let totalTax = 0;
        
        document.querySelectorAll('.item-row').forEach(row => {
            const qty = parseFloat(row.querySelector('.qty').value) || 0;
            const rate = parseFloat(row.querySelector('.rate').value) || 0;
            const discount = parseFloat(row.querySelector('.discount').value) || 0;
            const tax = parseFloat(row.querySelector('.tax').value) || 0;
            
            const lineSubtotal = qty * rate;
            const lineTotal = lineSubtotal - discount + tax;
            
            row.querySelector('.line-total').textContent = lineTotal.toFixed(2);
            
            subtotal += lineSubtotal;
            totalDiscount += discount;
            totalTax += tax;
        });
        
        const finalTotal = subtotal - totalDiscount + totalTax;
        
        document.getElementById('invoiceSubtotal').textContent = '$' + subtotal.toFixed(2);
        document.getElementById('invoiceDiscount').textContent = '-$' + totalDiscount.toFixed(2);
        document.getElementById('invoiceTax').textContent = '+$' + totalTax.toFixed(2);
        document.getElementById('invoiceTotal').textContent = '$' + finalTotal.toFixed(2);
        
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
            <td><input type="number" step="0.01" name="items[${itemIndex}][tax]" class="form-control bg-light border-0 text-end tax fw-medium" value="0.00" min="0"></td>
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
