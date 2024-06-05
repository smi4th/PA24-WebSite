<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReviewsRequest extends FormRequest
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
            'note' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:1|max:255',
            'housing' => '',
            'bedroom' => '',
            'service' => '',
        ];
    }
    public function messages()
    {
        return [
            'note.required' => 'La note est obligatoire',
            'note.integer' => 'La note doit être un entier',
            'note.min' => 'La note doit être supérieure à 0',
            'note.max' => 'La note doit être inférieure à 6',
            'comment.required' => 'Le contenu est obligatoire',
            'comment.string' => 'Le contenu doit être une chaine de caractères',
            'comment.min' => 'Le contenu doit être supérieur à 0'
        ];
    }
}
