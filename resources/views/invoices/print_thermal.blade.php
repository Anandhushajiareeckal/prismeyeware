<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #{{ $invoice->invoice_number }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 13px;
            font-weight: bold;
            color: #000;
            background: #f0f0f0;
        }

        .receipt-wrap {
            width: 80mm;
            margin: 20px auto;
            background: #fff;
            padding: 10px 8px 24px;
        }

        /* Header */
        .business-name {
            text-align: center;
            font-size: 13px;
            font-weight: bold;
            line-height: 1.4;
            margin-bottom: 4px;
        }
        .business-info {
            text-align: center;
            font-size: 11px;
            line-height: 1.55;
            margin-bottom: 8px;
        }

        /* Transaction line */
        .txn-line {
            font-size: 11px;
            margin: 6px 0;
        }

        /* Dividers */
        .dash {
            border: none;
            border-top: 1px dashed #000;
            margin: 6px 0;
        }
        .dash-solid {
            border: none;
            border-top: 1px solid #000;
            margin: 6px 0;
        }

        /* Customer section */
        .cst-id   { font-size: 11px; margin-bottom: 2px; }
        .cst-name { font-size: 15px; font-weight: bold; letter-spacing: 2px; margin-bottom: 4px; }
        .cst-addr { font-size: 11px; line-height: 1.5; margin-bottom: 2px; }

        /* Note block */
        .note-block { font-size: 11px; margin-top: 10px; line-height: 1.5; text-align: left; }
        .note-label { font-weight: bold; }

        /* Job type / description */
        .job-type { font-size: 13px; font-weight: bold; margin: 5px 0 3px; }

        /* Items */
        .item-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 3px;
            font-size: 12px;
        }
        .item-name  { flex: 1; padding-right: 8px; line-height: 1.4; }
        .item-price { white-space: nowrap; }

        /* Totals */
        .totals-table {
            width: 100%;
        }
        .totals-table td {
            padding: 2px 0;
            font-size: 12px;
            vertical-align: middle;
        }
        .totals-table .lbl { }
        .totals-table .amt { text-align: right; white-space: nowrap; }

        .row-total-bold .lbl { font-weight: bold; font-size: 13px; }
        .row-total-bold .amt { font-weight: bold; font-size: 13px; }

        .row-account .lbl { font-weight: bold; font-size: 15px; letter-spacing: 0.5px; }
        .row-account .amt { font-weight: bold; font-size: 18px; letter-spacing: 1px; }

        /* Footer */
        .footer {
            text-align: center;
            font-size: 11px;
            margin-top: 10px;
            line-height: 1.6;
        }
        .footer .thankyou {
            font-size: 13px;
            margin-bottom: 4px;
        }

        /* Print button bar */
        .btn-bar {
            width: 80mm;
            margin: 16px auto;
            display: flex;
            gap: 10px;
            justify-content: center;
        }
        .btn {
            padding: 10px 20px;
            font-family: sans-serif;
            font-weight: bold;
            cursor: pointer;
            border: none;
            border-radius: 4px;
            font-size: 13px;
        }
        .btn-print { background: #000; color: #fff; }
        .btn-close  { background: #e0e0e0; color: #000; text-decoration: none; display: inline-block; line-height: 1; }

        @media print {
            body { background: #fff; }
            .receipt-wrap { margin: 0; padding: 4px 4px 24px; width: 80mm; }
            .btn-bar { display: none !important; }
            @page { margin: 0; size: 80mm auto; }
        }
    </style>
</head>
<body>

@php
    $invoice->loadMissing(['customer', 'items']);

    $subtotal       = floatval($invoice->subtotal ?? 0);
    $taxAmount      = floatval($invoice->tax_amount ?? 0);
    $discountAmount = floatval($invoice->discount_amount ?? 0);
    $totalAmount    = floatval($invoice->total_amount ?? ($subtotal - $discountAmount));

    $customer = $invoice->customer;
    $staffName = $invoice->staff_name ?? 'Staff';
    $invoiceDate = \Carbon\Carbon::parse($invoice->created_at ?? $invoice->invoice_date)->format('d-M-Y H:i:s');
@endphp

{{-- Print Button --}}
<div class="btn-bar">
    <button class="btn btn-print" onclick="window.print()">&#128438; PRINT</button>
    <a href="{{ url()->previous() }}" class="btn btn-close">&#10006; CLOSE</a>
</div>

<div class="receipt-wrap">

    {{-- Business Header --}}
    <div class="business-name">Prism Eyewear</div>
    <div class="business-info">
        AD: 6A/100 Queens Road<br>
        Panmure Auckland-1072<br>
        PH: 09 948 8080 / 02108321242<br>
        GST# 138-002-128
    </div>

    {{-- Transaction Info Line --}}
    <div class="txn-line">
        #{{ $invoice->invoice_number }} {{ $staffName }} {{ $invoiceDate }}
    </div>

    <hr class="dash">

    {{-- Customer Info --}}
    @if($customer)
    <div class="cst-id">Cst {{ $customer->id }}</div>
    <div class="cst-name">{{ $customer->full_name }}</div>
    <div class="cst-addr">
        @if($customer->address){{ $customer->address }}<br>@endif
        @if($customer->city){{ $customer->city }} @endif
        @if($customer->postal_code){{ $customer->postal_code }}@endif
        @if($customer->phone)<br>Ph:{{ $customer->phone }}@endif
    </div>
    @else
    <div class="cst-name">Walk-in Customer</div>
    @endif

    <hr class="dash">

    {{-- Job Description (from repair if linked) --}}
    @if($invoice->repair && $invoice->repair->job_description)
    <div class="job-type">{{ $invoice->repair->job_description }}</div>
    @endif

    {{-- Line Items --}}
    @foreach($invoice->items as $item)
    @php
        $qty   = intval($item->quantity ?? 1);
        $rate  = floatval($item->rate ?? 0);
        $disc  = floatval($item->discount ?? 0);
        $lineTotal = ($qty * $rate) - $disc;
    @endphp
    <div class="item-row">
        <span class="item-name">{{ $item->item_name }}</span>
        <span class="item-price">${{ number_format($lineTotal, 2) }}</span>
    </div>
    @endforeach

    <hr class="dash">

    {{-- Totals --}}
    <table class="totals-table">
        <tr class="row-total-bold">
            <td class="lbl">TOTAL</td>
            <td class="amt">${{ number_format($totalAmount, 2) }}</td>
        </tr>
    </table>

    <hr class="dash">

    <table class="totals-table">
        @if($taxAmount > 0)
        <tr>
            <td class="lbl">GST Amount</td>
            <td class="amt">${{ number_format($taxAmount, 2) }}</td>
        </tr>
        @endif
        @if($discountAmount > 0)
        <tr>
            <td class="lbl">Discount</td>
            <td class="amt">-${{ number_format($discountAmount, 2) }}</td>
        </tr>
        @endif
    </table>

    <hr class="dash-solid">

    <table class="totals-table">
        <tr class="row-account">
            <td class="lbl">ACCOUNT</td>
            <td class="amt">${{ number_format($totalAmount, 2) }}</td>
        </tr>
    </table>

    @if($invoice->notes)
    <div class="note-block">
        <span class="note-label">Note: </span>{!! nl2br(e($invoice->notes)) !!}
    </div>
    @endif

    {{-- Footer --}}
    <div class="footer">
        <div class="thankyou">Thank You!</div>
        <div>Prism Eyewear Repairs &amp; Services</div>
        <div>9429051081454</div>
    </div>

</div>

<script>
    // For thermal printers that are set as the default printer, automatically trigger print
    // Remove or comment this out if you prefer manual printing
    // window.onload = function() { window.print(); };
</script>
</body>
</html>
