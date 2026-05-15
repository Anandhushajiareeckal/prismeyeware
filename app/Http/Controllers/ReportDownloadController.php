<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Customer;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportDownloadController extends Controller
{
    /**
     * Download a single invoice as PDF.
     */
    public function downloadSingle(Invoice $invoice)
    {
        $invoice->load(['customer', 'items', 'repair', 'order']);

        $pdf = Pdf::loadView('invoices.pdf_invoice', compact('invoice'))
            ->setPaper('a4', 'portrait');

        $filename = 'invoice-' . $invoice->invoice_number . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Download bulk selected invoices — one PDF per invoice, or all merged into one PDF.
     * We generate a multi-page PDF with each invoice on its own page.
     */
    public function downloadBulk(Request $request)
    {
        $invoiceIds = $request->input('invoices', []);

        if (empty($invoiceIds)) {
            return back()->with('error', 'No invoices selected for download.');
        }

        $invoices = Invoice::whereIn('id', $invoiceIds)
            ->with(['customer', 'items', 'repair', 'order'])
            ->orderBy('invoice_date', 'desc')
            ->get();

        $pdf = Pdf::loadView('invoices.pdf_bulk', compact('invoices'))
            ->setPaper('a4', 'portrait');

        $filename = 'invoices-bulk-' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Download ALL filtered invoices for a customer as a single multi-page PDF.
     */
    public function downloadCustomerAll(Request $request, Customer $customer)
    {
        $query = Invoice::where('customer_id', $customer->id)
            ->with(['repair', 'order', 'items', 'customer']);

        if ($request->filled('date_from')) {
            $query->whereDate('invoice_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('invoice_date', '<=', $request->date_to);
        }
        if ($request->filled('invoice_number')) {
            $query->where('invoice_number', 'like', '%' . $request->invoice_number . '%');
        }
        if ($request->filled('reference')) {
            $reference = $request->reference;
            $query->where(function ($q) use ($reference) {
                $q->whereHas('repair', function ($q2) use ($reference) {
                    $q2->where('repair_number', 'like', "%{$reference}%")
                       ->orWhere('reference', 'like', "%{$reference}%");
                })->orWhereHas('order', function ($q2) use ($reference) {
                    $q2->where('order_number', 'like', "%{$reference}%");
                });
            });
        }
        if ($request->filled('status') && $request->status !== 'All') {
            $query->where('payment_status', $request->status);
        }

        $invoices = $query->orderBy('invoice_date', 'desc')->get();

        if ($invoices->isEmpty()) {
            return back()->with('error', 'No invoices found to download.');
        }

        $pdf = Pdf::loadView('invoices.pdf_bulk', compact('invoices'))
            ->setPaper('a4', 'portrait');

        $filename = 'report-' . str()->slug($customer->full_name) . '-' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }
}
