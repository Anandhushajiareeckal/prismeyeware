<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #{{ $invoice->invoice_number }}</title>
    <style>
        body { margin: 0; padding: 0; font-family: monospace; color: #000; background-color: #f8f9fa; font-size: 14px; }
        .ticket { width: 80mm; max-width: 80mm; margin: 20px auto; background: #fff; padding: 10px; padding-bottom: 30px; }
        h1, h2, h3, h4, h5, h6, p { margin: 0; padding: 0; }
        .centered { text-align: center; }
        .title { font-size: 1.5rem; font-weight: bold; margin-bottom: 2px; }
        .text-bold { font-weight: bold; }
        .divider { border-top: 1px dashed #000; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 4px 0; vertical-align: top; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .item-name { display: block; max-width: 45mm; word-wrap: break-word; line-height: 1.2; }
        
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
    <div class="btn-print">
        <button onclick="window.print()" class="btn btn-primary">PRINT</button>
        <a href="{{ url()->previous() }}" class="btn btn-secondary">CLOSE</a>
    </div>

    <div class="ticket">
        <div class="centered" style="margin-bottom: 10px;">
            <div class="title">PRISM EYEWEAR</div>
            <p>123 Optical Avenue</p>
            <p>Tel: (555) 123-4567</p>
        </div>
        
        <div class="divider"></div>
        
        <table style="margin: 5px 0;">
            <tr>
                <td class="text-bold" style="width: 35%;">Rcpt #:</td>
                <td>{{ $invoice->invoice_number }}</td>
            </tr>
            <tr>
                <td class="text-bold">Date:</td>
                <td>{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d-M-Y H:i') }}</td>
            </tr>
            <tr>
                <td class="text-bold">Customer:</td>
                <td>{{ $invoice->customer ? substr($invoice->customer->full_name, 0, 20) : 'Walk-in' }}</td>
            </tr>
            <tr>
                <td class="text-bold">Status:</td>
                <td>{{ strtoupper($invoice->payment_status) }} {!! $invoice->payment_mode ? "<br>({$invoice->payment_mode})" : "" !!}</td>
            </tr>
        </table>
        
        <div class="divider"></div>
        
        <table style="margin-bottom: 5px;">
            <thead>
                <tr>
                    <th class="text-left" style="width: 55%; border-bottom: 1px solid #000; padding-bottom: 5px;">Item</th>
                    <th class="text-center" style="width: 15%; border-bottom: 1px solid #000; padding-bottom: 5px;">Qty</th>
                    <th class="text-right" style="width: 30%; border-bottom: 1px solid #000; padding-bottom: 5px;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                <tr>
                    <td style="padding-top: 8px;">
                        <span class="item-name">{{ $item->item_name }}</span>
                    </td>
                    <td class="text-center" style="padding-top: 8px;">{{ $item->quantity }}</td>
                    <td class="text-right text-bold" style="padding-top: 8px;">${{ number_format(($item->quantity * $item->rate) - $item->discount + $item->tax, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="divider" style="border-top-style: solid;"></div>
        
        <table>
            <tr>
                <td colspan="2" class="text-right">Subtotal:</td>
                <td class="text-right" style="width: 35%;">${{ number_format($invoice->subtotal, 2) }}</td>
            </tr>
            @if($invoice->discount_amount > 0)
            <tr>
                <td colspan="2" class="text-right">Discount:</td>
                <td class="text-right">-${{ number_format($invoice->discount_amount, 2) }}</td>
            </tr>
            @endif
            @if($invoice->tax_amount > 0)
            <tr>
                <td colspan="2" class="text-right">Tax:</td>
                <td class="text-right">+${{ number_format($invoice->tax_amount, 2) }}</td>
            </tr>
            @endif
            <tr>
                <td colspan="2" class="text-right text-bold" style="padding-top: 10px; font-size: 1.2rem;">TOTAL:</td>
                <td class="text-right text-bold" style="padding-top: 10px; font-size: 1.2rem;">${{ number_format($invoice->total_amount, 2) }}</td>
            </tr>
        </table>
        
        <div class="divider" style="margin-top: 15px;"></div>
        
        <div class="centered" style="margin-top: 15px;">
            <p class="text-bold" style="margin-bottom: 5px; font-size: 1.1rem;">Thank You!</p>
            <p style="font-size: 0.85rem;">Please retain receipt<br>for warranty purposes.</p>
            <p style="font-size: 0.8rem; margin-top: 20px;">Printed: {{ now()->format('d-M-Y H:i:s') }}</p>
            
            <p style="margin-top: 10px; font-size: 10px;">============================</p>
        </div>
    </div>
</body>
</html>
