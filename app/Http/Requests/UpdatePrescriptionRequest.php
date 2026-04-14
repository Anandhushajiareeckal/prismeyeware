<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePrescriptionRequest extends FormRequest
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
            'prescription_date' => 'required|date',
            'recall_date' => 'nullable|date',
            'type' => 'required|string',
            'doctor_name' => 'nullable|string|max:255',
            'eye_side' => 'required|string|in:R,L,Both',
            'sphere' => 'nullable|string|max:255',
            'cylinder' => 'nullable|string|max:255',
            'axis' => 'nullable|string|max:255',
            'h_prism' => 'nullable|string|max:255',
            'v_prism' => 'nullable|string|max:255',
            'add' => 'nullable|string|max:255',
            'intermediate_add' => 'nullable|string|max:255',
            'comments' => 'nullable|string',
        ];
    }
}
