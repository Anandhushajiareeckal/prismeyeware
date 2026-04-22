<?php

namespace App\Http\Controllers;

use App\Models\CustomerDocument;
use App\Http\Requests\StoreCustomerDocumentRequest;
use App\Http\Requests\UpdateCustomerDocumentRequest;

class CustomerDocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerDocumentRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $path = $file->store('customer_documents', 'public');
            CustomerDocument::create([
                'customer_id' => $data['customer_id'],
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'file_type' => $file->getClientOriginalExtension(),
                'uploaded_by' => auth()->id(),
            ]);
        }
        return back()->with('success', 'Document uploaded successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CustomerDocument $customerDocument)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CustomerDocument $customerDocument)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerDocumentRequest $request, CustomerDocument $customerDocument)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CustomerDocument $customerDocument)
    {
        //
    }
}
