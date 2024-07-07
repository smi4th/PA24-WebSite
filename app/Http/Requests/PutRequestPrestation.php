<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PutRequestPrestation extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    protected $redirect = '/profile/prestations';

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
            'description' => 'string',
            'duration' => 'string',
            'price' => 'numeric',
            'category' => 'string',
            'image' => 'sometimes|nullable|image|mimes:jpeg,png,svg|max:2048|extensions:jpeg,png,svg',
        ];
    }

    function messages()
    {
        return [
            'description.string' => 'La  description doit être une chaine de caractères',
            'duration.string' => 'La durée doit être une chaine de caractères',
            'price.numeric' => 'Le prix doit être un nombre',
            'category.string' => 'La catégorie doit être une chaine de caractères',
            'image.image' => 'Le fichier doit être une image',
            'image.mimes' => 'Le fichier doit être de type jpeg, png ou svg',
            'image.max' => 'Le fichier ne doit pas dépasser 2 Mo',
            'image.extensions' => 'Le fichier doit être de type jpeg, png ou svg',
        ];

    }
}
