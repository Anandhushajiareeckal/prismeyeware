<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Repair;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with('customer');

        if ($request->filled('invoice_number')) {
            $query->where('invoice_number', 'like', "%" . $request->invoice_number . "%");
        }

        if ($request->filled('date_from')) {
            $query->whereDate('invoice_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('invoice_date', '<=', $request->date_to);
        }

        if ($request->filled('customer_name')) {
            $search = $request->customer_name;
            $query->whereHas('customer', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
            });
        }

        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        }

        $invoices = $query->latest('invoice_date')->paginate(15)->withQueryString();

        return view('invoices.index', compact('invoices'));
    }

    public function create(Request $request)
    {
        $customer_id = $request->get('customer_id');
        $customer = $customer_id ? Customer::find($customer_id) : null;
        $repair = $request->get('repair_id') ? Repair::with('items')->find($request->get('repair_id')) : null;
        return view('invoices.create', compact('customer', 'repair'));
    }

    public function store(StoreInvoiceRequest $request)
    {
        $data = $request->validated();
        
        $subtotal = 0;
        $totalTax = 0;
        $totalDiscount = 0;
        
        foreach ($data['items'] as &$item) {
            $itemSubtotal = $item['rate'] * $item['quantity'];
            // Inclusive tax calculation: Tax = Total - (Total / 1.15)
            $item['tax'] = ($itemSubtotal - ($item['discount'] ?? 0)) - (($itemSubtotal - ($item['discount'] ?? 0)) / 1.15);
            
            $item['total'] = $itemSubtotal - ($item['discount'] ?? 0);

            $subtotal += ($item['rate'] * $item['quantity']);
            $totalTax += ($item['tax'] ?? 0);
            $totalDiscount += ($item['discount'] ?? 0);
        }
        
        $totalAmount = $subtotal - $totalDiscount;

        $invoice = Invoice::create([
            'invoice_number' => 'INV-' . strtoupper(substr(uniqid(), -6)),
            'customer_id' => $data['customer_id'],
            'order_id' => $data['order_id'] ?? null,
            'repair_id' => $data['repair_id'] ?? null,
            'invoice_date' => $data['invoice_date'],
            'subtotal' => $subtotal,
            'tax_amount' => $totalTax,
            'discount_amount' => $totalDiscount,
            'total_amount' => $totalAmount,
            'payment_mode' => $data['payment_mode'] ?? null,
            'payment_status' => $data['payment_status'],
            'notes' => $data['notes'] ?? null,
            'staff_name' => $data['staff_name'] ?? null,
        ]);

        $invoice->items()->createMany($data['items']);

        if ($invoice->order_id && $invoice->payment_status === 'Paid') {
            Order::where('id', $invoice->order_id)->update(['order_status' => 'Completed']);
        }

        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice created successfully.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['customer', 'items']);
        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $invoice->load('items');
        return view('invoices.edit', compact('invoice'));
    }

    public function update(UpdateInvoiceRequest $request, Invoice $invoice)
    {
        $data = $request->validated();

        $subtotal = 0;
        $totalTax = 0;
        $totalDiscount = 0;
        
        $invoice->items()->delete();

        foreach ($data['items'] as &$item) {
            $itemSubtotal = $item['rate'] * $item['quantity'];
            // Inclusive tax calculation: Tax = Total - (Total / 1.15)
            $item['tax'] = ($itemSubtotal - ($item['discount'] ?? 0)) - (($itemSubtotal - ($item['discount'] ?? 0)) / 1.15);
            
            $item['total'] = $itemSubtotal - ($item['discount'] ?? 0);

            $subtotal += ($item['rate'] * $item['quantity']);
            $totalTax += ($item['tax'] ?? 0);
            $totalDiscount += ($item['discount'] ?? 0);
        }
        
        $totalAmount = $subtotal - $totalDiscount;

        $invoice->update([
            'invoice_date' => $data['invoice_date'],
            'subtotal' => $subtotal,
            'tax_amount' => $totalTax,
            'discount_amount' => $totalDiscount,
            'total_amount' => $totalAmount,
            'payment_mode' => $data['payment_mode'] ?? null,
            'payment_status' => $data['payment_status'],
            'notes' => $data['notes'] ?? null,
            'staff_name' => $data['staff_name'] ?? null,
        ]);

        $invoice->items()->createMany($data['items']);

        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice updated successfully.');
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', 'Invoice deleted successfully.');
    }

    public function printA4(Invoice $invoice)
    {
        $invoice->load(['customer', 'items']);
        return view('invoices.print_a4', compact('invoice'));
    }

    public function printThermal(Invoice $invoice)
    {
        $invoice->load(['customer', 'items', 'repair']);
        return view('invoices.print_thermal', compact('invoice'));
    }
}
