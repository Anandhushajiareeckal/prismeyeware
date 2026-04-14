<?php

namespace App\Http\Controllers;

use App\Models\Repair;
use App\Http\Requests\StoreRepairRequest;
use App\Http\Requests\UpdateRepairRequest;

use Illuminate\Http\Request;
use App\Models\Customer;

class RepairController extends Controller
{
    public function index(Request $request)
    {
        $query = Repair::with('customer');
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('repair_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($q) use ($search) {
                      $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('phone_number', 'like', "%{$search}%");
                  });
        }
        $repairs = $query->latest('repair_date')->paginate(15);
        return view('repairs.index', compact('repairs'));
    }

    public function create(Request $request)
    {
        $customer_id = $request->get('customer_id');
        $customer = $customer_id ? Customer::find($customer_id) : null;
        return view('repairs.create', compact('customer'));
    }

    public function store(StoreRepairRequest $request)
    {
        $data = $request->validated();
        $data['repair_number'] = 'REP-' . strtoupper(substr(uniqid(), -6));
        $data['created_by'] = auth()->id();

        $repair = Repair::create($data);

        return redirect()->route('repairs.show', $repair)->with('success', 'Repair job created successfully.');
    }

    public function show(Repair $repair)
    {
        $repair->load(['customer', 'invoices']);
        return view('repairs.show', compact('repair'));
    }

    public function edit(Repair $repair)
    {
        return view('repairs.edit', compact('repair'));
    }

    public function update(UpdateRepairRequest $request, Repair $repair)
    {
        $repair->update($request->validated());
        return redirect()->route('repairs.show', $repair)->with('success', 'Repair job updated successfully.');
    }

    public function destroy(Repair $repair)
    {
        $repair->delete();
        return redirect()->route('repairs.index')->with('success', 'Repair job deleted successfully.');
    }
}
