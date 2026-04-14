<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Repair;
use App\Models\Invoice;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');

        if (!$query) {
            return redirect()->back()->with('error', 'Please enter a search term.');
        }

        $customers = Customer::where('first_name', 'like', "%{$query}%")
            ->orWhere('last_name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->orWhere('phone', 'like', "%{$query}%")
            ->orWhere('customer_number', 'like', "%{$query}%")
            ->get();

        $orders = Order::where('order_number', 'like', "%{$query}%")->get();
        
        $repairs = Repair::where('repair_number', 'like', "%{$query}%")
            ->orWhere('sku', 'like', "%{$query}%")
            ->get();
            
        $invoices = Invoice::where('invoice_number', 'like', "%{$query}%")->get();

        return view('search.results', compact('query', 'customers', 'orders', 'repairs', 'invoices'));
    }
}
