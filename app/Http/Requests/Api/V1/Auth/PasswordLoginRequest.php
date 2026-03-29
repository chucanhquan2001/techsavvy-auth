<?php

namespace App\Http\Requests\Api\V1\Auth;

use Illuminate\Foundation\Http\FormRequest;

class PasswordLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string'],
            'client_id' => ['required', 'string'],
            'client_secret' => ['nullable', 'string'],
            'scope' => ['nullable', 'string'],
        ];
    }
}
