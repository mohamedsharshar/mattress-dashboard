<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payable_type' => [
                'required',
                'string',
                Rule::in([
                    'sale',
                    'purchase',
                    'sales_return',
                    'purchase_return',
                ]),
            ],

            'payable_id' => [
                'required',
                'integer',
                'min:1',
            ],

            'direction' => [
                'required',
                'string',
                Rule::in([
                    'in',
                    'out',
                ]),
            ],

            'method' => [
                'required',
                'string',
                'max:50',
            ],

            'amount' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'reference_number' => [
                'nullable',
                'string',
                'max:100',
            ],

            'paid_at' => [
                'nullable',
                'date',
            ],

            'notes' => [
                'nullable',
                'string',
            ],
        ];
    }
}