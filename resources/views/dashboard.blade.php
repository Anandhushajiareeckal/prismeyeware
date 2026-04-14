@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="page-title mb-0">Dashboard</h3>
    <div>
        <a href="{{ route('customers.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> New Customer</a>
        <a href="{{ route('invoices.create') }}" class="btn btn-outline-primary ms-2"><i class="bi bi-receipt"></i> Create Bill</a>
    </div>
</div>

@php
    $stats = [
        ['title' => 'Total Customers', 'value' => $metrics['customers_count'], 'icon' => 'bi-people', 'color' => 'primary', 'bg' => 'primary-subtle'],
        ['title' => 'Active Repairs', 'value' => $metrics['active_repairs'], 'icon' => 'bi-tools', 'color' => 'warning', 'bg' => 'warning-subtle'],
        ['title' => 'Pending Orders', 'value' => $metrics['pending_orders'], 'icon' => 'bi-bag', 'color' => 'success', 'bg' => 'success-subtle'],
        ['title' => 'Monthly Revenue', 'value' => '$' . number_format($metrics['monthly_revenue'], 2), 'icon' => 'bi-currency-dollar', 'color' => 'info', 'bg' => 'info-subtle'],
    ];
@endphp

<div class="row g-4 mb-4">
    @foreach($stats as $stat)
    <div class="col-md-3 col-sm-6">
        <div class="card stat-card h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-{{ $stat['bg'] }} text-{{ $stat['color'] }} me-3">
                    <i class="bi {{ $stat['icon'] }}"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1 text-uppercase tracking-wide" style="font-size: 0.75rem; font-weight: 600;">{{ $stat['title'] }}</h6>
                    <h3 class="mb-0 fw-bold">{{ $stat['value'] }}</h3>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pt-4 pb-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold text-primary">Recent Orders</h5>
                <a href="{{ route('orders.index') }}" class="btn btn-sm btn-light shadow-none">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-bottom-0 ps-4">Order #</th>
                                <th class="border-bottom-0">Customer</th>
                                <th class="border-bottom-0">Amount</th>
                                <th class="border-bottom-0 text-end pe-4">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                            <tr>
                                <td class="ps-4">
                                    <a href="{{ route('orders.show', $order) }}" class="text-decoration-none fw-medium text-dark">{{ $order->order_number }}</a>
                                </td>
                                <td>{{ $order->customer->full_name ?? 'Unknown' }}</td>
                                <td class="fw-medium">${{ number_format($order->total_amount, 2) }}</td>
                                <td class="text-end pe-4">
                                    <span class="badge bg-light text-dark border">{{ $order->order_status }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center py-4 text-muted">No recent orders</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header border-0 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recent Invoices</h5>
                <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-light">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Invoice #</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(App\Models\Invoice::latest()->take(5)->get() as $invoice)
                            <tr>
                                <td><a href="{{ route('invoices.show', $invoice) }}" class="fw-medium text-primary">{{ $invoice->invoice_number }}</a></td>
                                <td>{{ $invoice->customer->full_name ?? 'Unknown' }}</td>
                                <td class="fw-medium">${{ number_format($invoice->total_amount, 2) }}</td>
                                <td>
                                    @if($invoice->payment_status === 'Paid')
                                        <span class="badge bg-success-subtle text-success">Paid</span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning">{{ $invoice->payment_status }}</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center py-4 text-muted">No invoices yet</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
