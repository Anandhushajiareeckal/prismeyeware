<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
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
        .btn-print-bar button { background: #1a6cdb; color: #fff; }
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
        .logo-icon {
            font-size: 36px;
            line-height: 1;
            margin-bottom: 2px;
        }
        /* SVG glasses logo */
        .logo-svg { width: 56px; height: auto; margin-bottom: 4px; }
        .company-name {
            font-size: 18px;
            font-weight: 800;
            color: #1a2b4a;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }
        .company-sub { font-size: 12px; color: #555; margin-top: 2px; line-height: 1.6; }

        .invoice-label-area { text-align: right; }
        .invoice-label {
            font-size: 36px;
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
        .inv-number strong { color: #222; }
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
        .items-table thead th:first-child { border-radius: 0; }
        .items-table tbody tr { border-bottom: 1px solid #eef1f6; }
        .items-table tbody tr:last-child { border-bottom: 2px solid #cdd5e0; }
        .items-table tbody td {
            padding: 11px 14px;
            color: #333;
            font-size: 12.5px;
        }
        .items-table tfoot td { padding: 8px 14px; font-size: 13px; }

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
            }
            @page { margin: 0.8cm; size: A4; }
        }
    </style>
</head>
<body>
    <div class="btn-print-bar">
        <button onclick="window.print()">🖨 Print Invoice</button>
        <a href="{{ url()->previous() }}">← Back</a>
    </div>

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
                    <div class="invoice-label">Tax Invoice</div>
                    <div class="inv-number"># Tax Inv-{{ $invoice->invoice_number }}</div>
                    <div class="balance-label">Balance Due</div>
                    @php
                        $balanceDue = $invoice->payment_status === 'Paid' ? 0 : floatval($invoice->total_amount);
                        $isPaid = $invoice->payment_status === 'Paid';
                    @endphp
                    <div class="balance-amount {{ $isPaid ? 'paid' : '' }}">
                        NZD{{ number_format($balanceDue, 2) }}
                    </div>
                </div>
            </div>

            <hr class="divider">

            <!-- BILL TO + DATES -->
            <div class="meta-row">
                <div>
                    <div class="bill-to-label">Bill To</div>
                    @if($invoice->customer)
                        <!-- <div class="bill-to-name">{{ $invoice->customer->customer_number ?? $invoice->customer->full_name }}</div> -->
                        <div class="bill-to-detail">
                            {{ $invoice->customer->full_name }}<br>
                            @if($invoice->customer->address){{ $invoice->customer->address }}<br>@endif
                            @if($invoice->customer->phone)Phone: {{ $invoice->customer->phone }}<br>@endif
                            @if($invoice->customer->email){{ $invoice->customer->email }}@endif
                        </div>
                    @else
                        <div class="bill-to-name">Walk-in Customer</div>
                    @endif
                </div>

                <div>
                    <table class="dates-table">
                        <tr>
                            <td>Invoice Date :</td>
                            <td>{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <td>Terms :</td>
                            <td>Due end of the month</td>
                        </tr>
                        <tr>
                            <td>Due Date :</td>
                            <td>{{ \Carbon\Carbon::parse($invoice->invoice_date)->endOfMonth()->format('d M Y') }}</td>
                        </tr>
                        @if($invoice->payment_mode)
                        <tr>
                            <td>Payment Mode :</td>
                            <td>{{ $invoice->payment_mode }}</td>
                        </tr>
                        @endif
                        @if($invoice->repair_id)
                        <tr>
                            <td>Ref Repair :</td>
                            <td>#{{ $invoice->repair?->repair_number ?? $invoice->repair_id }}</td>
                        </tr>
                        @endif
                        @if($invoice->order_id)
                        <tr>
                            <td>Ref Order :</td>
                            <td>{{ $invoice->order?->order_number ?? '#' . $invoice->order_id }}</td>
                        </tr>
                        @endif
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
                    @foreach($invoice->items as $index => $item)
                    <tr>
                        <td class="item-num">{{ $index + 1 }}</td>
                        <td>
                            <span style="font-weight:600; color:#1a2b4a;">{{ $item->item_name }}</span>
                            @if($item->sku)<br><small style="color:#999;">SKU: {{ $item->sku }}</small>@endif
                        </td>
                        <td class="text-center">{{ number_format($item->quantity, 2) }}</td>
                        <td class="text-right">{{ number_format($item->rate, 2) }}</td>
                        <td class="text-right" style="font-weight:600;">{{ number_format($item->quantity * $item->rate, 2) }}</td>
                    </tr>
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
                            <td>{{ number_format($invoice->subtotal, 2) }}</td>
                        </tr>
                        @if($invoice->discount_amount > 0)
                        <tr class="row-discount">
                            <td>Discount</td>
                            <td>(-) {{ number_format($invoice->discount_amount, 2) }}</td>
                        </tr>
                        @endif
                        @if($invoice->tax_amount > 0)
                        <tr>
                            <td>GST (Incl. 15%)</td>
                            <td>{{ number_format($invoice->tax_amount, 2) }}</td>
                        </tr>
                        @endif
                        <tr class="row-total">
                            <td>Total</td>
                            <td>NZD{{ number_format($invoice->total_amount, 2) }}</td>
                        </tr>
                        @if($isPaid)
                        <tr class="row-discount">
                            <td>Payment Made</td>
                            <td>(-) {{ number_format($invoice->total_amount, 2) }}</td>
                        </tr>
                        @endif
                        <tr class="row-balance">
                            <td>Balance Due</td>
                            <td>NZD{{ number_format($balanceDue, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- NOTES + FOOTER -->
            <div class="bottom-section">
                <div class="notes-area">
                    <div class="notes-label">Notes</div>
                    <div class="notes-text">
                        @if($invoice->notes){{ $invoice->notes }}@else Prism Eyewear Repairs And Services
Bank: ASB
A/C No: 12-3297-0403694-00

Thanks for your business.@endif
                    </div>
                </div>
                <div class="thank-you">
                    <strong>Thank you for your business!</strong>
                    Please retain this invoice for your records.
                </div>
            </div>
        </div>
    </div>
</body>
</html>
