<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePrescriptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'prescription_date' => 'required|date',
            'recall_date' => 'nullable|date',
            'type' => 'required|string',
            'doctor_name' => 'nullable|string|max:255',
            'od_sphere' => 'nullable|string|max:255',
            'od_cylinder' => 'nullable|string|max:255',
            'od_axis' => 'nullable|string|max:255',
            'od_h_prism' => 'nullable|string|max:255',
            'od_v_prism' => 'nullable|string|max:255',
            'od_add' => 'nullable|string|max:255',
            'od_pd' => 'nullable|string|max:255',
            'od_fh' => 'nullable|string|max:255',
            'os_sphere' => 'nullable|string|max:255',
            'os_cylinder' => 'nullable|string|max:255',
            'os_axis' => 'nullable|string|max:255',
            'os_h_prism' => 'nullable|string|max:255',
            'os_v_prism' => 'nullable|string|max:255',
            'os_add' => 'nullable|string|max:255',
            'os_pd' => 'nullable|string|max:255',
            'os_fh' => 'nullable|string|max:255',
            'comments' => 'nullable|string',
        ];
    }
}
