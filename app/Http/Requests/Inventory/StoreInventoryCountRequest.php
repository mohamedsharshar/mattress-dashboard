<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class StoreInventoryCountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'warehouse_id' => [
                'required',
                'integer',
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

            'items.*.counted_quantity' => [
                'required',
                'numeric',
                'min:0',
            ],

            'items.*.notes' => [
                'nullable',
                'string',
            ],
        ];
    }
}