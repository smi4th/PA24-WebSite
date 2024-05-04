<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'username' => 'required|string|min:4|max:32',
            'email' => 'required|email',
            'password' => ['required','min:8','max:32','regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*(_|[^\w])).+$/'],
            'password_confirmation' => 'required|same:password',
            'firstname' => 'required|string|min:4|max:32',
            'lastname' => 'required|string|min:4|max:32',
            'account_type' => 'required|string'
        ];
    }
    public function messages(): array
    {
        return [
            'name.required' => 'Name is required',
            'email.required' => 'Email is required',
            'email.email' => 'Email is not valid',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 6 characters',
            'password_confirmation.required' => 'Password confirmation is required',
            'password_confirmation.same' => 'Password confirmation must be the same as password',
            'firstname.required' => 'Firstname is required',
            'lastname.required' => 'Lastname is required',
            'account_type.required' => 'Account type is required',
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character',
            'name.string' => 'Name must be a string',
            'name.min' => 'Name must be at least 4 characters',
            'name.max' => 'Name must be at most 32 characters',
            'firstname.string' => 'Firstname must be a string',
            'firstname.min' => 'Firstname must be at least 4 characters',
            'firstname.max' => 'Firstname must be at most 32 characters',
            'lastname.string' => 'Lastname must be a string',
            'lastname.min' => 'Lastname must be at least 4 characters',
            'lastname.max' => 'Lastname must be at most 32 characters',
            'account_type.string' => 'Account type must be a string'

        ];
    }
}
