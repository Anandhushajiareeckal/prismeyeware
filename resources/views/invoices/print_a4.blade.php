<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; color: #333; }
        .invoice-box { max-width: 800px; margin: auto; padding: 40px; background: #fff; box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15); border-radius: 0.5rem; }
        .invoice-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 40px; }
        .company-details { text-align: right; }
        .invoice-title { font-size: 2.5rem; font-weight: 700; color: #0d6efd; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 2px; }
        .table th { background-color: #f8f9fa !important; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; }
        .totals-row td { font-weight: 600; }
        .grand-total { font-size: 1.25rem; color: #0d6efd; font-weight: 700; }
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; background-color: #fff; margin: 0; padding: 0; }
            .invoice-box { box-shadow: none; border: none; max-width: 100%; padding: 0; margin: 0; }
            .btn-print { display: none !important; }
            @page { margin: 1cm; }
        }
    </style>
</head>
<body class="py-5">
    <div class="container mb-4 text-center btn-print">
        <button onclick="window.print()" class="btn btn-primary btn-lg shadow-sm px-4 fw-bold">Print Invoice (A4)</button>
        <a href="{{ url()->previous() }}" class="btn btn-light btn-lg border ms-2 px-4 shadow-sm">Back</a>
    </div>

    <div class="invoice-box border">
        <div class="invoice-header border-bottom pb-4 mb-4">
            <div>
                <h2 class="fw-bold mb-1 text-dark" style="letter-spacing: -0.5px;">PRISM EYEWEAR</h2>
                <p class="text-secondary mb-0">123 Optical Avenue, Vision City</p>
                <p class="text-secondary mb-0">Phone: (555) 123-4567 | hello@prismeyewear.com</p>
            </div>
            <div class="company-details">
                <div class="invoice-title">TAX INVOICE</div>
                <p class="mb-0 fw-bold fs-5 text-dark">#{{ $invoice->invoice_number }}</p>
                <p class="text-secondary mb-0">Date: {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('F d, Y') }}</p>
                <p class="text-secondary mb-0">Status: <strong class="text-{{ $invoice->payment_status === 'Paid' ? 'success' : 'danger' }}">{{ strtoupper($invoice->payment_status) }}</strong></p>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-sm-6">
                <h6 class="text-secondary fw-bold text-uppercase tracking-wide mb-2" style="font-size: 0.85rem;">Bill To:</h6>
                @if($invoice->customer)
                    <h5 class="fw-bold mb-1 text-dark">{{ $invoice->customer->full_name }}</h5>
                    @if($invoice->customer->phone) <p class="mb-0 text-secondary">Phone: {{ $invoice->customer->phone }}</p> @endif
                    @if($invoice->customer->email) <p class="mb-0 text-secondary">Email: {{ $invoice->customer->email }}</p> @endif
                    @if($invoice->customer->address) <p class="mb-0 text-secondary mt-1">{{ $invoice->customer->address }}</p> @endif
                @else
                    <h5 class="fw-bold mb-1 text-secondary">Walk-in Customer</h5>
                @endif
            </div>
            <div class="col-sm-6 text-end">
                @if($invoice->payment_mode)
                    <h6 class="text-secondary fw-bold text-uppercase tracking-wide mb-2" style="font-size: 0.85rem;">Payment Method:</h6>
                    <h5 class="fw-bold mb-1 text-dark">{{ $invoice->payment_mode }}</h5>
                @endif
                @if($invoice->order_id)
                    <p class="mb-0 text-secondary mt-3">Ref Order: <span class="fw-bold text-dark">{{ $invoice->order->order_number ?? '#' . $invoice->order_id }}</span></p>
                @endif
            </div>
        </div>

        <table class="table table-bordered border-secondary mb-5">
            <thead>
                <tr>
                    <th class="w-50 bg-light text-secondary">Item Description</th>
                    <th class="text-center bg-light text-secondary">Qty</th>
                    <th class="text-end bg-light text-secondary">Rate</th>
                    <th class="text-end bg-light text-secondary">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                <tr>
                    <td class="py-3">
                        <span class="fw-bold text-dark">{{ $item->item_name }}</span>
                        @if($item->sku) <br><small class="text-secondary">SKU: {{ $item->sku }}</small> @endif
                    </td>
                    <td class="text-center py-3 fw-medium">{{ $item->quantity }}</td>
                    <td class="text-end py-3 fw-medium">${{ number_format($item->rate, 2) }}</td>
                    <td class="text-end py-3 fw-bold text-dark">${{ number_format($item->quantity * $item->rate, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="row pt-2">
            <div class="col-6">
                @if($invoice->notes)
                <h6 class="text-secondary fw-bold text-uppercase tracking-wide mb-2" style="font-size: 0.85rem;">Notes & Terms:</h6>
                <div class="bg-light p-3 border rounded text-secondary small" style="white-space: pre-wrap;">{{ $invoice->notes }}</div>
                @endif
            </div>
            <div class="col-6">
                <table class="table table-borderless table-sm text-end mb-0">
                    <tr>
                        <td class="text-secondary w-75 py-2">Subtotal:</td>
                        <td class="fw-bold py-2">${{ number_format($invoice->subtotal, 2) }}</td>
                    </tr>
                    @if($invoice->discount_amount > 0)
                    <tr>
                        <td class="text-danger py-2">Discount:</td>
                        <td class="text-danger fw-bold py-2">-${{ number_format($invoice->discount_amount, 2) }}</td>
                    </tr>
                    @endif
                    @if($invoice->tax_amount > 0)
                    <tr>
                        <td class="text-secondary py-2">Tax:</td>
                        <td class="fw-bold py-2">+${{ number_format($invoice->tax_amount, 2) }}</td>
                    </tr>
                    @endif
                    <tr class="border-top border-secondary">
                        <td class="pt-3 pb-0 grand-total text-dark">Total Due:</td>
                        <td class="pt-3 pb-0 grand-total text-primary">${{ number_format($invoice->total_amount, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="mt-5 pt-4 border-top border-secondary text-center text-secondary small">
            <p class="mb-1 fw-bold text-dark">Thank you for choosing Prism Eyewear!</p>
            <p class="mb-0">All eyewear comes with a 1-year warranty on manufacturing defects. Please retain this invoice for your records.</p>
        </div>
    </div>
</body>
</html>
