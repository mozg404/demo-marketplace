<?php

namespace App\Http\Requests\Auth;

use App\ValueObjects\Email;
use Illuminate\Foundation\Http\FormRequest;

class PasswordForgotStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email|exists:users,email',
        ];
    }
}
