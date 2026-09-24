<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'supplier_id' => [
                'required',
                'integer',
                'exists:suppliers,id',
            ],

            'warehouse_id' => [
                'required',
                'integer',
                'exists:warehouses,id',
            ],

            'invoice_number' => [
                'nullable',
                'string',
                'max:100',
            ],

            'purchase_date' => [
                'required',
                'date',
            ],

            'discount_amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'tax_amount' => [
                'nullable',
                'numeric',
                'min:0',
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

            'items.*.unit_cost' => [
                'required',
                'numeric',
                'min:0',
            ],

            'items.*.discount_amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'items.*.tax_amount' => [
                'nullable',
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