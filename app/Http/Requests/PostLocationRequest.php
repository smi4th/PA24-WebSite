<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostLocationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */

    protected $redirect = '/travel/createLocation';
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
        //dd($this->all());

        $rules = [
            "surface" => "required|numeric|min:10",
            "price.*" => "required|numeric|min:10",
            "street_nb" => "required|string",
            "city" => "required|string",
            "price_housing"=> "required|numeric|min:10",
            "zip_code" => "required|string",
            "street" => "required|string",
            "title" => "required|string",
            "description_housing" => "required|string",
            "house_type" => "required|integer",
            "imgPathHousing" => "required",
            "description" => "required|array",
            "imgPath" => "required|array",
            "imgPath.*" => "required|image|mimes:jpeg,png,jpg,svg|max:2048",
            "description.*" => "required|string|max:255|min:10",
            "nbPlaces.*" => "required|integer|min:1",
            "imgPathHousing.*" => "required|image|mimes:jpeg,png,jpg,svg|max:2048",
            "equipment_type" => "required",
            "nameEquipment.*" => "required|string",
            "descriptionEquipment.*" => "required|string",
            "imgPathEquipment.*" => "required|image|mimes:jpeg,png,jpg,svg|max:2048",
            "priceEquipement.*" => "required|numeric|min:0",
            'chamber_associated.*' => 'integer',
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'surface.required' => 'La surface est obligatoire',
            'surface.numeric' => 'La surface doit être un nombre',
            'surface.min' => 'La surface doit être supérieure à 10',
            'price.required' => 'Le prix est obligatoire',
            'price.numeric' => 'Le prix doit être un nombre',
            'price.min' => 'Le prix doit être supérieur à 10',
            'street_nb.required' => 'Le numéro de rue est obligatoire',
            'street_nb.string' => 'Le numéro de rue doit être une chaine de caractères',
            'city.required' => 'La ville est obligatoire',
            'city.string' => 'La ville doit être une chaine de caractères',
            'zip_code.required' => 'Le code postal est obligatoire',
            'zip_code.string' => 'Le code postal doit être une chaine de caractères',
            'street.required' => 'La rue est obligatoire',
            'street.string' => 'La rue doit être une chaine de caractères',
            'title.required' => 'Le titre est obligatoire',
            'title.string' => 'Le titre doit être une chaine de caractères',
            'description_housing.required' => 'La description est obligatoire',
            'description_housing.string' => 'La description doit être une chaine de caractères',
            'house_type.required' => 'Le type de logement est obligatoire',
            'house_type.integer' => 'Le type de logement doit être un nombre entier',
            'imgPathHousing.*.required' => 'L\'image est obligatoire',
            'imgPathHousing.*.image' => 'L\'image doit être une image',
            'imgPathHousing.*.mimes' => 'L\'image doit être de type jpeg, png, jpg, svg',
            'imgPathHousing.*.max' => 'L\'image doit être inférieure à 2Mo',
            'nbPlaces.*.required' => 'Le nombre de places est obligatoire',
            'nbPlaces.*.integer' => 'Le nombre de places doit être un nombre entier',
            'nbPlaces.*.min' => 'Le nombre de places doit être supérieur à 1',
            'description.*.required' => 'La description est obligatoire',
            'description.*.string' => 'La description doit être une chaine de caractères',
            'description.*.max' => 'La description doit être inférieure à 255 caractères',
            'description.*.min' => 'La description doit être supérieure à 10 caractères',
            'imgPath.*.required' => 'L\'image est obligatoire',
            'imgPath.*.image' => 'L\'image doit être une image',
            'imgPath.*.mimes' => 'L\'image doit être de type jpeg, png, jpg, svg',
            'imgPath.*.max' => 'L\'image doit être inférieure à 2Mo',
            'description.required' => 'La description est obligatoire',
            'description.array' => 'La description doit être un tableau',
            'nbPlaces.required' => 'Le nombre de places est obligatoire',
            'imgPath.required' => 'L\'image est obligatoire',
            'imgPath.array' => 'L\'image doit être un tableau',
            'chamber_associated.*.integer' => 'La chambre associée doit être un nombre entier',
            'price.required' => 'Le prix est obligatoire',
            'price.numeric' => 'Le prix doit être un nombre',
            'price.min' => 'Le prix doit être supérieur à 10',
            'equipment_type.*.required' => 'Le type d\'équipement est obligatoire',
            'equipment_type.*.integer' => 'Le type d\'équipement doit être un nombre entier',
            'nameEquipment.*.required' => 'Le nom de l\'équipement est obligatoire',
            'nameEquipment.*.string' => 'Le nom de l\'équipement doit être une chaine de caractères',
            'descriptionEquipment.*.required' => 'La description de l\'équipement est obligatoire',
            'descriptionEquipment.*.string' => 'La description de l\'équipement doit être une chaine de caractères',
            'imgPathEquipment.*.required' => 'L\'image de l\'équipement est obligatoire',
            'imgPathEquipment.*.image' => 'L\'image de l\'équipement doit être une image',
            'imgPathEquipment.*.mimes' => 'L\'image de l\'équipement doit être de type jpeg, png, jpg',
            'imgPathEquipment.*.max' => 'L\'image de l\'équipement doit être inférieure à 2Mo',
            'priceEquipement.*.required' => 'Le prix de l\'équipement est obligatoire',
            'priceEquipement.*.numeric' => 'Le prix de l\'équipement doit être un nombre',
            'priceEquipement.*.min' => 'Le prix de l\'équipement doit être supérieur à 0',
            'validation.required' => 'Tout les champs sont obligatoires',
        ];
    }
}
