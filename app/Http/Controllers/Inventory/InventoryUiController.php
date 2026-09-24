<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\InventoryCount;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\StockMovement;
use App\Models\StockTransfer;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryUiController extends Controller
{
    public function catalog(
        Request $request
    ): View {
        $query = Product::query()
            ->with([
                'brand',
                'category',
                'variants.skus',
            ]);

        if ($request->filled('search')) {
            $search =
                $request->string('search')
                    ->toString();

            $query->where(
                function ($q) use ($search) {
                    $q->where(
                        'name',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhereHas(
                        'brand',
                        function ($brand) use ($search) {
                            $brand->where(
                                'name',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'name_en',
                                'like',
                                "%{$search}%"
                            );
                        }
                    )
                    ->orWhereHas(
                        'variants.skus',
                        fn ($sku) =>
                            $sku->where(
                                'sku',
                                'like',
                                "%{$search}%"
                            )
                    );
                }
            );
        }

        $items =
            $query
                ->orderBy('name')
                ->paginate(20)
                ->through(
                    fn (Product $product) => [
                        $product->name,

                        $product->brand?->name
                            ?? '-',

                        $product->category?->name
                            ?? '-',

                        $product->variants->count(),

                        $product->variants->sum(
                            fn ($variant) =>
                                $variant->skus->count()
                        ),

                        $product->is_active
                            ? 'نشط'
                            : 'غير نشط',
                    ]
                );

        return $this->table(
            title: 'المنتجات',
            description:
                'الكتالوج الرئيسي للمنتجات والموديلات والمقاسات.',
            headers: [
                'المنتج',
                'العلامة التجارية',
                'التصنيف',
                'الموديلات',
                'SKUs',
                'الحالة',
            ],
            items: $items,
        );
    }

    public function stock(
        Request $request
    ): View {
        $query =
            WarehouseStock::query()
                ->with([
                    'warehouse',
                    'sku.variant.product',
                ]);

        if ($request->filled('search')) {
            $search =
                $request->string('search')
                    ->toString();

            $query->where(
                function ($q) use ($search) {
                    $q->whereHas(
                        'sku',
                        fn ($sku) =>
                            $sku->where(
                                'sku',
                                'like',
                                "%{$search}%"
                            )
                    )
                    ->orWhereHas(
                        'sku.variant.product',
                        fn ($product) =>
                            $product->where(
                                'name',
                                'like',
                                "%{$search}%"
                            )
                    )
                    ->orWhereHas(
                        'warehouse',
                        fn ($warehouse) =>
                            $warehouse->where(
                                'name',
                                'like',
                                "%{$search}%"
                            )
                    );
                }
            );
        }

        $items =
            $query
                ->latest('updated_at')
                ->paginate(25)
                ->through(
                    fn (WarehouseStock $stock) => [
                        $stock->warehouse?->name
                            ?? '-',

                        $stock->sku
                            ?->variant
                            ?->product
                            ?->name
                            ?? '-',

                        $stock->sku
                            ?->variant
                            ?->name
                            ?? '-',

                        $stock->sku?->size_label
                            ?? '-',

                        $stock->sku?->sku
                            ?? '-',

                        number_format(
                            (float) $stock->quantity,
                            3
                        ),

                        number_format(
                            (float) $stock->reserved_quantity,
                            3
                        ),

                        number_format(
                            (float) $stock->quantity
                            -
                            (float) $stock->reserved_quantity,
                            3
                        ),

                        number_format(
                            (float) $stock->average_cost,
                            2
                        ),
                    ]
                );

        return $this->table(
            title: 'المخزون الحالي',
            description:
                'الكميات الفعلية والمحجوزة والمتاحة لكل SKU.',
            headers: [
                'المخزن',
                'المنتج',
                'الموديل',
                'المقاس',
                'SKU',
                'الكمية',
                'محجوز',
                'متاح',
                'متوسط التكلفة',
            ],
            items: $items,
        );
    }

    public function warehouses(
        Request $request
    ): View {
        $query =
            Warehouse::query()
                ->withCount('stocks')
                ->withSum(
                    'stocks as total_quantity',
                    'quantity'
                )
                ->withSum(
                    'stocks as reserved_quantity',
                    'reserved_quantity'
                );

        if ($request->filled('search')) {
            $search =
                $request->string('search')
                    ->toString();

            $query->where(
                function ($q) use ($search) {
                    $q->where(
                        'name',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'code',
                        'like',
                        "%{$search}%"
                    );
                }
            );
        }

        $items =
            $query
                ->orderByDesc('is_default')
                ->orderBy('name')
                ->paginate(20)
                ->through(
                    fn (Warehouse $warehouse) => [
                        $warehouse->name,
                        $warehouse->code,
                        $warehouse->stocks_count,

                        number_format(
                            (float) (
                                $warehouse->total_quantity
                                ?? 0
                            ),
                            3
                        ),

                        number_format(
                            (float) (
                                $warehouse->reserved_quantity
                                ?? 0
                            ),
                            3
                        ),

                        $warehouse->is_default
                            ? 'رئيسي'
                            : '-',

                        $warehouse->is_active
                            ? 'نشط'
                            : 'متوقف',
                    ]
                );

        return $this->table(
            title: 'المخازن',
            description:
                'إدارة ومتابعة مواقع التخزين.',
            headers: [
                'المخزن',
                'الكود',
                'عدد الأصناف',
                'إجمالي الكمية',
                'محجوز',
                'النوع',
                'الحالة',
            ],
            items: $items,
        );
    }

    public function purchases(
        Request $request
    ): View {
        $query =
            Purchase::query()
                ->with([
                    'supplier',
                    'warehouse',
                ]);

        if ($request->filled('search')) {
            $search =
                $request->string('search')
                    ->toString();

            $query->where(
                function ($q) use ($search) {
                    $q->where(
                        'purchase_number',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'invoice_number',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhereHas(
                        'supplier',
                        fn ($supplier) =>
                            $supplier->where(
                                'name',
                                'like',
                                "%{$search}%"
                            )
                    );
                }
            );
        }

        $items =
            $query
                ->latest('purchase_date')
                ->paginate(20)
                ->through(
                    fn (Purchase $purchase) => [
                        $purchase->purchase_number,

                        $purchase->supplier?->name
                            ?? '-',

                        $purchase->warehouse?->name
                            ?? '-',

                        optional(
                            $purchase->purchase_date
                        )->format('Y-m-d'),

                        $purchase->status,

                        number_format(
                            (float) $purchase->total_amount,
                            2
                        ),

                        number_format(
                            (float) $purchase->paid_amount,
                            2
                        ),
                    ]
                );

        return $this->table(
            title: 'المشتريات',
            description:
                'فواتير الشراء واستلام البضاعة من الموردين.',
            headers: [
                'رقم العملية',
                'المورد',
                'المخزن',
                'التاريخ',
                'الحالة',
                'الإجمالي',
                'المدفوع',
            ],
            items: $items,
        );
    }

    public function sales(
        Request $request
    ): View {
        $query =
            Sale::query()
                ->with([
                    'customer',
                    'warehouse',
                ]);

        if ($request->filled('search')) {
            $search =
                $request->string('search')
                    ->toString();

            $query->where(
                function ($q) use ($search) {
                    $q->where(
                        'sale_number',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhereHas(
                        'customer',
                        fn ($customer) =>
                            $customer->where(
                                'name',
                                'like',
                                "%{$search}%"
                            )
                    );
                }
            );
        }

        $items =
            $query
                ->latest('sale_date')
                ->paginate(20)
                ->through(
                    fn (Sale $sale) => [
                        $sale->sale_number,

                        $sale->customer?->name
                            ?? 'عميل نقدي',

                        $sale->warehouse?->name
                            ?? '-',

                        optional(
                            $sale->sale_date
                        )->format('Y-m-d'),

                        $sale->status,

                        number_format(
                            (float) $sale->total_amount,
                            2
                        ),

                        number_format(
                            (float) $sale->paid_amount,
                            2
                        ),
                    ]
                );

        return $this->table(
            title: 'المبيعات',
            description:
                'عمليات البيع والحجز والتحصيل.',
            headers: [
                'رقم العملية',
                'العميل',
                'المخزن',
                'التاريخ',
                'الحالة',
                'الإجمالي',
                'المدفوع',
            ],
            items: $items,
        );
    }

    public function transfers(): View
    {
        $items =
            StockTransfer::query()
                ->with([
                    'fromWarehouse',
                    'toWarehouse',
                ])
                ->latest()
                ->paginate(20)
                ->through(
                    fn (StockTransfer $transfer) => [
                        $transfer->transfer_number,

                        $transfer->fromWarehouse?->name
                            ?? '-',

                        $transfer->toWarehouse?->name
                            ?? '-',

                        $transfer->status,

                        optional(
                            $transfer->transferred_at
                        )?->format(
                            'Y-m-d H:i'
                        ) ?? '-',

                        optional(
                            $transfer->received_at
                        )?->format(
                            'Y-m-d H:i'
                        ) ?? '-',
                    ]
                );

        return $this->table(
            title: 'تحويلات المخازن',
            description:
                'حركة نقل الأصناف بين المخازن.',
            headers: [
                'رقم التحويل',
                'من',
                'إلى',
                'الحالة',
                'وقت التحويل',
                'وقت الاستلام',
            ],
            items: $items,
            searchable: false,
        );
    }

    public function counts(): View
    {
        $items =
            InventoryCount::query()
                ->with('warehouse')
                ->latest()
                ->paginate(20)
                ->through(
                    fn (InventoryCount $count) => [
                        $count->count_number,

                        $count->warehouse?->name
                            ?? '-',

                        $count->status,

                        optional(
                            $count->started_at
                        )?->format(
                            'Y-m-d H:i'
                        ) ?? '-',

                        optional(
                            $count->completed_at
                        )?->format(
                            'Y-m-d H:i'
                        ) ?? '-',
                    ]
                );

        return $this->table(
            title: 'الجرد',
            description:
                'جلسات الجرد ومقارنة الكمية الفعلية بالنظام.',
            headers: [
                'رقم الجرد',
                'المخزن',
                'الحالة',
                'بدأ',
                'اكتمل',
            ],
            items: $items,
            searchable: false,
        );
    }

    public function suppliers(
        Request $request
    ): View {
        $query =
            Supplier::query()
                ->withCount('purchases');

        if ($request->filled('search')) {
            $search =
                $request->string('search')
                    ->toString();

            $query->where(
                function ($q) use ($search) {
                    $q->where(
                        'name',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'code',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'phone',
                        'like',
                        "%{$search}%"
                    );
                }
            );
        }

        $items =
            $query
                ->orderBy('name')
                ->paginate(20)
                ->through(
                    fn (Supplier $supplier) => [
                        $supplier->name,
                        $supplier->code,
                        $supplier->phone ?? '-',
                        $supplier->email ?? '-',
                        $supplier->purchases_count,

                        $supplier->is_active
                            ? 'نشط'
                            : 'متوقف',
                    ]
                );

        return $this->table(
            title: 'الموردون',
            description:
                'بيانات الموردين وسجل التعاملات.',
            headers: [
                'المورد',
                'الكود',
                'الهاتف',
                'البريد',
                'المشتريات',
                'الحالة',
            ],
            items: $items,
        );
    }

    public function customers(
        Request $request
    ): View {
        $query =
            Customer::query()
                ->withCount('sales');

        if ($request->filled('search')) {
            $search =
                $request->string('search')
                    ->toString();

            $query->where(
                function ($q) use ($search) {
                    $q->where(
                        'name',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'code',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'phone',
                        'like',
                        "%{$search}%"
                    );
                }
            );
        }

        $items =
            $query
                ->orderBy('name')
                ->paginate(20)
                ->through(
                    fn (Customer $customer) => [
                        $customer->name,
                        $customer->code,
                        $customer->phone ?? '-',
                        $customer->email ?? '-',
                        $customer->sales_count,

                        $customer->is_active
                            ? 'نشط'
                            : 'متوقف',
                    ]
                );

        return $this->table(
            title: 'العملاء',
            description:
                'بيانات العملاء وسجل المبيعات.',
            headers: [
                'العميل',
                'الكود',
                'الهاتف',
                'البريد',
                'المبيعات',
                'الحالة',
            ],
            items: $items,
        );
    }

    public function movements(
        Request $request
    ): View {
        $query =
            StockMovement::query()
                ->with([
                    'warehouse',
                    'sku.variant.product',
                    'user',
                ]);

        if ($request->filled('search')) {
            $search =
                $request->string('search')
                    ->toString();

            $query->where(
                function ($q) use ($search) {
                    $q->where(
                        'movement_type',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhereHas(
                        'sku',
                        fn ($sku) =>
                            $sku->where(
                                'sku',
                                'like',
                                "%{$search}%"
                            )
                    );
                }
            );
        }

        $items =
            $query
                ->latest('occurred_at')
                ->paginate(25)
                ->through(
                    fn (StockMovement $movement) => [
                        optional(
                            $movement->occurred_at
                        )?->format(
                            'Y-m-d H:i'
                        ) ?? '-',

                        $movement->warehouse?->name
                            ?? '-',

                        $movement->sku
                            ?->variant
                            ?->product
                            ?->name
                            ?? '-',

                        $movement->sku?->sku
                            ?? '-',

                        $movement->movement_type,

                        number_format(
                            (float) $movement->quantity_change,
                            3
                        ),

                        number_format(
                            (float) $movement->balance_after,
                            3
                        ),

                        $movement->user?->name
                            ?? '-',
                    ]
                );

        return $this->table(
            title: 'سجل حركات المخزون',
            description:
                'Ledger كامل لكل دخول وخروج وتعديل في المخزون.',
            headers: [
                'التاريخ',
                'المخزن',
                'المنتج',
                'SKU',
                'نوع الحركة',
                'التغيير',
                'الرصيد',
                'المستخدم',
            ],
            items: $items,
        );
    }

    private function table(
        string $title,
        string $description,
        array $headers,
        $items,
        bool $searchable = true
    ): View {
        return view(
            'inventory.table',
            compact(
                'title',
                'description',
                'headers',
                'items',
                'searchable',
            )
        );
    }
}