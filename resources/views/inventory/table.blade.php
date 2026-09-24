<x-layouts::app :title="$title">
    <div
        dir="rtl"
        class="flex w-full flex-1 flex-col gap-6"
    >
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"
        >
            <div>
                <p
                    class="mb-1 text-sm font-medium text-zinc-500 dark:text-zinc-400"
                >
                    نظام إدارة المخزون
                </p>

                <h1
                    class="text-2xl font-bold tracking-tight text-zinc-950 dark:text-white"
                >
                    {{ $title }}
                </h1>

                <p
                    class="mt-2 text-sm text-zinc-500 dark:text-zinc-400"
                >
                    {{ $description }}
                </p>
            </div>

            @if ($searchable)
                <form
                    method="GET"
                    class="w-full sm:w-80"
                >
                    <div class="relative">
                        <input
                            type="search"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="بحث..."
                            class="w-full rounded-xl border border-zinc-200 bg-white px-4 py-2.5 text-sm text-zinc-900 shadow-sm outline-none transition focus:border-zinc-400 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white dark:focus:border-zinc-600 dark:focus:ring-zinc-800"
                        >
                    </div>
                </form>
            @endif
        </div>

        <div
            class="overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900"
        >
            <div class="overflow-x-auto">
                <table class="w-full text-right text-sm">
                    <thead
                        class="border-b border-zinc-200 bg-zinc-50 text-xs font-semibold text-zinc-500 dark:border-zinc-700 dark:bg-zinc-800/70 dark:text-zinc-400"
                    >
                        <tr>
                            @foreach ($headers as $header)
                                <th
                                    class="whitespace-nowrap px-5 py-4"
                                >
                                    {{ $header }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>

                    <tbody
                        class="divide-y divide-zinc-100 dark:divide-zinc-800"
                    >
                        @forelse ($items as $row)
                            <tr
                                class="transition hover:bg-zinc-50/80 dark:hover:bg-zinc-800/50"
                            >
                                @foreach ($row as $value)
                                    <td
                                        class="whitespace-nowrap px-5 py-4 text-zinc-700 dark:text-zinc-200"
                                    >
                                        {{ $value }}
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td
                                    colspan="{{ count($headers) }}"
                                    class="px-6 py-16 text-center"
                                >
                                    <div
                                        class="mx-auto max-w-sm"
                                    >
                                        <p
                                            class="font-semibold text-zinc-800 dark:text-zinc-200"
                                        >
                                            لا توجد بيانات حاليًا
                                        </p>

                                        <p
                                            class="mt-1 text-sm text-zinc-500"
                                        >
                                            ستظهر البيانات هنا بمجرد إضافة عمليات للنظام.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div>
            {{ $items->withQueryString()->links() }}
        </div>
    </div>
</x-layouts::app>