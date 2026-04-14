@extends('layouts.app')

@section('content')
<div class="mb-4">
    @if($customer)
        <a href="{{ route('customers.show', $customer) }}" class="text-decoration-none text-muted"><i class="bi bi-arrow-left"></i> Back to {{ $customer->first_name }}</a>
    @else
        <a href="{{ route('orders.index') }}" class="text-decoration-none text-muted"><i class="bi bi-arrow-left"></i> Back to Orders</a>
    @endif
    <h3 class="page-title mt-2 mb-0">Create New Order</h3>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-4 p-md-5">
        <form action="{{ route('orders.store') }}" method="POST" id="orderForm">
            @csrf
            
            <div class="row g-3 mb-4 border-bottom pb-4">
                @if(!$customer)
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-medium text-muted">Customer <span class="text-danger">*</span></label>
                    <select name="customer_id" class="form-select bg-light border-0" required>
                        <option value="">Select a customer...</option>
                        @foreach(App\Models\Customer::orderBy('first_name')->get() as $c)
                            <option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>{{ $c->full_name }} ({{ $c->customer_number }})</option>
                        @endforeach
                    </select>
                </div>
                @else
                <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-medium text-muted">Customer</label>
                    <input type="text" class="form-control bg-light border-0 fw-medium text-dark" value="{{ $customer->full_name }}" readonly>
                </div>
                @endif

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-medium text-muted">Order Date <span class="text-danger">*</span></label>
                    <input type="date" name="order_date" class="form-control bg-light border-0" value="{{ old('order_date', date('Y-m-d')) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium text-muted">Sales Staff</label>
                    <input type="text" name="sales_staff" class="form-control bg-light border-0" value="{{ old('sales_staff') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium text-muted">Status</label>
                    <select name="order_status" class="form-select bg-light border-0">
                        <option value="Pending" {{ old('order_status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Processing" {{ old('order_status') == 'Processing' ? 'selected' : '' }}>Processing</option>
                        <option value="Completed" {{ old('order_status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3 pt-2">
                <h5 class="mb-0 text-primary fw-semibold">Order Items</h5>
                <button type="button" class="btn btn-sm btn-outline-primary shadow-sm" id="addItemBtn"><i class="bi bi-plus-lg"></i> Add Item</button>
            </div>

            <div class="table-responsive mb-4">
                <table class="table table-bordered align-middle" id="itemsTable">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 30%" class="text-muted fw-bold small text-uppercase tracking-wide border-bottom-0">Product / Service <span class="text-danger">*</span></th>
                            <th style="width: 15%" class="text-muted fw-bold small text-uppercase tracking-wide border-bottom-0">Category</th>
                            <th style="width: 10%" class="text-muted fw-bold small text-uppercase tracking-wide border-bottom-0">Qty <span class="text-danger">*</span></th>
                            <th style="width: 15%" class="text-muted fw-bold small text-uppercase tracking-wide border-bottom-0">Rate ($)<span class="text-danger">*</span></th>
                            <th style="width: 10%" class="text-muted fw-bold small text-uppercase tracking-wide border-bottom-0">Discount ($)</th>
                            <th style="width: 15%" class="text-muted fw-bold small text-uppercase tracking-wide border-bottom-0 text-end">Line Total</th>
                            <th style="width: 5%" class="border-bottom-0"></th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        <tr class="item-row">
                            <td><input type="text" name="items[0][product_name]" class="form-control bg-light border-0 product-name" required placeholder="e.g. Ray-Ban Aviator"></td>
                            <td>
                                <select name="items[0][category]" class="form-select bg-light border-0 category">
                                    <option value="Frames">Frames</option>
                                    <option value="Lenses">Lenses</option>
                                    <option value="Contact Lenses">Contact Lenses</option>
                                    <option value="Accessories">Accessories</option>
                                    <option value="Service">Service</option>
                                </select>
                            </td>
                            <td><input type="number" name="items[0][quantity]" class="form-control bg-light border-0 text-center qty fw-medium" value="1" min="1" required></td>
                            <td><input type="number" step="0.01" name="items[0][unit_price]" class="form-control bg-light border-0 text-end price fw-medium" value="0.00" min="0" required></td>
                            <td><input type="number" step="0.01" name="items[0][discount]" class="form-control bg-light border-0 text-end discount fw-medium text-danger" value="0.00" min="0"></td>
                            <td class="text-end fw-bold line-total p-3 text-dark">0.00</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-light text-danger remove-item rounded-circle" disabled><i class="bi bi-x-lg"></i></button>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="5" class="text-end fw-bold text-muted text-uppercase tracking-wide py-3">Order Total:</td>
                            <td class="text-end fw-bold fs-4 text-primary py-3" id="orderTotal">$0.00</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="text-end pt-3 mt-4 border-top">
                <a href="{{ url()->previous() }}" class="btn btn-light me-2 px-4">Cancel</a>
                <button type="submit" class="btn btn-primary px-4 shadow-sm">Create Order</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let itemIndex = 1;
    const itemsBody = document.getElementById('itemsBody');
    const orderTotalEl = document.getElementById('orderTotal');

    function calculateTotals() {
        let total = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const qty = parseFloat(row.querySelector('.qty').value) || 0;
            const price = parseFloat(row.querySelector('.price').value) || 0;
            const discount = parseFloat(row.querySelector('.discount').value) || 0;
            
            const lineTotal = (qty * price) - discount;
            row.querySelector('.line-total').textContent = lineTotal.toFixed(2);
            total += lineTotal;
        });
        orderTotalEl.textContent = '$' + total.toFixed(2);
        
        const rows = document.querySelectorAll('.item-row');
        rows.forEach(row => {
            row.querySelector('.remove-item').disabled = rows.length === 1;
        });
    }

    document.getElementById('addItemBtn').addEventListener('click', function() {
        const tr = document.createElement('tr');
        tr.className = 'item-row';
        tr.innerHTML = `
            <td><input type="text" name="items[${itemIndex}][product_name]" class="form-control bg-light border-0 product-name" required placeholder="Product name"></td>
            <td>
                <select name="items[${itemIndex}][category]" class="form-select bg-light border-0 category">
                    <option value="Frames">Frames</option>
                    <option value="Lenses">Lenses</option>
                    <option value="Contact Lenses">Contact Lenses</option>
                    <option value="Accessories">Accessories</option>
                    <option value="Service">Service</option>
                </select>
            </td>
            <td><input type="number" name="items[${itemIndex}][quantity]" class="form-control bg-light border-0 text-center qty fw-medium" value="1" min="1" required></td>
            <td><input type="number" step="0.01" name="items[${itemIndex}][unit_price]" class="form-control bg-light border-0 text-end price fw-medium" value="0.00" min="0" required></td>
            <td><input type="number" step="0.01" name="items[${itemIndex}][discount]" class="form-control bg-light border-0 text-end discount fw-medium text-danger" value="0.00" min="0"></td>
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
        if(e.target.classList.contains('qty') || e.target.classList.contains('price') || e.target.classList.contains('discount')) {
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
