<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostCreatePrestation extends FormRequest
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
            'description' => 'required|string',
            'price' => 'required|numeric',
            'duration' => 'required|date_format:H:i',
            'imgPath' => 'required|image|mimes:jpeg,png,svg|max:2048|extensions:jpeg,png,svg',
            'service_id' => 'required|string'
        ];
    }

    public function messages()
    {
        return [
            'description.required' => 'La description est obligatoire',
            'price.required' => 'Le prix est obligatoire',
            'duration.required' => 'La durée est obligatoire',
            'imgPath.required' => 'L\'image est obligatoire',
            'service_id.required' => 'Le service est obligatoire',
            'imgPath.image' => 'Le fichier doit être une image',
            'imgPath.mimes' => 'Le fichier doit être une image de type jpeg, png ou svg',
            'imgPath.max' => 'Le fichier ne doit pas dépasser 2Mo',
            'imgPath.extensions' => 'Le fichier doit être une image de type jpeg, png ou svg',
            'duration.date_format' => 'La durée doit être au format HH:MM',
            'price.numeric' => 'Le prix doit être un nombre',
            'service_id.string' => 'Le service doit être une chaîne de caractères',

        ];
    }
}
