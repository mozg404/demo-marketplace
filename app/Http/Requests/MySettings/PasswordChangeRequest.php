<?php

namespace App\Http\Requests\MySettings;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class PasswordChangeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'old_password' => ['required', 'string', 'min:8', 'max:255'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ];
    }
}
