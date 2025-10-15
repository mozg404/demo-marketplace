<?php

namespace App\Http\Requests\MyOrder;

use Illuminate\Foundation\Http\FormRequest;

class OrderItemFeedbackEditRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'is_positive' => 'required|boolean',
            'comment' => 'nullable|string|min:3|max:255',
        ];
    }
}
