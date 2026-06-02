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

        // Search Filter (Repair No., Reference, Customer Name/Phone)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('repair_number', 'like', "%{$search}%")
                  ->orWhere('reference', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($cq) use ($search) {
                      $cq->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('phone_number', 'like', "%{$search}%");
                  });
            });
        }

        // Date Filter
        if ($request->filled('date')) {
            $query->whereDate('repair_date', $request->date);
        }

        // Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $repairs = $query->latest('repair_date')->paginate(15)->withQueryString();
        return view('repairs.index', compact('repairs'));
    }

    public function create(Request $request)
    {
        $customer_id = $request->get('customer_id');
        $customer = $customer_id ? Customer::find($customer_id) : null;
        $prescriptionTypes = \App\Models\PrescriptionType::where('status', 'Active')->orderBy('name')->get();
        return view('repairs.create', compact('customer', 'prescriptionTypes'));
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
        
        // Filter out empty rows
        $data['items'] = array_filter($data['items'] ?? [], function($item) {
            return !empty($item['repair_type']);
        });
        $data['lenses'] = array_filter($data['lenses'] ?? [], function($lens) {
            return !empty($lens['lens_type']);
        });

        $repair_price = 0;
        foreach($data['items'] as $item) {
            $repair_price += ($item['price'] ?? 0);
        }
        foreach($data['lenses'] as $lens) {
            $repair_price += ($lens['price'] ?? 0);
        }
        $data['repair_price'] = $repair_price;

        $repair = Repair::create($data);
        
        // Save Repair Items
        foreach($data['items'] as $item) {
            $repair->items()->create([
                'repair_type' => $item['repair_type'],
                'price' => $item['price'] ?? 0,
                'item_type' => 'Repair'
            ]);
        }
        
        // Save Lens Items
        if (!empty($data['lenses'])) {
            foreach($data['lenses'] as $lens) {
                $repair->items()->create([
                    'repair_type' => $lens['lens_type'],
                    'price' => $lens['price'] ?? 0,
                    'item_type' => 'Lens'
                ]);
            }
        }

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
        $prescriptionTypes = \App\Models\PrescriptionType::where('status', 'Active')->orderBy('name')->get();
        return view('repairs.edit', compact('repair', 'prescriptionTypes'));
    }

    public function update(UpdateRepairRequest $request, Repair $repair)
    {
        $data = $request->validated();
        
        // Filter out empty rows
        $data['items'] = array_filter($data['items'] ?? [], function($item) {
            return !empty($item['repair_type']);
        });
        $data['lenses'] = array_filter($data['lenses'] ?? [], function($lens) {
            return !empty($lens['lens_type']);
        });

        $repair_price = 0;
        foreach($data['items'] as $item) {
            $repair_price += ($item['price'] ?? 0);
        }
        foreach($data['lenses'] as $lens) {
            $repair_price += ($lens['price'] ?? 0);
        }
        $data['repair_price'] = $repair_price;

        $repair->update($data);
        
        $repair->items()->delete();
        
        // Save Repair Items
        foreach($data['items'] as $item) {
            $repair->items()->create([
                'repair_type' => $item['repair_type'],
                'price' => $item['price'] ?? 0,
                'item_type' => 'Repair'
            ]);
        }
        
        // Save Lens Items
        if (!empty($data['lenses'])) {
            foreach($data['lenses'] as $lens) {
                $repair->items()->create([
                    'repair_type' => $lens['lens_type'],
                    'price' => $lens['price'] ?? 0,
                    'item_type' => 'Lens'
                ]);
            }
        }
        
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
