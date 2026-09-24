<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStockTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'from_warehouse_id' => [
                'required',
                'integer',
                'exists:warehouses,id',
            ],

            'to_warehouse_id' => [
                'required',
                'integer',
                'different:from_warehouse_id',
                'exists:warehouses,id',
            ],

            'notes' => [
                'nullable',
                'string',
            ],

            'items' => [
                'required',
                'array',
                'min:1',
            ],

            'items.*.product_sku_id' => [
                'required',
                'integer',
                'exists:product_skus,id',
            ],

            'items.*.quantity' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'items.*.notes' => [
                'nullable',
                'string',
            ],
        ];
    }
}