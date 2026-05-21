@extends('layouts.app')

@section('content')
<div class="mb-4">
    <a href="{{ route('repairs.show', $repair) }}" class="text-decoration-none text-muted"><i class="bi bi-arrow-left"></i> Back to Repair</a>
    <h3 class="page-title mt-2 mb-0">Edit Repair Job</h3>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-4 p-md-5">
        <form action="{{ route('repairs.update', $repair) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row g-3 mb-3">
                <div class="col-md-12">
                    <label class="form-label text-muted fw-medium">Customer</label>
                    <input type="text" class="form-control bg-light border-0 text-dark fw-medium" value="{{ $repair->customer->full_name ?? 'Unknown' }}" readonly disabled>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-12">
                    <label class="form-label fw-medium text-muted">Reference / Name</label>
                    <input type="text" name="reference" class="form-control bg-light border-0" placeholder="e.g., customer name, frame brand, job reference…" value="{{ old('reference', $repair->reference) }}">
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-medium text-muted">Date In <span class="text-danger">*</span></label>
                    <input type="date" name="repair_date" class="form-control bg-light border-0" value="{{ old('repair_date', \Carbon\Carbon::parse($repair->repair_date)->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium text-muted">Delivery Date</label>
                    <input type="date" name="completion_date" class="form-control bg-light border-0" value="{{ old('completion_date', $repair->completion_date ? \Carbon\Carbon::parse($repair->completion_date)->format('Y-m-d') : '') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium text-muted">Status</label>
                    <select name="status" class="form-select bg-light border-0 fw-medium">
                        <option value="Pending" {{ old('status', $repair->status) == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="In Progress" {{ old('status', $repair->status) == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="Completed" {{ old('status', $repair->status) == 'Completed' ? 'selected' : '' }}>Completed</option>
                        <option value="Collected" {{ old('status', $repair->status) == 'Collected' ? 'selected' : '' }}>Collected</option>
                        <option value="Cancelled" {{ old('status', $repair->status) == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
            </div>

            <div class="row g-3 mb-4 border-top pt-4">
                <div class="col-md-6">
                    <label class="form-label fw-medium text-muted">Item / SKU</label>
                    <input type="text" name="sku" class="form-control bg-light border-0" value="{{ old('sku', $repair->sku) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium text-muted">Assigned Staff</label>
                    <input type="text" name="assigned_staff" class="form-control bg-light border-0" value="{{ old('assigned_staff', $repair->assigned_staff) }}">
                </div>
            </div>

            @php
                $repairItems = $repair->items->where('item_type', 'Repair');
                $lensItems = $repair->items->where('item_type', 'Lens');
            @endphp

            <div class="d-flex justify-content-between align-items-center mb-3 pt-2">
                <h5 class="mb-0 fw-semibold text-primary">Repair Types</h5>
                <button type="button" class="btn btn-sm btn-outline-primary shadow-sm" id="addItemBtn"><i class="bi bi-plus-lg"></i> Add Repair Type</button>
            </div>

            <div class="table-responsive mb-4">
                <table class="table table-bordered align-middle" id="itemsTable">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 70%" class="text-muted fw-bold small text-uppercase tracking-wide border-bottom-0">Repair Type / Name <span class="text-danger">*</span></th>
                            <th style="width: 25%" class="text-muted fw-bold small text-uppercase tracking-wide border-bottom-0 text-end">Estimated Cost ($)<span class="text-danger">*</span></th>
                            <th style="width: 5%" class="border-bottom-0"></th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        @if($repairItems->count())
                            @foreach($repairItems as $index => $item)
                            <tr class="item-row">
                                <td>
                                    <select name="items[{{ $index }}][repair_type]" class="form-select bg-light border-0 repair-type" required>
                                        <option value="">— Select Repair Type —</option>
                                        @foreach(\App\Models\RepairType::where('status','Active')->orderBy('name')->get() as $type)
                                            <option value="{{ $type->name }}" {{ $item->repair_type === $type->name ? 'selected' : '' }}>{{ $type->name }}</option>
                                        @endforeach
                                        @if($item->repair_type && !\App\Models\RepairType::where('name',$item->repair_type)->exists())
                                            <option value="{{ $item->repair_type }}" selected>{{ $item->repair_type }}</option>
                                        @endif
                                    </select>
                                </td>
                                <td><input type="number" step="0.01" name="items[{{ $index }}][price]" class="form-control bg-light border-0 text-end price fw-medium text-success" value="{{ $item->price }}" min="0" required></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-light text-danger remove-item rounded-circle" {{ $repairItems->count() == 1 ? 'disabled' : '' }}><i class="bi bi-x-lg"></i></button>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr class="item-row">
                                <td>
                                    <select name="items[0][repair_type]" class="form-select bg-light border-0 repair-type" required>
                                        <option value="">— Select Repair Type —</option>
                                        @foreach(\App\Models\RepairType::where('status','Active')->orderBy('name')->get() as $type)
                                            <option value="{{ $type->name }}">{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input type="number" step="0.01" name="items[0][price]" class="form-control bg-light border-0 text-end price fw-medium text-success" value="0.00" min="0" required></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-light text-danger remove-item rounded-circle" disabled><i class="bi bi-x-lg"></i></button>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="text-end fw-bold pt-3 pb-3">Repair Subtotal Cost:</td>
                            <td class="text-end fw-bold fs-5 text-dark pt-3 pb-3" id="repairTotal">${{ number_format($repairItems->sum('price'), 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3 pt-4 border-top">
                <h5 class="mb-0 fw-semibold text-primary">Lenses</h5>
                <button type="button" class="btn btn-sm btn-outline-primary shadow-sm" id="addLensBtn"><i class="bi bi-plus-lg"></i> Add Lens</button>
            </div>

            <div class="table-responsive mb-4">
                <table class="table table-bordered align-middle" id="lensesTable">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 70%" class="text-muted fw-bold small text-uppercase tracking-wide border-bottom-0">Lens Type / Name</th>
                            <th style="width: 25%" class="text-muted fw-bold small text-uppercase tracking-wide border-bottom-0 text-end">Cost ($)</th>
                            <th style="width: 5%" class="border-bottom-0"></th>
                        </tr>
                    </thead>
                    <tbody id="lensesBody">
                        @foreach($lensItems as $index => $item)
                        <tr class="lens-row">
                            <td>
                                <select name="lenses[{{ $index }}][lens_type]" class="form-select bg-light border-0" required>
                                    <option value="">— Select Lens —</option>
                                    @foreach($prescriptionTypes as $pt)
                                        <option value="{{ $pt->name }}" {{ $item->repair_type === $pt->name ? 'selected' : '' }}>{{ $pt->name }}</option>
                                    @endforeach
                                    @if($item->repair_type && !$prescriptionTypes->contains('name', $item->repair_type))
                                        <option value="{{ $item->repair_type }}" selected>{{ $item->repair_type }}</option>
                                    @endif
                                </select>
                            </td>
                            <td><input type="number" step="0.01" name="lenses[{{ $index }}][price]" class="form-control bg-light border-0 text-end price fw-medium text-success" value="{{ $item->price }}" min="0" required></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-light text-danger remove-lens rounded-circle"><i class="bi bi-x-lg"></i></button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="text-end fw-bold pt-3 pb-3">Lenses Subtotal Cost:</td>
                            <td class="text-end fw-bold fs-5 text-dark pt-3 pb-3" id="lensesTotal">${{ number_format($lensItems->sum('price'), 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-8">
                    <label class="form-label fw-medium text-muted">Repair Description & Notes</label>
                    <textarea name="repair_notes" rows="3" class="form-control bg-light border-0">{{ old('repair_notes', $repair->repair_notes) }}</textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium text-muted">Delivery Charge ($)</label>
                    <input type="number" step="0.01" name="delivery_charge" id="delivery_charge" class="form-control bg-light border-0 text-end fw-medium" value="{{ old('delivery_charge', number_format($repair->delivery_charge ?? 0, 2, '.', '')) }}" min="0">
                    <div class="text-end mt-3">
                        <span class="text-muted fw-medium">Grand Total: </span>
                        <span class="fw-bold fs-4 text-primary" id="grandTotal">$0.00</span>
                    </div>
                </div>
            </div>
            
            <div class="mb-4 border-top pt-4">
                <label class="form-label fw-medium text-muted">Collection / Outcome Notes</label>
                <textarea name="collection_notes" rows="2" class="form-control bg-light border-0">{{ old('collection_notes', $repair->collection_notes) }}</textarea>
            </div>

            <div class="text-end pt-3 mt-4 border-top">
                <a href="{{ route('repairs.show', $repair) }}" class="btn btn-light me-2 px-4">Cancel</a>
                <button type="submit" class="btn btn-primary px-4 shadow-sm">Update Repair Job</button>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let itemIndex = {{ $repairItems->count() ?: 1 }};
    let lensIndex = {{ $lensItems->count() ?: 0 }};
    const itemsBody = document.getElementById('itemsBody');
    const lensesBody = document.getElementById('lensesBody');

    function calculateTotals() {
        let repairSubtotal = 0;
        document.querySelectorAll('#itemsBody .item-row').forEach(row => {
            const price = parseFloat(row.querySelector('.price').value) || 0;
            repairSubtotal += price;
        });
        document.getElementById('repairTotal').textContent = '$' + repairSubtotal.toFixed(2);

        let lensesSubtotal = 0;
        document.querySelectorAll('#lensesBody .lens-row').forEach(row => {
            const price = parseFloat(row.querySelector('.price').value) || 0;
            lensesSubtotal += price;
        });
        document.getElementById('lensesTotal').textContent = '$' + lensesSubtotal.toFixed(2);
        
        const deliveryCharge = parseFloat(document.getElementById('delivery_charge').value) || 0;
        const grandTotal = repairSubtotal + lensesSubtotal + deliveryCharge;
        document.getElementById('grandTotal').textContent = '$' + grandTotal.toFixed(2);

        const repairRows = document.querySelectorAll('#itemsBody .item-row');
        repairRows.forEach(row => {
            row.querySelector('.remove-item').disabled = repairRows.length === 1;
        });
    }

    const repairTypesData = {!! json_encode(\App\Models\RepairType::where('status','Active')->orderBy('name')->get()->keyBy('name')) !!};
    const repairTypeOptions = Object.keys(repairTypesData);
    const prescriptionTypes = {!! json_encode($prescriptionTypes) !!};

    document.getElementById('addItemBtn').addEventListener('click', function() {
        let opts = '<option value="">— Select Repair Type —</option>';
        repairTypeOptions.forEach(n => { opts += `<option value="${n}">${n}</option>`; });
        const tr = document.createElement('tr');
        tr.className = 'item-row';
        tr.innerHTML = `
            <td>
                <select name="items[${itemIndex}][repair_type]" class="form-select bg-light border-0 repair-type" required>${opts}</select>
            </td>
            <td><input type="number" step="0.01" name="items[${itemIndex}][price]" class="form-control bg-light border-0 text-end price fw-medium text-success" value="0.00" min="0" required></td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-light text-danger remove-item rounded-circle"><i class="bi bi-x-lg"></i></button>
            </td>
        `;
        itemsBody.appendChild(tr);
        itemIndex++;
        calculateTotals();
    });

    document.getElementById('addLensBtn').addEventListener('click', function() {
        let opts = '<option value="">— Select Lens —</option>';
        prescriptionTypes.forEach(pt => { opts += `<option value="${pt.name}">${pt.name}</option>`; });
        const tr = document.createElement('tr');
        tr.className = 'lens-row';
        tr.innerHTML = `
            <td>
                <select name="lenses[${lensIndex}][lens_type]" class="form-select bg-light border-0" required>${opts}</select>
            </td>
            <td><input type="number" step="0.01" name="lenses[${lensIndex}][price]" class="form-control bg-light border-0 text-end price fw-medium text-success" value="0.00" min="0" required></td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-light text-danger remove-lens rounded-circle"><i class="bi bi-x-lg"></i></button>
            </td>
        `;
        lensesBody.appendChild(tr);
        lensIndex++;
        calculateTotals();
    });

    document.addEventListener('input', function(e) {
        if(e.target.classList.contains('price')) {
            calculateTotals();
        }
    });

    itemsBody.addEventListener('change', function(e) {
        if(e.target.classList.contains('repair-type')) {
            const typeName = e.target.value;
            if(typeName && repairTypesData[typeName]) {
                const deliveryCharge = parseFloat(repairTypesData[typeName].delivery_charge) || 0;
                if(deliveryCharge > 0) {
                    document.getElementById('delivery_charge').value = deliveryCharge.toFixed(2);
                    calculateTotals();
                }
            }
        }
    });

    document.getElementById('delivery_charge').addEventListener('input', calculateTotals);

    document.addEventListener('click', function(e) {
        if(e.target.closest('.remove-item')) {
            const btn = e.target.closest('.remove-item');
            if(!btn.disabled) {
                btn.closest('tr').remove();
                calculateTotals();
            }
        }
        if(e.target.closest('.remove-lens')) {
            e.target.closest('tr').remove();
            calculateTotals();
        }
    });

    calculateTotals();
});
</script>
@endpush
@endsection
