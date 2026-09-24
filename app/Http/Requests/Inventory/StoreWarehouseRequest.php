<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreWarehouseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $warehouse = $this->route('warehouse');

        return [
            'name' => [
                'required',
                'string',
                'max:191',
            ],

            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('warehouses', 'code')
                    ->ignore($warehouse),
            ],

            'phone' => [
                'nullable',
                'string',
                'max:50',
            ],

            'address' => [
                'nullable',
                'string',
            ],

            'is_default' => [
                'sometimes',
                'boolean',
            ],

            'is_active' => [
                'sometimes',
                'boolean',
            ],

            'notes' => [
                'nullable',
                'string',
            ],
        ];
    }
}