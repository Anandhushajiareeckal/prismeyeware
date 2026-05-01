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
                @if($customer->business)
                <div class="mb-2">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 rounded-pill small">Business Customer</span>
                </div>
                @endif
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
                    <li class="nav-item">
                        <button class="nav-link fw-medium text-secondary" data-bs-toggle="tab" data-bs-target="#cust-comms">
                            Comments
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-medium text-secondary" data-bs-toggle="tab" data-bs-target="#cust-notes">
                            Notes <span class="badge bg-light text-dark ms-1">{{ $customer->notes->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-medium text-secondary" data-bs-toggle="tab" data-bs-target="#cust-docs">
                            Docs <span class="badge bg-light text-dark ms-1">{{ $customer->documents->count() }}</span>
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
                                        <th>Type</th>
                                        <th>Optometrist</th>
                                        <th>Exp. Date</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($customer->prescriptions->sortByDesc('prescription_date') as $rx)
                                    <tr>
                                        <td><a href="{{ route('prescriptions.show', $rx) }}" class="fw-medium text-primary">{{ \Carbon\Carbon::parse($rx->prescription_date)->format('M d, Y') }}</a></td>
                                        <td><span class="badge bg-light text-dark border">{{ $rx->type }}</span></td>
                                        <td>{{ $rx->doctor_name ?? '-' }}</td>
                                        <td class="text-muted">{{ $rx->recall_date ? \Carbon\Carbon::parse($rx->recall_date)->format('M d, Y') : '-' }}</td>
                                        <td class="text-end"><a href="{{ route('prescriptions.show', $rx) }}" class="btn btn-sm btn-light"><i class="bi bi-eye"></i> View Rx</a></td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="5" class="text-center py-4 text-muted">No prescriptions on record.</td></tr>
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

                    <!-- Cust Comms Tab -->
                    <div class="tab-pane fade" id="cust-comms">
                        <form action="{{ route('customers.updateComments', $customer) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label class="form-label text-muted fw-medium">Customer Comments</label>
                                <textarea name="cust_comms" class="form-control bg-light border-0" rows="8" placeholder="Enter comments here...">{{ $customer->cust_comms }}</textarea>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary px-4 shadow-sm">Save Comments</button>
                            </div>
                        </form>
                    </div>

                    <!-- Notes Tab -->
                    <div class="tab-pane fade" id="cust-notes">
                        <form action="{{ route('customer-notes.store') }}" method="POST" class="mb-4">
                            @csrf
                            <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                            <div class="mb-3">
                                <label class="form-label text-muted fw-medium">Add New Note</label>
                                <textarea name="note" class="form-control bg-light border-0" rows="3" required></textarea>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary shadow-sm">Add Note</button>
                            </div>
                        </form>
                        <div class="list-group list-group-flush border-top pt-3">
                            @forelse($customer->notes->sortByDesc('created_at') as $note)
                            <div class="list-group-item px-0 border-0 mb-3 bg-light rounded p-3">
                                <p class="mb-1 text-dark" style="white-space: pre-wrap;">{{ $note->note }}</p>
                                <small class="text-muted"><i class="bi bi-person"></i> {{ $note->user->name ?? 'System' }} &bull; {{ $note->created_at->format('M d, Y h:i A') }}</small>
                            </div>
                            @empty
                            <p class="text-muted text-center py-3">No notes yet.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Docs Tab -->
                    <div class="tab-pane fade" id="cust-docs">
                        <form action="{{ route('customer-documents.store') }}" method="POST" enctype="multipart/form-data" class="mb-4">
                            @csrf
                            <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                            <div class="row g-2 mb-3">
                                <div class="col-md-8">
                                    <input type="file" name="document" class="form-control bg-light border-0" required accept=".pdf,.png,.jpg,.jpeg,.doc,.docx">
                                </div>
                                <div class="col-md-4 text-end">
                                    <button type="submit" class="btn btn-primary w-100 shadow-sm">Upload File</button>
                                </div>
                            </div>
                        </form>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>File Name</th>
                                        <th>Uploaded By</th>
                                        <th>Date</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($customer->documents->sortByDesc('created_at') as $doc)
                                    <tr>
                                        <td>
                                            @if($doc->file_path)
                                                <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="fw-medium text-dark"><i class="bi bi-file-earmark-text me-2"></i>{{ $doc->file_name ?? 'Document' }}</a>
                                            @else
                                                <span class="fw-medium text-dark"><i class="bi bi-file-earmark-text me-2"></i>{{ $doc->file_name ?? 'Document' }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $doc->uploader->name ?? '-' }}</td>
                                        <td>{{ $doc->created_at->format('M d, Y') }}</td>
                                        <td class="text-end">
                                            @if($doc->file_path)
                                            <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="btn btn-sm btn-light"><i class="bi bi-download"></i></a>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="4" class="text-center py-4 text-muted">No documents uploaded.</td></tr>
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
