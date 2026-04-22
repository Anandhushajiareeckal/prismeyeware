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
        $latestRepair = \App\Models\Repair::orderBy('id', 'desc')->first();
        
        $nextRepairId = 100;
        if ($latestRepair && is_numeric($latestRepair->repair_number)) {
            $nextRepairId = max(100, intval($latestRepair->repair_number) + 1);
        } else {
            $nextRepairId = max(100, (\App\Models\Repair::max('id') ?? 0) + 1);
        }
        
        $data['repair_number'] = sprintf('%05d', $nextRepairId);
        $data['created_by'] = auth()->id();
        
        $repair_price = 0;
        foreach($data['items'] as $item) {
            $repair_price += ($item['price'] ?? 0);
        }
        $data['repair_price'] = $repair_price;

        $repair = Repair::create($data);
        $repair->items()->createMany($data['items']);

        foreach($data['items'] as $item) {
            if (!empty($item['repair_type'])) {
                \App\Models\RepairType::firstOrCreate(['name' => trim($item['repair_type'])]);
            }
        }

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
        $data = $request->validated();
        
        $repair_price = 0;
        foreach($data['items'] as $item) {
            $repair_price += ($item['price'] ?? 0);
        }
        $data['repair_price'] = $repair_price;

        $repair->update($data);
        
        $repair->items()->delete();
        $repair->items()->createMany($data['items']);
        
        foreach($data['items'] as $item) {
            if (!empty($item['repair_type'])) {
                \App\Models\RepairType::firstOrCreate(['name' => trim($item['repair_type'])]);
            }
        }
        
        return redirect()->route('repairs.show', $repair)->with('success', 'Repair job updated successfully.');
    }

    public function destroy(Repair $repair)
    {
        $repair->delete();
        return redirect()->route('repairs.index')->with('success', 'Repair job deleted successfully.');
    }
}
