<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReservationRequest extends FormRequest
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
            'date_start' => 'required|date',
            'date_end' => 'required|date|after:date_start',
        ];
    }
    public function messages()
    {
        return [
            "date_start.required" => "start date is required",
            "date_start.date" => "start date must be a valid date",

            "date_end.required" => "end date is required",
            "date_end.date" => "end date must be a valid date",
            "date_end.after" => "end date must be after start date",

        ];
    }
}
