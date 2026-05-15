<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Consolidated Invoice</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0;
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
            font-size: 30px;
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
        .item-sku { font-size: 10px; color: #999; margin-top: 2px; line-height: 1.4; }

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

    @php
        $customer = $invoices->first()->customer ?? null;
        $consolidated_subtotal = 0;
        $consolidated_tax = 0;
        $consolidated_discount = 0;
        $consolidated_delivery = 0;
        $consolidated_total = 0;
        $consolidated_balance = 0;
        
        foreach($invoices as $inv) {
            $consolidated_subtotal += $inv->subtotal;
            $consolidated_tax += $inv->tax_amount;
            $consolidated_discount += $inv->discount_amount;
            $consolidated_delivery += $inv->delivery_charge ?? 0;
            $consolidated_total += $inv->total_amount;
            
            $bal = $inv->payment_status === 'Paid' ? 0 : floatval($inv->total_amount);
            $consolidated_balance += $bal;
        }
        
        $isPaid = $consolidated_balance <= 0;
        $statement_date = \Carbon\Carbon::now()->format('d M Y');
    @endphp

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
                <div class="invoice-label">Consolidated Invoice</div>
                <div class="inv-number">Includes: {{ count($invoices) }} Invoice(s)</div>
                
                <div class="balance-label">Total Balance Due</div>
                <div class="balance-amount">NZD {{ number_format($consolidated_balance, 2) }}</div>
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
                    @if($customer)
                        <strong style="font-size: 14px; color: #1a2b4a;">{{ $customer->full_name }}</strong><br>
                        {!! $customer->address ? $customer->address . '<br>' : '' !!}
                        {!! $customer->phone ? 'Phone: ' . $customer->phone . '<br>' : '' !!}
                        {{ $customer->email }}
                    @else
                        <strong style="font-size: 14px; color: #1a2b4a;">Walk-in Customer</strong>
                    @endif
                </div>
            </td>
            <td width="45%" valign="top">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="dt-label">Statement Date:</td>
                        <td class="dt-value">{{ $statement_date }}</td>
                    </tr>
                    <tr>
                        <td class="dt-label">Terms:</td>
                        <td class="dt-value">Due end of month</td>
                    </tr>
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
            @php $itemIndex = 1; @endphp
            @foreach($invoices as $invoice)
                @foreach($invoice->items as $item)
                <tr>
                    <td style="color: #888;">{{ $itemIndex++ }}</td>
                    <td>
                        <div class="item-name">{{ $item->item_name }}</div>
                        <div class="item-sku">
                            Inv: {{ $invoice->invoice_number }} 
                            @if($item->sku) | SKU: {{ $item->sku }} @endif 
                            @if($invoice->repair_id) | Ref: #{{ $invoice->repair?->repair_number ?? $invoice->repair_id }} 
                            @elseif($invoice->order_id) | Ref: {{ $invoice->order?->order_number ?? '#'.$invoice->order_id }} 
                            @endif
                        </div>
                    </td>
                    <td style="text-align: center;">{{ number_format($item->quantity, 2) }}</td>
                    <td style="text-align: right;">{{ number_format($item->rate, 2) }}</td>
                    <td style="text-align: right; font-weight: bold;">{{ number_format($item->quantity * $item->rate, 2) }}</td>
                </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

    {{-- BOTTOM SECTION (Wrapped to avoid orphaned thank you message on new page) --}}
    <div style="page-break-inside: avoid;">
        {{-- TOTALS SECTION --}}
        <table width="100%" cellpadding="0" cellspacing="0" style="margin-top: 15px;">
            <tr>
                <!-- 55% Empty Left Column -->
                <td width="55%"></td>
                
                <!-- 45% Right Column for Totals -->
                <td width="45%">
                    <table class="tot-table" cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <td>Sub Total</td>
                            <td class="val">{{ number_format($consolidated_subtotal, 2) }}</td>
                        </tr>
                        @if($consolidated_discount > 0)
                        <tr style="color: #e03030;">
                            <td>Discount</td>
                            <td class="val">(-) {{ number_format($consolidated_discount, 2) }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td>GST (15%)</td>
                            <td class="val">{{ number_format($consolidated_tax, 2) }}</td>
                        </tr>
                        @if($consolidated_delivery > 0)
                        <tr>
                            <td>Delivery Charge</td>
                            <td class="val">{{ number_format($consolidated_delivery, 2) }}</td>
                        </tr>
                        @endif
                        <tr class="row-total">
                            <td style="font-weight: bold;">Total</td>
                            <td class="val">NZD {{ number_format($consolidated_total, 2) }}</td>
                        </tr>
                        @if($consolidated_total - $consolidated_balance > 0)
                        <tr style="color: #e03030;">
                            <td>Payment Made</td>
                            <td class="val">(-) {{ number_format($consolidated_total - $consolidated_balance, 2) }}</td>
                        </tr>
                        @endif
                        <tr class="row-bal">
                            <td>Balance Due</td>
                            <td class="val">NZD {{ number_format($consolidated_balance, 2) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        {{-- FOOTER --}}
        <table width="100%" cellpadding="0" cellspacing="0" style="margin-top: 40px; border-top: 1px solid #dde3ed; padding-top: 20px;">
            <tr>
                <!-- 55% Left Column for Notes -->
                <td width="55%" valign="top" style="padding-right: 20px;">
                    <div class="footer-label">Notes</div>
                    <div class="footer-text">Consolidated statement containing multiple invoices.</div>
                </td>
                
                <!-- 45% Right Column for Payment (Matches Totals Width Perfectly) -->
                <td width="45%" valign="top" style="text-align: left;margin-left:255px;">
                    <div class="footer-label" style="color: #1a2b4a;">Payment Details</div>
                    <div class="footer-text" style="line-height: 1.6;">
                        Prism Eyewear Repairs And Services<br>
                        Bank: <strong>ASB</strong><br>
                        A/C No: <strong>12-3297-0403694-00</strong>
                    </div>
                </td>
            </tr>
        </table>

        <div style="text-align: center; margin-top: 30px;">
            <div style="font-weight: bold; color: #1a2b4a; font-size: 14px;">Thank you for your business!</div>
            <div style="color: #888; font-size: 11px; margin-top: 5px;">Please retain this statement for your records.</div>
        </div>
    </div>

</body>
</html>
