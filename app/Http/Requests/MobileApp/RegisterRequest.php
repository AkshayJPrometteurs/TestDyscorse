<?php

namespace App\Http\Requests\MobileApp;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'auth_type' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|unique:users,email',
            'gender' => 'required',
            'age' => '',
          	'max_streak' => '',
            'password' => 'required',
            'terms_policy_status' => 'required',
            'splash_que_ans' => '',
        ];
    }
}
