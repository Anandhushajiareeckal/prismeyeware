<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreRepairRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'reference'   => 'nullable|string|max:255',
            'sku' => 'nullable|string|max:255',
            'repair_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.repair_type' => 'required|string|max:255',
            'items.*.price' => 'nullable|numeric|min:0',
            'repair_notes' => 'nullable|string',
            'collection_notes' => 'nullable|string',
            'assigned_staff' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:Pending,In Progress,Completed,Collected,Cancelled',
            'completion_date' => 'nullable|date',
            'collected_date' => 'nullable|date',
        ];
    }
}
