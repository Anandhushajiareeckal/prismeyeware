<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #{{ $invoice->invoice_number }}</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 0; font-family: monospace; color: #000; background-color: #f8f9fa; font-size: 13px; }
        .ticket { width: 80mm; max-width: 80mm; margin: 20px auto; background: #fff; padding: 10px 10px 30px; }
        h1, h2, h3, h4, h5, h6, p { margin: 0; padding: 0; }
        .centered { text-align: center; }
        .title { font-size: 1.4rem; font-weight: bold; margin-bottom: 2px; }
        .text-bold { font-weight: bold; }
        .divider { border-top: 1px dashed #000; margin: 8px 0; }
        .divider-solid { border-top: 1px solid #000; margin: 8px 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 3px 0; vertical-align: top; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .item-name { display: block; max-width: 44mm; word-wrap: break-word; line-height: 1.3; }
        .btn-print { margin: 20px auto; text-align: center; width: 80mm; display: flex; gap: 10px; justify-content: center; }
        .btn { padding: 12px 20px; font-family: sans-serif; font-weight: bold; cursor: pointer; text-decoration: none; border-radius: 4px; border: none; font-size: 14px; }
        .btn-primary { background: #000; color: #fff; }
        .btn-secondary { background: #e2e8f0; color: #000; }
        @media print {
            body { background: #fff; }
            .ticket { margin: 0; padding: 0; width: 100%; }
            .btn-print { display: none !important; }
            @page { margin: 0; width: 80mm; }
        }
    </style>
</head>
<body>
@php
    // Ensure relationships are always loaded regardless of how we got here
    $invoice->loadMissing(['customer', 'items']);

    $subtotal       = floatval($invoice->subtotal ?? 0);
    $taxAmount      = floatval($invoice->tax_amount ?? 0);
    $discountAmount = floatval($invoice->discount_amount ?? 0);
    $totalAmount    = floatval($invoice->total_amount ?? ($subtotal - $discountAmount + $taxAmount));
@endphp

    <div class="btn-print">
        <button onclick="window.print()" class="btn btn-primary">PRINT</button>
        <a href="{{ url()->previous() }}" class="btn btn-secondary">CLOSE</a>
    </div>

    <div class="ticket">
        <div class="centered" style="margin-bottom: 8px;">
            <div class="title">PRISM EYEWEAR</div>
            <p style="font-size:0.8rem;">Frames | Lenses | Repairs</p>
            <p style="font-size:0.8rem; margin-top:3px;"><strong>TAX RECEIPT</strong></p>
        </div>

        <div class="divider"></div>

        <table style="margin: 4px 0;">
            <tr>
                <td class="text-bold" style="width: 35%;">Rcpt #:</td>
                <td>{{ $invoice->invoice_number }}</td>
            </tr>
            <tr>
                <td class="text-bold">Date:</td>
                <td>{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d-M-Y') }}</td>
            </tr>
            <tr>
                <td class="text-bold">Customer:</td>
                <td>{{ $invoice->customer ? substr($invoice->customer->full_name, 0, 22) : 'Walk-in' }}</td>
            </tr>
            <tr>
                <td class="text-bold">Status:</td>
                <td>
                    {{ strtoupper($invoice->payment_status ?? 'N/A') }}
                    @if($invoice->payment_mode) ({{ $invoice->payment_mode }}) @endif
                </td>
            </tr>
        </table>

        <div class="divider"></div>

        <table style="margin-bottom: 4px;">
            <thead>
                <tr>
                    <th style="width:55%; border-bottom:1px solid #000; padding-bottom:4px; text-align:left;">Item</th>
                    <th style="width:10%; border-bottom:1px solid #000; padding-bottom:4px; text-align:center;">Qty</th>
                    <th style="width:35%; border-bottom:1px solid #000; padding-bottom:4px; text-align:right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoice->items as $item)
                @php
                    $qty       = intval($item->quantity ?? 1);
                    $rate      = floatval($item->rate ?? 0);
                    $disc      = floatval($item->discount ?? 0);
                    $tax       = floatval($item->tax ?? 0);
                    $lineTotal = ($qty * $rate) - $disc + $tax;
                @endphp
                <tr>
                    <td style="padding-top:6px;">
                        <span class="item-name">{{ $item->item_name }}</span>
                    </td>
                    <td class="text-center" style="padding-top:6px;">{{ $qty }}</td>
                    <td class="text-right text-bold" style="padding-top:6px;">${{ number_format($lineTotal, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" style="text-align:center; padding:8px 0; font-style:italic;">No items found</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="divider-solid"></div>

        <table>
            <tr>
                <td colspan="2" class="text-right">Subtotal:</td>
                <td class="text-right" style="width:35%;">${{ number_format($subtotal, 2) }}</td>
            </tr>
            @if($discountAmount > 0)
            <tr>
                <td colspan="2" class="text-right">Discount:</td>
                <td class="text-right">-${{ number_format($discountAmount, 2) }}</td>
            </tr>
            @endif
            @if($taxAmount > 0)
            <tr>
                <td colspan="2" class="text-right">Tax (Incl. 15%):</td>
                <td class="text-right">${{ number_format($taxAmount, 2) }}</td>
            </tr>
            @endif
            <tr>
                <td colspan="2" class="text-right text-bold" style="padding-top:8px; font-size:1.1rem; border-top:1px solid #000;">TOTAL:</td>
                <td class="text-right text-bold" style="padding-top:8px; font-size:1.1rem; border-top:1px solid #000;">${{ number_format($totalAmount, 2) }}</td>
            </tr>
        </table>

        <div class="divider" style="margin-top:12px;"></div>

        <div class="centered" style="margin-top:12px;">
            <p class="text-bold" style="margin-bottom:4px; font-size:1rem;">Thank You!</p>
            <p style="font-size:0.8rem;">Please retain receipt for warranty purposes.</p>
            <p style="font-size:0.75rem; margin-top:15px;">Printed: {{ now()->format('d-M-Y H:i') }}</p>
            <p style="margin-top:8px; font-size:10px;">============================</p>
        </div>
    </div>
</body>
</html>
