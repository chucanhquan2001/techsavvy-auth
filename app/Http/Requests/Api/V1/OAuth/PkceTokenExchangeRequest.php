<?php

namespace App\Http\Requests\Api\V1\OAuth;

use Illuminate\Foundation\Http\FormRequest;

class PkceTokenExchangeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'grant_type' => ['required', 'in:authorization_code'],
            'client_id' => ['required', 'string'],
            'redirect_uri' => ['required', 'string'],
            'code' => ['required', 'string'],
            'code_verifier' => ['required', 'string'],
            'client_secret' => ['nullable', 'string'],
            'scope' => ['nullable', 'string'],
        ];
    }
}
