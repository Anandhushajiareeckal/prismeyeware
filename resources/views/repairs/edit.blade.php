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
            
            <div class="row g-3 mb-4">
                <div class="col-md-12 mb-3">
                    <label class="form-label text-muted fw-medium">Customer</label>
                    <input type="text" class="form-control bg-light border-0 text-dark fw-medium" value="{{ $repair->customer->full_name ?? 'Unknown' }}" readonly disabled>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-medium text-muted">Date In <span class="text-danger">*</span></label>
                    <input type="date" name="repair_date" class="form-control bg-light border-0" value="{{ old('repair_date', \Carbon\Carbon::parse($repair->repair_date)->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium text-muted">Target Completion</label>
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
                        @if($repair->items && $repair->items->count())
                            @foreach($repair->items as $index => $item)
                            <tr class="item-row">
                                <td>
                                    <select name="items[{{ $index }}][repair_type]" class="form-select bg-light border-0 repair-type" required>
                                        <option value="">— Select Repair Type —</option>
                                        @foreach(\App\Models\RepairType::where('status','Active')->orderBy('name')->get() as $type)
                                            <option value="{{ $type->name }}" {{ $item->repair_type === $type->name ? 'selected' : '' }}>{{ $type->name }}</option>
                                        @endforeach
                                        {{-- keep old value even if type was deleted/inactive --}}
                                        @if($item->repair_type && !\App\Models\RepairType::where('name',$item->repair_type)->exists())
                                            <option value="{{ $item->repair_type }}" selected>{{ $item->repair_type }}</option>
                                        @endif
                                    </select>
                                </td>
                                <td><input type="number" step="0.01" name="items[{{ $index }}][price]" class="form-control bg-light border-0 text-end price fw-medium text-success" value="{{ $item->price }}" min="0" required></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-light text-danger remove-item rounded-circle" {{ $loop->count == 1 ? 'disabled' : '' }}><i class="bi bi-x-lg"></i></button>
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
                            <td class="text-end fw-bold pt-3 pb-3">Subtotal Estimated Cost:</td>
                            <td class="text-end fw-bold fs-5 text-dark pt-3 pb-3" id="repairTotal">${{ number_format($repair->repair_price, 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="mb-4">
                <label class="form-label fw-medium text-muted">Repair Description & Notes</label>
                <textarea name="repair_notes" rows="3" class="form-control bg-light border-0">{{ old('repair_notes', $repair->repair_notes) }}</textarea>
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
    let itemIndex = {{ $repair->items ? $repair->items->count() : 1 }};
    const itemsBody = document.getElementById('itemsBody');

    function calculateTotals() {
        let subtotal = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const price = parseFloat(row.querySelector('.price').value) || 0;
            subtotal += price;
        });
        document.getElementById('repairTotal').textContent = '$' + subtotal.toFixed(2);
        const rows = document.querySelectorAll('.item-row');
        rows.forEach(row => {
            row.querySelector('.remove-item').disabled = rows.length === 1;
        });
    }

    const repairTypeOptions = {!! json_encode(\App\Models\RepairType::where('status','Active')->orderBy('name')->pluck('name')) !!};

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

    itemsBody.addEventListener('input', function(e) {
        if(e.target.classList.contains('price')) {
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
