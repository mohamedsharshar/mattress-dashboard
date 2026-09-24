<x-layouts::app :title="'لوحة التحكم'">
    <div
        dir="rtl"
        class="flex w-full flex-1 flex-col gap-6"
    >
        <div
            class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between"
        >
            <div>
                <p
                    class="text-sm font-medium text-zinc-500 dark:text-zinc-400"
                >
                    نظرة عامة
                </p>

                <h1
                    class="mt-1 text-3xl font-bold tracking-tight text-zinc-950 dark:text-white"
                >
                    لوحة إدارة المخزن
                </h1>

                <p
                    class="mt-2 text-sm text-zinc-500 dark:text-zinc-400"
                >
                    ملخص لحظي للمنتجات والمخزون والمبيعات والمشتريات.
                </p>
            </div>

            <div
                class="rounded-xl border border-zinc-200 bg-white px-4 py-2 text-sm text-zinc-500 shadow-sm dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-400"
            >
                {{ now()->format('Y-m-d') }}
            </div>
        </div>

        <div
            class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4"
        >
            <div
                class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900"
            >
                <p class="text-sm text-zinc-500">
                    المنتجات
                </p>

                <p
                    class="mt-3 text-3xl font-bold text-zinc-950 dark:text-white"
                >
                    {{ number_format($stats['products']) }}
                </p>

                <p
                    class="mt-2 text-xs text-zinc-400"
                >
                    {{ number_format($stats['skus']) }} SKU
                </p>
            </div>

            <div
                class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900"
            >
                <p class="text-sm text-zinc-500">
                    إجمالي المخزون
                </p>

                <p
                    class="mt-3 text-3xl font-bold text-zinc-950 dark:text-white"
                >
                    {{ number_format($stats['stock_quantity'], 0) }}
                </p>

                <p
                    class="mt-2 text-xs text-zinc-400"
                >
                    محجوز:
                    {{ number_format($stats['reserved_quantity'], 0) }}
                </p>
            </div>

            <div
                class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900"
            >
                <p class="text-sm text-zinc-500">
                    قيمة المخزون
                </p>

                <p
                    class="mt-3 text-3xl font-bold text-zinc-950 dark:text-white"
                >
                    {{ number_format($stats['inventory_value'], 0) }}
                </p>

                <p
                    class="mt-2 text-xs text-zinc-400"
                >
                    جنيه مصري
                </p>
            </div>

            <div
                class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900"
            >
                <p class="text-sm text-zinc-500">
                    المخازن
                </p>

                <p
                    class="mt-3 text-3xl font-bold text-zinc-950 dark:text-white"
                >
                    {{ number_format($stats['warehouses']) }}
                </p>

                <p
                    class="mt-2 text-xs text-zinc-400"
                >
                    مخازن نشطة
                </p>
            </div>
        </div>

        <div
            class="grid gap-4 lg:grid-cols-2"
        >
            <div
                class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900"
            >
                <div
                    class="flex items-center justify-between"
                >
                    <div>
                        <p
                            class="text-sm text-zinc-500"
                        >
                            مبيعات اليوم
                        </p>

                        <p
                            class="mt-2 text-2xl font-bold text-zinc-950 dark:text-white"
                        >
                            {{ number_format($stats['today_sales'], 2) }}
                            ج.م
                        </p>
                    </div>

                    <a
                        href="{{ route('manage.sales') }}"
                        class="text-sm font-medium text-zinc-500 hover:text-zinc-900 dark:hover:text-white"
                    >
                        عرض المبيعات ←
                    </a>
                </div>
            </div>

            <div
                class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900"
            >
                <div
                    class="flex items-center justify-between"
                >
                    <div>
                        <p
                            class="text-sm text-zinc-500"
                        >
                            مشتريات اليوم
                        </p>

                        <p
                            class="mt-2 text-2xl font-bold text-zinc-950 dark:text-white"
                        >
                            {{ number_format($stats['today_purchases'], 2) }}
                            ج.م
                        </p>
                    </div>

                    <a
                        href="{{ route('manage.purchases') }}"
                        class="text-sm font-medium text-zinc-500 hover:text-zinc-900 dark:hover:text-white"
                    >
                        عرض المشتريات ←
                    </a>
                </div>
            </div>
        </div>

        <div
            class="grid gap-6 xl:grid-cols-[1.35fr_1fr]"
        >
            <div
                class="overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900"
            >
                <div
                    class="flex items-center justify-between border-b border-zinc-200 px-5 py-4 dark:border-zinc-700"
                >
                    <div>
                        <h2
                            class="font-bold text-zinc-950 dark:text-white"
                        >
                            آخر حركات المخزون
                        </h2>

                        <p
                            class="mt-1 text-xs text-zinc-500"
                        >
                            آخر عمليات الدخول والخروج والتعديل
                        </p>
                    </div>

                    <a
                        href="{{ route('manage.movements') }}"
                        class="text-sm text-zinc-500 hover:text-zinc-900 dark:hover:text-white"
                    >
                        عرض الكل
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table
                        class="w-full text-right text-sm"
                    >
                        <thead
                            class="bg-zinc-50 text-xs text-zinc-500 dark:bg-zinc-800/70"
                        >
                            <tr>
                                <th class="px-5 py-3">
                                    المنتج
                                </th>
                                <th class="px-5 py-3">
                                    الحركة
                                </th>
                                <th class="px-5 py-3">
                                    الكمية
                                </th>
                                <th class="px-5 py-3">
                                    المخزن
                                </th>
                            </tr>
                        </thead>

                        <tbody
                            class="divide-y divide-zinc-100 dark:divide-zinc-800"
                        >
                            @forelse ($recentMovements as $movement)
                                <tr>
                                    <td
                                        class="px-5 py-4"
                                    >
                                        <div
                                            class="font-medium text-zinc-900 dark:text-zinc-100"
                                        >
                                            {{
                                                $movement
                                                    ->sku
                                                    ?->variant
                                                    ?->product
                                                    ?->name
                                                ?? '-'
                                            }}
                                        </div>

                                        <div
                                            class="mt-1 text-xs text-zinc-400"
                                        >
                                            {{ $movement->sku?->sku }}
                                        </div>
                                    </td>

                                    <td
                                        class="px-5 py-4 text-zinc-500"
                                    >
                                        {{ $movement->movement_type }}
                                    </td>

                                    <td
                                        class="px-5 py-4 font-semibold"
                                    >
                                        {{
                                            number_format(
                                                (float) $movement->quantity_change,
                                                3
                                            )
                                        }}
                                    </td>

                                    <td
                                        class="px-5 py-4 text-zinc-500"
                                    >
                                        {{ $movement->warehouse?->name }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td
                                        colspan="4"
                                        class="px-5 py-12 text-center text-zinc-500"
                                    >
                                        لا توجد حركات مخزون حتى الآن.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div
                class="rounded-2xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900"
            >
                <div
                    class="border-b border-zinc-200 px-5 py-4 dark:border-zinc-700"
                >
                    <h2
                        class="font-bold text-zinc-950 dark:text-white"
                    >
                        آخر المبيعات
                    </h2>
                </div>

                <div
                    class="divide-y divide-zinc-100 dark:divide-zinc-800"
                >
                    @forelse ($recentSales as $sale)
                        <div
                            class="flex items-center justify-between gap-4 px-5 py-4"
                        >
                            <div>
                                <p
                                    class="font-medium text-zinc-900 dark:text-zinc-100"
                                >
                                    {{ $sale->sale_number }}
                                </p>

                                <p
                                    class="mt-1 text-xs text-zinc-500"
                                >
                                    {{
                                        $sale->customer?->name
                                        ?? 'عميل نقدي'
                                    }}
                                </p>
                            </div>

                            <div class="text-left">
                                <p
                                    class="font-semibold text-zinc-900 dark:text-zinc-100"
                                >
                                    {{
                                        number_format(
                                            (float) $sale->total_amount,
                                            2
                                        )
                                    }}
                                </p>

                                <p
                                    class="mt-1 text-xs text-zinc-500"
                                >
                                    {{ $sale->status }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div
                            class="px-5 py-12 text-center text-sm text-zinc-500"
                        >
                            لا توجد مبيعات بعد.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        @if ($lowStock->isNotEmpty())
            <div
                class="rounded-2xl border border-amber-200 bg-amber-50 p-5 dark:border-amber-900/60 dark:bg-amber-950/20"
            >
                <h2
                    class="font-bold text-amber-900 dark:text-amber-300"
                >
                    تنبيه مخزون منخفض
                </h2>

                <div
                    class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-4"
                >
                    @foreach ($lowStock as $stock)
                        <div
                            class="rounded-xl bg-white p-4 dark:bg-zinc-900"
                        >
                            <p
                                class="font-semibold text-zinc-900 dark:text-white"
                            >
                                {{
                                    $stock
                                        ->sku
                                        ?->variant
                                        ?->product
                                        ?->name
                                    ?? '-'
                                }}
                            </p>

                            <p
                                class="mt-1 text-xs text-zinc-500"
                            >
                                {{ $stock->warehouse?->name }}
                            </p>

                            <p
                                class="mt-3 text-sm font-bold text-amber-700 dark:text-amber-400"
                            >
                                متاح:
                                {{
                                    number_format(
                                        (float) $stock->quantity
                                        -
                                        (float) $stock->reserved_quantity,
                                        3
                                    )
                                }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-layouts::app>