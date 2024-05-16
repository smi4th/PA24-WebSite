<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LocationRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'dates_form' => 'required|json',
            'equipment_form' => 'json',
        ];
    }

    public function messages(): array
    {
        return [
            'dates_form.required' => 'dates must be required',
            'dates_form.json' => 'they have a problem with the date send',
            'equipment_form.json' => 'equipment_form must be a valid json',
        ];
    }
}
