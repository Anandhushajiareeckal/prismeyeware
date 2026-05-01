<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Invoice;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query();

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('customer_number', 'like', "%{$search}%");
            });
        }

        $customers = $query->orderBy('first_name')->paginate(20);

        return view('reports.index', compact('customers'));
    }

    public function customer(Request $request, Customer $customer)
    {
        $query = Invoice::where('customer_id', $customer->id)->with(['repair', 'order']);

        if ($request->filled('date_from')) {
            $query->whereDate('invoice_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('invoice_date', '<=', $request->date_to);
        }

        if ($request->filled('invoice_number')) {
            $query->where('invoice_number', 'like', "%{$request->invoice_number}%");
        }

        if ($request->filled('reference')) {
            $reference = $request->reference;
            $query->where(function($q) use ($reference) {
                $q->whereHas('repair', function($q2) use ($reference) {
                    $q2->where('repair_number', 'like', "%{$reference}%")
                       ->orWhere('reference', 'like', "%{$reference}%");
                })->orWhereHas('order', function($q2) use ($reference) {
                    $q2->where('order_number', 'like', "%{$reference}%");
                });
            });
        }

        if ($request->filled('status') && $request->status !== 'All') {
            $query->where('payment_status', $request->status);
        }

        $invoices = $query->orderBy('invoice_date', 'desc')->paginate(20);

        return view('reports.customer', compact('customer', 'invoices'));
    }

    public function printSelected(Request $request)
    {
        $invoiceIds = $request->input('invoices', []);
        
        if (empty($invoiceIds)) {
            return back()->with('error', 'No invoices selected for printing.');
        }

        $invoices = Invoice::whereIn('id', $invoiceIds)->with(['customer', 'items', 'repair', 'order'])->orderBy('invoice_date', 'desc')->get();

        return view('reports.print_selected', compact('invoices'));
    }
}
