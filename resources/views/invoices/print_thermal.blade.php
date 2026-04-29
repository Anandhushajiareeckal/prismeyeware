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
            font-size: 12px;
            font-weight: bold;
            color: #000;
            background: #f0f0f0;
        }

        .receipt-wrap {
            width: 76mm;
            margin: 20px auto;
            background: #fff;
            padding: 8px 6px 20px;
        }

        /* Header */
        .business-name {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            line-height: 1.4;
            margin-bottom: 2px;
        }
        .business-info {
            text-align: center;
            font-size: 10px;
            line-height: 1.6;
            margin-bottom: 6px;
        }

        /* Transaction line */
        .txn-line {
            font-size: 10px;
            margin: 4px 0;
            word-break: break-all;
        }

        /* Dividers */
        .dash {
            border: none;
            border-top: 1px dashed #000;
            margin: 5px 0;
        }
        .dash-solid {
            border: none;
            border-top: 1px solid #000;
            margin: 5px 0;
        }

        /* Customer section */
        .cst-id   { font-size: 10px; margin-bottom: 1px; }
        .cst-name { font-size: 14px; font-weight: bold; letter-spacing: 1px; margin-bottom: 3px; }
        .cst-addr { font-size: 10px; line-height: 1.5; margin-bottom: 2px; }

        /* Job type / description */
        .job-type { font-size: 11px; font-weight: bold; margin: 4px 0 2px; }

        /* Items - using table for reliable Epson rendering, no flexbox */
        .items-table {
            width: 100%;
            border-collapse: collapse;
        }
        .items-table td {
            padding: 1px 0;
            font-size: 11px;
            vertical-align: top;
        }
        .items-table .td-name {
            width: 70%;
            word-break: break-word;
            line-height: 1.4;
            padding-right: 4px;
        }
        .items-table .td-price {
            width: 30%;
            text-align: right;
            white-space: nowrap;
        }

        /* Totals */
        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 2px 0;
            font-size: 11px;
            vertical-align: middle;
        }
        .totals-table .lbl { width: 60%; }
        .totals-table .amt { width: 40%; text-align: right; white-space: nowrap; }

        .row-total-bold .lbl { font-weight: bold; font-size: 12px; }
        .row-total-bold .amt { font-weight: bold; font-size: 12px; }

        .row-account .lbl { font-weight: bold; font-size: 13px; letter-spacing: 0.5px; }
        .row-account .amt { font-weight: bold; font-size: 16px; letter-spacing: 1px; }

        /* Payment mode */
        .pay-mode {
            font-size: 11px;
            margin: 3px 0;
        }

        /* Note block */
        .note-block { font-size: 10px; margin-top: 8px; line-height: 1.5; }
        .note-label { font-weight: bold; }

        /* Footer */
        .footer {
            text-align: center;
            font-size: 10px;
            margin-top: 10px;
            line-height: 1.7;
        }
        .footer .thankyou {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 2px;
        }

        /* Print button bar */
        .btn-bar {
            width: 76mm;
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
        .btn-close  { background: #e0e0e0; color: #000; text-decoration: none;
                      display: inline-flex; align-items: center; justify-content: center; }

        /* ─── Epson Thermal Print Styles ─────────────────────────────────────
           Key fixes:
           1. @page size: 80mm auto  — tells browser this is a roll, not A4
           2. width: 100% on receipt-wrap — fills the 72mm printable area
           3. No margin/padding on html/body — Epson driver adds its own margins
           4. -webkit-print-color-adjust + print-color-adjust for ink savings
        ─────────────────────────────────────────────────────────────────── */
        @media print {
            @page {
                size: 80mm auto;   /* 80mm wide roll, auto height */
                margin: 0mm;       /* Epson driver controls physical margins */
            }
            html, body {
                margin: 0;
                padding: 0;
                background: #fff !important;
                width: 80mm;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .receipt-wrap {
                width: 100%;
                margin: 0;
                padding: 4px 4px 16px;
            }
            .btn-bar { display: none !important; }
        }
    </style>
</head>
<body>

@php
    $invoice->loadMissing(['customer', 'items', 'repair']);

    $subtotal       = floatval($invoice->subtotal ?? 0);
    $taxAmount      = floatval($invoice->tax_amount ?? 0);
    $discountAmount = floatval($invoice->discount_amount ?? 0);
    $totalAmount    = floatval($invoice->total_amount ?? ($subtotal - $discountAmount));

    $customer    = $invoice->customer;
    $staffName   = $invoice->staff_name ?? 'Staff';
    $paymentMode = $invoice->payment_mode ?? null;
    $invoiceDate = \Carbon\Carbon::parse($invoice->created_at ?? $invoice->invoice_date)->format('d-M-Y H:i');
    $jobDesc     = optional($invoice->repair)->job_description;
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
        6/100 Queens Road<br>
        Panmure Auckland-1072<br>
        PH: 09 948 8080 / 02108321242<br>
        GST# 138-002-128
    </div>

    {{-- Transaction Info --}}
    <div class="txn-line">
        #{{ $invoice->invoice_number }} &nbsp; {{ $staffName }} &nbsp; {{ $invoiceDate }}
    </div>

    <hr class="dash">

    {{-- Customer Info --}}
    @if($customer)
    <div class="cst-id">Cst {{ $customer->id }}</div>
    <div class="cst-name">{{ strtoupper($customer->full_name) }}</div>
    <div class="cst-addr">
        @if($customer->address){{ $customer->address }}<br>@endif
        @if($customer->city){{ $customer->city }} @endif
        @if($customer->postal_code){{ $customer->postal_code }}@endif
        @if($customer->phone)<br>Ph: {{ $customer->phone }}@endif
    </div>
    @else
    <div class="cst-name">WALK-IN CUSTOMER</div>
    @endif

    <hr class="dash">

    {{-- Job Description (from repair if linked) --}}
    @if($jobDesc)
    <div class="job-type">{{ $jobDesc }}</div>
    @endif

    {{-- Line Items — table-based for reliable Epson rendering --}}
    <table class="items-table">
        @foreach($invoice->items as $item)
        @php
            $qty       = intval($item->quantity ?? 1);
            $rate      = floatval($item->rate ?? 0);
            $disc      = floatval($item->discount ?? 0);
            $lineTotal = ($qty * $rate) - $disc;
        @endphp
        <tr>
            <td class="td-name">
                {{ $item->item_name }}
                @if($qty > 1) x{{ $qty }}@endif
            </td>
            <td class="td-price">${{ number_format($lineTotal, 2) }}</td>
        </tr>
        @endforeach
    </table>

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
            <td class="lbl">GST Incl.</td>
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

    {{-- Payment Mode --}}
    @if($paymentMode)
    <div class="pay-mode">Paid by: {{ $paymentMode }}</div>
    @endif

    @if($invoice->notes)
    <div class="note-block">
        <span class="note-label">Note: </span>{!! nl2br(e($invoice->notes)) !!}
    </div>
    @endif

    <hr class="dash">

    {{-- Footer --}}
    <div class="footer">
        <div class="thankyou">Thank You!</div>
        <div>Prism Eyewear</div>
        <div>9429051081454</div>
        <div>{{ $invoiceDate }}</div>
    </div>

</div>

<script>
    // Auto-trigger print dialog when page loads (for Epson thermal printers set as default)
    window.addEventListener('load', function () {
        // Small delay ensures CSS is fully applied before print dialog opens
        setTimeout(function () {
            window.print();
        }, 400);
    });
</script>
</body>
</html>
