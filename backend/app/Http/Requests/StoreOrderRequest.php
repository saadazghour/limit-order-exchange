<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Order; // Import the Order model

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by auth:sanctum middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'symbol' => ['required', 'string', 'max:10'],
            'side' => ['required', 'string', Rule::in(['buy', 'sell'])],
            'price' => ['required', 'numeric', 'min:0.00000001'], // Price must be positive
            'amount' => ['required', 'numeric', 'min:0.00000001'], // Amount must be positive
        ];
    }
}
