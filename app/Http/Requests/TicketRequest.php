<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TicketRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:32'],
            'description' => ['required', 'string'],
            'status' => 'required|int',
        ];
    }
    public function messages()
    {
        return [
            'title.required' => 'Un titre est requis',
            'title.string' => 'Le titre doit être une chaîne de caractères',
            'title.max' => 'Le titre ne doit pas dépasser 32 caractères',
            'description.required' => 'Une description est requise',
            'description.string' => 'La description doit être une chaîne de caractères',
            'status.required' => 'Un statut est requis',
        ];
    }
}
