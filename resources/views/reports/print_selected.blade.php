<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consolidated Invoice</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: #e8ecf0;
            color: #222;
            font-size: 13px;
        }

        .btn-print-bar {
            text-align: center;
            padding: 20px;
        }
        .btn-print-bar button, .btn-print-bar a {
            padding: 10px 28px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 0 6px;
        }
        .btn-print-bar button.btn-primary { background: #1a6cdb; color: #fff; }
        .btn-print-bar button.btn-secondary { background: #4a5b7d; color: #fff; }
        .btn-print-bar a { background: #fff; color: #333; border: 1px solid #ccc; }

        .page {
            max-width: 800px;
            margin: 0 auto 40px;
            background: #fff;
            position: relative;
            overflow: hidden;
            border-radius: 4px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.12);
            padding: 48px 50px 50px;
        }

        /* Watermark concentric circles */
        .watermark {
            position: absolute;
            top: -120px;
            right: -120px;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            border: 60px solid rgba(26, 108, 219, 0.04);
            box-shadow:
                0 0 0 80px rgba(26, 108, 219, 0.035),
                0 0 0 160px rgba(26, 108, 219, 0.025),
                0 0 0 240px rgba(26, 108, 219, 0.015);
            pointer-events: none;
            z-index: 0;
        }
        .watermark-bottom {
            position: absolute;
            bottom: -100px;
            left: -100px;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            border: 50px solid rgba(26, 108, 219, 0.04);
            box-shadow:
                0 0 0 70px rgba(26, 108, 219, 0.03),
                0 0 0 140px rgba(26, 108, 219, 0.02);
            pointer-events: none;
            z-index: 0;
        }

        .content { position: relative; z-index: 1; }

        /* HEADER */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
        }
        .logo-area { display: flex; flex-direction: column; gap: 6px; }
        .company-sub { font-size: 12px; color: #555; margin-top: 2px; line-height: 1.6; }

        .invoice-label-area { text-align: right; }
        .invoice-label {
            font-size: 30px;
            font-weight: 700;
            color: #1a6cdb;
            letter-spacing: -0.5px;
            line-height: 1;
            margin-bottom: 6px;
        }
        .inv-number {
            font-size: 13px;
            color: #555;
            margin-bottom: 16px;
        }
        .balance-label {
            font-size: 11px;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .balance-amount {
            font-size: 22px;
            font-weight: 700;
            color: #1a2b4a;
        }
        .balance-amount.paid { color: #1a6cdb; }

        /* DIVIDER */
        .divider { border: none; border-top: 1px solid #dde3ed; margin: 22px 0; }

        /* BILL TO + DATES ROW */
        .meta-row {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 28px;
        }
        .bill-to-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #888;
            margin-bottom: 6px;
            font-weight: 600;
        }
        .bill-to-name { font-size: 14px; font-weight: 700; color: #1a2b4a; margin-bottom: 3px; }
        .bill-to-detail { font-size: 12px; color: #555; line-height: 1.7; }

        .dates-table { margin-left: auto; }
        .dates-table td {
            font-size: 12.5px;
            padding: 3px 0 3px 24px;
            vertical-align: top;
            white-space: nowrap;
        }
        .dates-table td:first-child { color: #666; font-weight: 500; text-align: right; }
        .dates-table td:last-child { color: #222; font-weight: 600; }

        /* ITEMS TABLE */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }
        .items-table thead tr {
            background: #1a6cdb;
            color: #fff;
        }
        .items-table thead th {
            padding: 10px 14px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.3px;
        }
        .items-table tbody tr { border-bottom: 1px solid #eef1f6; }
        .items-table tbody tr:last-child { border-bottom: 2px solid #cdd5e0; }
        .items-table tbody td {
            padding: 11px 14px;
            color: #333;
            font-size: 12.5px;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .item-num { color: #888; font-size: 11px; }

        /* TOTALS */
        .totals-section {
            display: flex;
            justify-content: flex-end;
            margin-top: 0;
        }
        .totals-box {
            width: 260px;
            border-left: 3px solid #1a6cdb;
            padding-left: 0;
        }
        .totals-box table { width: 100%; border-collapse: collapse; }
        .totals-box table td {
            padding: 7px 14px;
            font-size: 12.5px;
        }
        .totals-box table td:first-child { color: #555; }
        .totals-box table td:last-child { text-align: right; font-weight: 600; color: #222; }
        .totals-box .row-discount td:last-child { color: #e03030; }
        .totals-box .row-total {
            background: #f0f5ff;
            border-top: 2px solid #1a6cdb;
        }
        .totals-box .row-total td {
            padding: 10px 14px;
            font-size: 14px;
            font-weight: 700;
        }
        .totals-box .row-total td:first-child { color: #1a2b4a; }
        .totals-box .row-total td:last-child { color: #1a6cdb; }
        .totals-box .row-balance {
            background: #1a6cdb;
        }
        .totals-box .row-balance td {
            padding: 11px 14px;
            font-size: 14px;
            font-weight: 700;
            color: #fff !important;
        }

        /* BOTTOM SECTION */
        .bottom-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-top: 30px;
            padding-top: 24px;
            border-top: 1px solid #dde3ed;
            gap: 24px;
        }
        .notes-area { flex: 1; }
        .notes-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #888;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .notes-text { font-size: 12px; color: #444; line-height: 1.7; white-space: pre-wrap; }
        .thank-you {
            text-align: right;
            font-size: 12px;
            color: #888;
            align-self: flex-end;
        }
        .thank-you strong { display: block; font-size: 13px; color: #1a2b4a; margin-bottom: 2px; }

        @media print {
            body { background: #fff; margin: 0; padding: 0; }
            .btn-print-bar { display: none !important; }
            .page {
                box-shadow: none;
                border-radius: 0;
                max-width: 100%;
                margin: 0;
                padding: 30px 40px;
                page-break-after: avoid;
            }
            @page { margin: 0.8cm; size: A4; }
        }
    </style>
</head>
<body>
    <div class="btn-print-bar">
        <button class="btn-primary" onclick="window.print()">🖨 Print / Save as PDF</button>
        <button class="btn-secondary" onclick="window.close()">Close Tab</button>
    </div>

    @php
        $customer = $invoices->first()->customer;
        $consolidated_subtotal = 0;
        $consolidated_tax = 0;
        $consolidated_discount = 0;
        $consolidated_total = 0;
        $consolidated_balance = 0;
        
        foreach($invoices as $inv) {
            $consolidated_subtotal += $inv->subtotal;
            $consolidated_tax += $inv->tax_amount;
            $consolidated_discount += $inv->discount_amount;
            $consolidated_total += $inv->total_amount;
            
            $bal = $inv->payment_status === 'Paid' ? 0 : floatval($inv->total_amount);
            $consolidated_balance += $bal;
        }
        
        $isPaid = $consolidated_balance <= 0;
        $statement_date = \Carbon\Carbon::now()->format('d M Y');
        $invoice_numbers = $invoices->pluck('invoice_number')->implode(', ');
    @endphp

    <div class="page">
        <div class="watermark"></div>
        <div class="watermark-bottom"></div>

        <div class="content">
            <!-- HEADER -->
            <div class="header">
                <div class="logo-area">
                    <img src="{{ asset('assets/img/logo/logo.jpg') }}" alt="Prism Eyewear" style="max-width: 160px; max-height: 80px; object-fit: contain; margin-bottom: 8px;">
                    <div class="company-sub">
                        GST No: 138-002-128<br>
                        Address: 6/100 Queens Road<br>
                        Panmure Auckland 1072<br>
                        New Zealand
                    </div>
                </div>

                <div class="invoice-label-area">
                    <div class="invoice-label">Consolidated Invoice</div>
                    <div class="inv-number">Includes: {{ count($invoices) }} Invoice(s)</div>
                    <div class="balance-label">Total Balance Due</div>
                    <div class="balance-amount {{ $isPaid ? 'paid' : '' }}">
                        NZD{{ number_format($consolidated_balance, 2) }}
                    </div>
                </div>
            </div>

            <hr class="divider">

            <!-- BILL TO + DATES -->
            <div class="meta-row">
                <div>
                    <div class="bill-to-label">Bill To</div>
                    @if($customer)
                        <div class="bill-to-detail">
                            {{ $customer->full_name }}<br>
                            @if($customer->address){{ $customer->address }}<br>@endif
                            @if($customer->phone)Phone: {{ $customer->phone }}<br>@endif
                            @if($customer->email){{ $customer->email }}@endif
                        </div>
                    @else
                        <div class="bill-to-name">Walk-in Customer</div>
                    @endif
                </div>

                <div>
                    <table class="dates-table">
                        <tr>
                            <td>Statement Date :</td>
                            <td>{{ $statement_date }}</td>
                        </tr>
                        <tr>
                            <td>Terms :</td>
                            <td>Due end of the month</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- ITEMS TABLE -->
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width:5%">#</th>
                        <th style="width:45%">Item &amp; Description</th>
                        <th class="text-center" style="width:12%">Qty</th>
                        <th class="text-right" style="width:18%">Rate</th>
                        <th class="text-right" style="width:20%">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @php $itemIndex = 1; @endphp
                    @foreach($invoices as $invoice)
                        @foreach($invoice->items as $item)
                        <tr>
                            <td class="item-num">{{ $itemIndex++ }}</td>
                            <td>
                                <span style="font-weight:600; color:#1a2b4a;">{{ $item->item_name }}</span>
                                <br><small style="color:#999;">Inv: {{ $invoice->invoice_number }} @if($item->sku) | SKU: {{ $item->sku }}@endif</small>
                            </td>
                            <td class="text-center">{{ number_format($item->quantity, 2) }}</td>
                            <td class="text-right">{{ number_format($item->rate, 2) }}</td>
                            <td class="text-right" style="font-weight:600;">{{ number_format($item->quantity * $item->rate, 2) }}</td>
                        </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>

            <!-- TOTALS + NOTES -->
            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-top:6px; gap:20px;">
                <div style="flex:1;"></div>
                <div class="totals-box">
                    <table>
                        <tr>
                            <td>Sub Total</td>
                            <td>{{ number_format($consolidated_subtotal, 2) }}</td>
                        </tr>
                        @if($consolidated_discount > 0)
                        <tr class="row-discount">
                            <td>Discount</td>
                            <td>(-) {{ number_format($consolidated_discount, 2) }}</td>
                        </tr>
                        @endif
                        @if($consolidated_tax > 0)
                        <tr>
                            <td>GST (Incl. 15%)</td>
                            <td>{{ number_format($consolidated_tax, 2) }}</td>
                        </tr>
                        @endif
                        <tr class="row-total">
                            <td>Total</td>
                            <td>NZD{{ number_format($consolidated_total, 2) }}</td>
                        </tr>
                        @if($consolidated_total - $consolidated_balance > 0)
                        <tr class="row-discount">
                            <td>Payment Made</td>
                            <td>(-) {{ number_format($consolidated_total - $consolidated_balance, 2) }}</td>
                        </tr>
                        @endif
                        <tr class="row-balance">
                            <td>Balance Due</td>
                            <td>NZD{{ number_format($consolidated_balance, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- NOTES + FOOTER -->
            <div class="bottom-section">
                <div class="notes-area">
                    <div class="notes-label">Notes</div>
                    <div class="notes-text">Prism Eyewear Repairs And Services
Bank: ASB
A/C No: 12-3297-0403694-00

Thanks for your business.</div>
                </div>
                <div class="thank-you">
                    <strong>Thank you for your business!</strong>
                    Please retain this statement for your records.
                </div>
            </div>
        </div>
    </div>
</body>
</html>
