<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class StoreSalesReturnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sale_id' => [
                'required',
                'integer',
                'exists:sales,id',
            ],

            'warehouse_id' => [
                'required',
                'integer',
                'exists:warehouses,id',
            ],

            'return_date' => [
                'required',
                'date',
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

            'items.*.sale_item_id' => [
                'required',
                'integer',
                'exists:sale_items,id',
            ],

            'items.*.quantity' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'items.*.condition' => [
                'nullable',
                'string',
                'in:resellable,damaged,defective',
            ],

            'items.*.restock' => [
                'nullable',
                'boolean',
            ],

            'items.*.notes' => [
                'nullable',
                'string',
            ],
        ];
    }
}