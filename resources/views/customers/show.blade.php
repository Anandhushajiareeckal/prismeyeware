@extends('layouts.app')

@section('content')
<div class="mb-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
    <div>
        <a href="{{ route('customers.index') }}" class="text-decoration-none text-muted fw-medium"><i class="bi bi-arrow-left"></i> All Customers</a>
        <div class="d-flex align-items-center mt-2">
            <h3 class="page-title mb-0 me-3">{{ $customer->full_name }}</h3>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill">{{ $customer->customer_number }}</span>
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('customers.edit', $customer) }}" class="btn btn-light"><i class="bi bi-pencil"></i> Edit Profile</a>
        <a href="{{ route('prescriptions.create', ['customer_id' => $customer->id]) }}" class="btn btn-primary"><i class="bi bi-file-medical"></i> Add Rx</a>
        <a href="{{ route('orders.create', ['customer_id' => $customer->id]) }}" class="btn btn-outline-primary"><i class="bi bi-bag-plus"></i> New Order</a>
    </div>
</div>

<div class="row g-4">
    <!-- Sidebar Identity -->
    <div class="col-md-4 col-lg-3">
        <div class="card text-center mb-4">
            <div class="card-body p-4">
                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 86px; height: 86px; font-size: 2.5rem;">
                    <i class="bi bi-person text-secondary"></i>
                </div>
                <h5 class="fw-bold mb-1">{{ $customer->full_name }}</h5>
                <p class="text-muted small mb-3">
                    <i class="bi bi-telephone-fill me-1"></i> {{ $customer->phone_number ?? 'No phone' }}
                </p>
                
                @if($customer->email)
                <a href="mailto:{{ $customer->email }}" class="btn btn-light btn-sm w-100 mb-2"><i class="bi bi-envelope"></i> Email</a>
                @endif
                
                <div class="text-start mt-4 pt-4 border-top">
                    <div class="mb-3">
                        <span class="text-muted small fw-bold text-uppercase tracking-wide">Gender / Age</span><br>
                        <span class="fw-medium text-dark">{{ $customer->gender ?? '-' }}</span> @if($customer->date_of_birth) &bull; <span class="text-muted">{{ \Carbon\Carbon::parse($customer->date_of_birth)->age }} yrs</span> @endif
                    </div>
                    <div class="mb-3">
                        <span class="text-muted small fw-bold text-uppercase tracking-wide">Joined</span><br>
                        <span class="fw-medium text-dark">{{ $customer->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="mb-3">
                        <span class="text-muted small fw-bold text-uppercase tracking-wide">Address</span><br>
                        <span class="text-dark">
                            {{ $customer->address_line_1 ?: 'No address provided' }}<br>
                            @if($customer->city) {{ $customer->city }}, {{ $customer->state }} @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main Hub content -->
    <div class="col-md-8 col-lg-9">
        <div class="card shadow-sm h-100 pb-3">
            <div class="card-header bg-white pt-3 pb-0 border-0">
                <ul class="nav nav-tabs border-bottom-0" id="customerTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active fw-medium text-secondary" data-bs-toggle="tab" data-bs-target="#prescriptions">
                            Prescriptions <span class="badge bg-light text-dark ms-1">{{ $customer->prescriptions->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-medium text-secondary" data-bs-toggle="tab" data-bs-target="#orders">
                            Orders <span class="badge bg-light text-dark ms-1">{{ $customer->orders->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-medium text-secondary" data-bs-toggle="tab" data-bs-target="#repairs">
                            Repairs <span class="badge bg-light text-dark ms-1">{{ $customer->repairs->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-medium text-secondary" data-bs-toggle="tab" data-bs-target="#invoices">
                            Invoices <span class="badge bg-light text-dark ms-1">{{ $customer->invoices->count() }}</span>
                        </button>
                    </li>
                </ul>
                <hr class="mt-0 text-muted opacity-25">
            </div>
            <div class="card-body p-4 pt-1">
                <style>
                    .nav-link.active { color: #0d6efd !important; border-bottom: 2px solid #0d6efd !important; background: transparent !important; }
                    .nav-link { border: none !important; margin-bottom: -1px; padding: 0.75rem 1rem; }
                </style>
                <div class="tab-content" id="customerTabsContent">
                    
                    <!-- Prescriptions Tab -->
                    <div class="tab-pane fade show active" id="prescriptions">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Eye</th>
                                        <th>SPH</th>
                                        <th>CYL</th>
                                        <th>AXIS</th>
                                        <th>ADD</th>
                                        <th>Rec. Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($customer->prescriptions->sortByDesc('prescription_date') as $rx)
                                    <tr>
                                        <td><a href="{{ route('prescriptions.show', $rx) }}" class="fw-medium text-primary">{{ \Carbon\Carbon::parse($rx->prescription_date)->format('M d, Y') }}</a></td>
                                        <td class="fw-bold">{{ $rx->eye_side }}</td>
                                        <td>{{ $rx->sphere ?: '-' }}</td>
                                        <td>{{ $rx->cylinder ?: '-' }}</td>
                                        <td>{{ $rx->axis ?: '-' }}</td>
                                        <td>{{ $rx->add ?: '-' }}</td>
                                        <td class="text-muted">{{ $rx->recall_date ? \Carbon\Carbon::parse($rx->recall_date)->format('M d, Y') : '-' }}</td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="7" class="text-center py-4 text-muted">No prescriptions on record.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Orders Tab -->
                    <div class="tab-pane fade" id="orders">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Order #</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($customer->orders->sortByDesc('order_date') as $order)
                                    <tr>
                                        <td>
                                            <a href="{{ route('orders.show', $order) }}" class="fw-medium text-dark">{{ $order->order_number }}</a>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($order->order_date)->format('M d, Y') }}</td>
                                        <td class="fw-medium">${{ number_format($order->total_amount, 2) }}</td>
                                        <td><span class="badge bg-success-subtle text-success">{{ $order->order_status }}</span></td>
                                        <td class="text-end">
                                            <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-light"><i class="bi bi-eye"></i></a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="5" class="text-center py-4 text-muted">No orders found.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Repairs Tab -->
                    <div class="tab-pane fade" id="repairs">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Repair #</th>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($customer->repairs->sortByDesc('repair_date') as $repair)
                                    <tr>
                                        <td>
                                            <a href="{{ route('repairs.show', $repair) }}" class="fw-medium text-dark">{{ $repair->repair_number }}</a>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($repair->repair_date)->format('M d, Y') }}</td>
                                        <td>{{ $repair->repair_type ?: '-' }}</td>
                                        <td><span class="badge bg-warning-subtle text-warning">{{ $repair->status }}</span></td>
                                        <td class="text-end">
                                            <a href="{{ route('repairs.show', $repair) }}" class="btn btn-sm btn-light"><i class="bi bi-eye"></i></a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="5" class="text-center py-4 text-muted">No repairs found.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Invoices Tab -->
                    <div class="tab-pane fade" id="invoices">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Invoice #</th>
                                        <th>Date</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($customer->invoices->sortByDesc('invoice_date') as $invoice)
                                    <tr>
                                        <td>
                                            <a href="{{ route('invoices.show', $invoice) }}" class="fw-medium text-dark">{{ $invoice->invoice_number }}</a>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('M d, Y') }}</td>
                                        <td class="fw-medium">${{ number_format($invoice->total_amount, 2) }}</td>
                                        <td><span class="badge bg-info-subtle text-info">{{ $invoice->payment_status }}</span></td>
                                        <td class="text-end">
                                            <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-sm btn-light"><i class="bi bi-receipt"></i></a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="5" class="text-center py-4 text-muted">No invoices found.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
