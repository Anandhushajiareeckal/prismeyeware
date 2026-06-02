<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
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
            'order_id' => 'nullable|exists:orders,id',
            'repair_id' => 'nullable|exists:repairs,id',
            'invoice_date' => 'required|date',
            'payment_mode' => 'nullable|string',
            'payment_status' => 'required|string',
            'paid_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'staff_name' => 'nullable|string',
            'delivery_charge' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.sku' => 'nullable|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.rate' => 'required|numeric|min:0',
            'items.*.tax' => 'nullable|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
        ];
    }
}
