<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0; /* Control margins via body padding for better background control */
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #222;
            background: #fff;
            padding: 1.2cm 1.5cm;
            line-height: 1.4;
        }

        /* ── Header Styles ── */
        .company-sub { font-size: 11px; color: #555; line-height: 1.6; margin-top: 5px; }
        .invoice-label {
            font-size: 32px;
            font-weight: bold;
            color: #1a6cdb;
            line-height: 1;
        }
        .inv-number { font-size: 12px; color: #555; margin-top: 5px; }
        .balance-label {
            font-size: 10px;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 15px;
        }
        .balance-amount { font-size: 24px; font-weight: bold; color: #1a2b4a; }

        .divider { border: none; border-top: 1px solid #dde3ed; margin: 20px 0; }

        /* ── Meta Section ── */
        .bill-to-label {
            font-size: 10px;
            text-transform: uppercase;
            color: #888;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .bill-to-detail { font-size: 12px; color: #444; line-height: 1.6; }
        .dt-label { font-size: 11px; color: #666; text-align: right; padding-right: 15px; padding-bottom: 5px; }
        .dt-value { font-size: 11px; color: #222; font-weight: bold; padding-bottom: 5px; }

        /* ── Items Table ── */
        .items-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .items-table thead tr { background: #1a6cdb; }
        .items-table thead th {
            padding: 12px;
            font-size: 11px;
            font-weight: bold;
            color: #ffffff;
            text-align: left;
            text-transform: uppercase;
        }
        .items-table tbody td {
            padding: 12px;
            border-bottom: 1px solid #eef1f6;
            vertical-align: top;
        }
        .item-name { font-weight: bold; color: #1a2b4a; font-size: 12px; }
        .item-sku { font-size: 10px; color: #999; margin-top: 2px; }

        /* ── Totals ── */
        .tot-table { width: 100%; border-collapse: collapse; border-left: 3px solid #1a6cdb; }
        .tot-table td { padding: 8px 12px; font-size: 12px; }
        .tot-table .val { text-align: right; font-weight: bold; }
        .row-total { background: #f0f5ff; border-top: 1px solid #1a6cdb; color: #1a6cdb; }
        .row-bal { background: #1a6cdb; color: #ffffff !important; }
        .row-bal td { font-weight: bold; font-size: 14px; }

        /* ── Footer ── */
        .footer-label { font-size: 10px; text-transform: uppercase; color: #888; font-weight: bold; margin-bottom: 5px; }
        .footer-text { font-size: 11px; color: #444; line-height: 1.6; }
        .payment-details { background: #fafafa; padding: 10px; border-radius: 4px; }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td width="60%" valign="top">
                <img src="{{ 'file://'.str_replace('\\','/',public_path('assets/img/logo/logo.jpg')) }}" 
                     style="width: 180px; margin-bottom: 10px;">
                <div class="company-sub">
                    GST No: 138-002-128<br>
                    6/100 Queens Road<br>
                    Panmure Auckland 1072, New Zealand
                </div>
            </td>
            <td width="40%" valign="top" style="text-align: right;">
                <div class="invoice-label">Tax Invoice</div>
                <div class="inv-number"></div>
                
                @php
                    $balanceDue = $invoice->payment_status === 'Paid' ? 0 : floatval($invoice->total_amount);
                    $isPaid = $invoice->payment_status === 'Paid';
                @endphp
                
                <div class="balance-label">Balance Due</div>
                <div class="balance-amount">NZD {{ number_format($balanceDue, 2) }}</div>
            </td>
        </tr>
    </table>

    <hr class="divider">

    {{-- BILL TO & DATES --}}
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td width="55%" valign="top">
                <div class="bill-to-label">Bill To</div>
                <div class="bill-to-detail">
                    <strong style="font-size: 14px; color: #1a2b4a;">{{ $invoice->customer->full_name ?? 'Walk-in Customer' }}</strong><br>
                    @if($invoice->customer)
                        {!! $invoice->customer->address ? $invoice->customer->address . '<br>' : '' !!}
                        {!! $invoice->customer->phone ? 'Phone: ' . $invoice->customer->phone . '<br>' : '' !!}
                        {{ $invoice->customer->email }}
                    @endif
                </div>
            </td>
            <td width="45%" valign="top">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="dt-label">Invoice Date:</td>
                        <td class="dt-value">{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <td class="dt-label">Terms:</td>
                        <td class="dt-value">Due end of month</td>
                    </tr>
                    <tr>
                        <td class="dt-label">Due Date:</td>
                        <td class="dt-value">{{ \Carbon\Carbon::parse($invoice->invoice_date)->endOfMonth()->format('d M Y') }}</td>
                    </tr>
                    @if($invoice->repair_id)
                    <tr>
                        <td class="dt-label">Ref Repair:</td>
                        <td class="dt-value">#{{ $invoice->repair?->repair_number ?? $invoice->repair_id }}</td>
                    </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    {{-- ITEMS TABLE --}}
    <table class="items-table" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th width="5%">#</th>
                <th width="45%">Item & Description</th>
                <th width="15%" style="text-align: center;">Qty</th>
                <th width="15%" style="text-align: right;">Rate</th>
                <th width="20%" style="text-align: right;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $index => $item)
            <tr>
                <td style="color: #888;">{{ $index + 1 }}</td>
                <td>
                    <div class="item-name">{{ $item->item_name }}</div>
                    @if($item->sku)<div class="item-sku">SKU: {{ $item->sku }}</div>@endif
                </td>
                <td style="text-align: center;">{{ number_format($item->quantity, 2) }}</td>
                <td style="text-align: right;">{{ number_format($item->rate, 2) }}</td>
                <td style="text-align: right; font-weight: bold;">{{ number_format($item->quantity * $item->rate, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- BOTTOM SECTION (Wrapped to avoid orphaned thank you message on new page) --}}
    <div style="page-break-inside: avoid;">
        {{-- TOTALS SECTION --}}
        <table width="100%" cellpadding="0" cellspacing="0" style="margin-top: 15px;">
            <tr>
                <!-- 55% Empty Left Column -->
                <td width="55%" valign="top" style="padding-right: 20px;">
                    @if($invoice->repair && $invoice->repair->repair_notes)
                        <div class="footer-label">Repair Notes</div>
                        <div class="footer-text" style="margin-bottom: 10px;">{{ $invoice->repair->repair_notes }}</div>
                    @endif
                </td>
                
                <!-- 45% Right Column for Totals -->
                <td width="45%">
                    <table class="tot-table" cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <td>Sub Total</td>
                            <td class="val">{{ number_format($invoice->subtotal, 2) }}</td>
                        </tr>
                        @if($invoice->discount_amount > 0)
                        <tr style="color: #e03030;">
                            <td>Discount</td>
                            <td class="val">(-) {{ number_format($invoice->discount_amount, 2) }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td>GST (15%)</td>
                            <td class="val">{{ number_format($invoice->tax_amount, 2) }}</td>
                        </tr>
                        @if($invoice->delivery_charge > 0)
                        <tr>
                            <td>Delivery Charge</td>
                            <td class="val">{{ number_format($invoice->delivery_charge, 2) }}</td>
                        </tr>
                        @endif
                        <tr class="row-total">
                            <td style="font-weight: bold;">Total</td>
                            <td class="val">NZD {{ number_format($invoice->total_amount, 2) }}</td>
                        </tr>
                        <tr class="row-bal">
                            <td>Balance Due</td>
                            <td class="val">NZD {{ number_format($balanceDue, 2) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        {{-- FOOTER --}}
        <table width="100%" cellpadding="0" cellspacing="0" style="margin-top: 40px; border-top: 1px solid #dde3ed; padding-top: 20px;">
            <tr>
                <!-- 55% Empty Left Column -->
                <td width="55%" valign="top" style="padding-right: 20px;">
                    @if($invoice->notes)
                        <div class="footer-label">Notes</div>
                        <div class="footer-text">{{ $invoice->notes }}</div>
                    @endif
                </td>
                
                <!-- 45% Right Column for Payment (Matches Totals Width Perfectly) -->
                <td width="45%" valign="top" style="text-align: left;margin-left:255px; ">
                    <div class="footer-label" style="color: #1a2b4a;">Payment Details</div>
                    <div class="footer-text" style="line-height: 1.6;">
                        PRISM EYEWEAR REPAIRS AND SERVICES LIMITED<br>
                        Bank: <strong>ASB</strong><br>
                        A/C No: <strong>12-3287-0403694-00</strong>
                    </div>
                </td>
            </tr>
        </table>

        <div style="text-align: center; margin-top: 30px;">
            <div style="font-weight: bold; color: #1a2b4a; font-size: 14px;">Thank you for your business!</div>
            <div style="color: #888; font-size: 11px; margin-top: 5px;">Please retain this invoice for your records.</div>
        </div>
    </div>

</body>
</html>