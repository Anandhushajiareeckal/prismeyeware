<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRepairRequest extends FormRequest
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
            'reference'   => 'nullable|string|max:255',
            'sku' => 'nullable|string|max:255',
            'repair_date' => 'required|date',
            
            // Items and Lenses are both arrays
            'items' => 'nullable|array',
            'items.*.repair_type' => 'nullable|string|max:255',
            'items.*.price' => 'nullable|numeric|min:0',
            
            'lenses' => 'nullable|array',
            'lenses.*.lens_type' => 'nullable|string|max:255',
            'lenses.*.price' => 'nullable|numeric|min:0',
            
            'repair_notes' => 'nullable|string',
            'collection_notes' => 'nullable|string',
            'assigned_staff' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:Pending,In Progress,Completed,Collected,Cancelled',
            'completion_date' => 'nullable|date',
            'collected_date' => 'nullable|date',
            'delivery_charge' => 'nullable|numeric|min:0',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $hasRepair = false;
            if ($this->has('items')) {
                foreach ($this->items as $item) {
                    if (!empty($item['repair_type'])) {
                        $hasRepair = true;
                        break;
                    }
                }
            }

            $hasLens = false;
            if ($this->has('lenses')) {
                foreach ($this->lenses as $lens) {
                    if (!empty($lens['lens_type'])) {
                        $hasLens = true;
                        break;
                    }
                }
            }

            if (!$hasRepair && !$hasLens) {
                $validator->errors()->add('items', 'Please select at least one Repair Type or Lens.');
            }
        });
    }
}
