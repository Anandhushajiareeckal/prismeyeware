<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Receipt #{{ $invoice->invoice_number }}</title>
    <style>
        /* ── Screen styles (preview) ─────────────────────────── */
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #1a1a1a;
            background: #e8e8e8;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 24px 16px;
        }

        /* ── Button bar ──────────────────────────────────────── */
        .btn-bar {
            width: 80mm;
            display: flex;
            gap: 8px;
            justify-content: center;
            margin-bottom: 14px;
        }
        .btn {
            padding: 9px 18px;
            font-family: sans-serif;
            font-weight: 700;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            font-size: 13px;
            transition: opacity 0.15s;
        }
        .btn:hover { opacity: 0.85; }
        .btn-print  { background: #212529; color: #fff; }
        .btn-reprint{ background: #0d6efd; color: #fff; }
        .btn-close  {
            background: #dee2e6; color: #212529;
            text-decoration: none;
            display: inline-flex; align-items: center; justify-content: center;
        }

        /* ── Receipt preview (screen only) ──────────────────── */
        .receipt-wrap {
            width: 80mm;
            background: #fff;
            padding: 8px 6px 20px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.12);
        }

        /* ── Shared receipt styles ───────────────────────────── */
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
            line-height: 1.7;
            margin-bottom: 6px;
        }
        .txn-line { font-size: 10px; margin: 4px 0; }
        .dash      { border: none; border-top: 1px dashed #000; margin: 5px 0; }
        .dash-solid{ border: none; border-top: 1px solid #000; margin: 5px 0; }
        .cst-id    { font-size: 10px; margin-bottom: 1px; }
        .cst-name  { font-size: 14px; font-weight: bold; letter-spacing: 1px; margin-bottom: 3px; }
        .cst-addr  { font-size: 10px; line-height: 1.5; margin-bottom: 2px; }
        .job-type  { font-size: 11px; font-weight: bold; margin: 4px 0 2px; }
        .items-table { width: 100%; border-collapse: collapse; }
        .items-table td { padding: 1px 0; font-size: 11px; vertical-align: top; }
        .items-table .td-name  { width: 70%; word-break: break-word; padding-right: 4px; }
        .items-table .td-price { width: 30%; text-align: right; white-space: nowrap; }
        .totals-table { width: 100%; border-collapse: collapse; }
        .totals-table td { padding: 2px 0; font-size: 11px; vertical-align: middle; }
        .totals-table .lbl { width: 60%; }
        .totals-table .amt { width: 40%; text-align: right; white-space: nowrap; }
        .row-total-bold .lbl, .row-total-bold .amt { font-weight: bold; font-size: 12px; }
        .row-account .lbl { font-weight: bold; font-size: 13px; letter-spacing: 0.5px; }
        .row-account .amt { font-weight: bold; font-size: 16px; letter-spacing: 1px; }
        .pay-mode   { font-size: 11px; margin: 3px 0; }
        .note-block { font-size: 10px; margin-top: 8px; line-height: 1.5; }
        .note-label { font-weight: bold; }
        .footer     { text-align: center; font-size: 10px; margin-top: 10px; line-height: 1.7; }
        .footer .thankyou { font-size: 13px; font-weight: bold; margin-bottom: 2px; }

        /* ── Print styles ────────────────────────────────────── */
        @media print {
            /* Hide everything except the receipt */
            .btn-bar, .no-print { display: none !important; }

            /* Remove page margins and background */
            @page {
                size: 80mm auto;   /* 80mm wide, auto height = no page break */
                margin: 0;
            }

            html, body {
                width: 80mm;
                background: #fff !important;
                padding: 0 !important;
                margin: 0 !important;
                display: block !important;
            }

            .receipt-wrap {
                width: 80mm;
                box-shadow: none !important;
                padding: 4px 4px 20px;
            }
        }
    </style>
</head>
<body>

@php
    $invoice->loadMissing(['customer', 'items', 'repair']);

    $subtotal       = floatval($invoice->subtotal       ?? 0);
    $taxAmount      = floatval($invoice->tax_amount     ?? 0);
    $discountAmount = floatval($invoice->discount_amount ?? 0);
    $totalAmount    = floatval($invoice->total_amount   ?? ($subtotal - $discountAmount));

    $customer    = $invoice->customer;
    $staffName   = $invoice->staff_name  ?? 'Staff';
    $paymentMode = $invoice->payment_mode ?? null;
    $invoiceDate = \Carbon\Carbon::parse($invoice->created_at ?? $invoice->invoice_date)->format('d-M-Y H:i');
    $jobDesc     = optional($invoice->repair)->job_description;
@endphp

{{-- Button bar (hidden on print) --}}
<div class="btn-bar no-print">
    <button class="btn btn-print" id="btn-qz-print" onclick="triggerPrint()">🖨 PRINT</button>
    <button class="btn btn-reprint" id="btn-qz-reprint" onclick="triggerPrint()">↺ RE-PRINT</button>
    <a href="{{ url()->previous() }}" class="btn btn-close">✕ CLOSE</a>
</div>

{{-- Receipt (this is what gets printed) --}}
<div class="receipt-wrap">

    <div class="business-name">Prism Eyewear</div>
    <div class="business-info">
        6/100 Queens Road<br>
        Panmure Auckland-1072<br>
        PH: 09 948 8080 / 02108321242<br>
        GST# 138-002-128
    </div>

    <div class="txn-line">
        #{{ $invoice->invoice_number }} &nbsp; {{ $staffName }} &nbsp; {{ $invoiceDate }}
    </div>

    <hr class="dash">

    @if($customer)
    <div class="cst-id">Cst {{ $customer->id }}</div>
    <div class="cst-name">{{ strtoupper($customer->full_name) }}</div>
    <div class="cst-addr">
        @if($customer->address_line_1){{ $customer->address_line_1 }}<br>@endif
        @if($customer->city){{ $customer->city }} @endif
        @if($customer->postal_code){{ $customer->postal_code }}@endif
        @if($customer->phone_number)<br>Ph: {{ $customer->phone_number }}@endif
    </div>
    @else
    <div class="cst-name">WALK-IN CUSTOMER</div>
    @endif

    <hr class="dash">

    @if($jobDesc)
    <div class="job-type">{{ $jobDesc }}</div>
    @endif

    <table class="items-table">
        @foreach($invoice->items as $item)
        @php
            $qty       = intval($item->quantity ?? 1);
            $rate      = floatval($item->rate    ?? 0);
            $disc      = floatval($item->discount ?? 0);
            $lineTotal = ($qty * $rate) - $disc;
        @endphp
        <tr>
            <td class="td-name">{{ $item->item_name }}@if($qty > 1) x{{ $qty }}@endif</td>
            <td class="td-price">${{ number_format($lineTotal, 2) }}</td>
        </tr>
        @endforeach
    </table>

    <hr class="dash">

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

    @if($paymentMode)
    <div class="pay-mode">Paid by: {{ $paymentMode }}</div>
    @endif

    @if($invoice->notes)
    <div class="note-block">
        <span class="note-label">Note: </span>{!! nl2br(e($invoice->notes)) !!}
    </div>
    @endif

    <hr class="dash">

    <div class="footer">
        <div class="thankyou">Thank You!</div>
        <div>Prism Eyewear</div>
        <div>9429051081454</div>
        <div>{{ $invoiceDate }}</div>
    </div>

</div>

{{-- QZ Tray + ESC/POS scripts --}}
<script src="https://cdn.jsdelivr.net/npm/qz-tray@2.2.4/qz-tray.min.js"></script>
<script src="{{ asset('js/escpos-builder.js') }}"></script>
<script src="{{ asset('js/qz-print.js') }}"></script>

<script>
    /* ── Invoice data from PHP ── */
    const INVOICE_DATA = {
        invoiceNumber : @json($invoice->invoice_number),
        invoiceDate   : @json($invoiceDate),
        staffName     : @json($staffName),
        customer      : @json($customer ? [
            'id'         => $customer->id,
            'name'       => $customer->full_name,
            'address'    => $customer->address_line_1 ?? null,
            'city'       => $customer->city        ?? null,
            'postalCode' => $customer->postal_code  ?? null,
            'phone'      => $customer->phone_number ?? null,
        ] : null),
        items         : @json($invoice->items->map(function($item) {
            $qty       = intval($item->quantity ?? 1);
            $rate      = floatval($item->rate    ?? 0);
            $disc      = floatval($item->discount ?? 0);
            $lineTotal = ($qty * $rate) - $disc;
            return [
                'name'      => $item->item_name,
                'qty'       => $qty,
                'rate'      => $rate,
                'discount'  => $disc,
                'lineTotal' => $lineTotal,
            ];
        })),
        subtotal       : @json($subtotal),
        taxAmount      : @json($taxAmount),
        discountAmount : @json($discountAmount),
        totalAmount    : @json($totalAmount),
        paymentMode    : @json($paymentMode),
        notes          : @json($invoice->notes ?? null),
        jobDescription : @json($jobDesc ?? null),
    };

    /* ── Trigger ESC/POS print via QZ Tray ── */
    async function triggerPrint() {
        const btn = document.getElementById('btn-qz-print');
        const btn2 = document.getElementById('btn-qz-reprint');
        if (btn)  { btn.disabled  = true; btn.textContent  = '⏳ Printing…'; }
        if (btn2) { btn2.disabled = true; btn2.textContent = '⏳ Printing…'; }
        try {
            await printReceipt(INVOICE_DATA);
        } finally {
            if (btn)  { btn.disabled  = false; btn.textContent  = '🖨 PRINT'; }
            if (btn2) { btn2.disabled = false; btn2.textContent = '↺ RE-PRINT'; }
        }
    }

    /* ── Auto-print on page load via ESC/POS ── */
    window.addEventListener('load', () => {
        setTimeout(triggerPrint, 600);
    });
</script>

</body>
</html>
