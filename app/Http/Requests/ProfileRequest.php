<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest {
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'uuid' => 'required|uuid',  
            'username' => 'required|string|max:255',  
            'first_name' => 'required|string|max:255',  
            'last_name' => 'required|string|max:255',  
            'email' => 'required|email',  
            'creation_date' => 'required|date',  
            'account_type' => 'required|integer',  
            'provider' => 'nullable|string|max:255',  
            'imgPath' => 'nullable|string|max:255',  
        ];
    }
}