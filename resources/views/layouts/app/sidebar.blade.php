<!DOCTYPE html>
<html
    lang="ar"
    dir="rtl"
    class="dark"
>
    <head>
        @include('partials.head')
    </head>

    <body
        class="min-h-screen bg-white dark:bg-zinc-800"
    >
        <flux:sidebar
            sticky
            collapsible="mobile"
            class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900"
        >
            <flux:sidebar.header>
                <x-app-logo
                    :sidebar="true"
                    href="{{ route('dashboard') }}"
                    wire:navigate
                />

                <flux:sidebar.collapse
                    class="lg:hidden"
                />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group
                    heading="الرئيسية"
                    class="grid"
                >
                    <flux:sidebar.item
                        icon="home"
                        :href="route('dashboard')"
                        :current="request()->routeIs('dashboard')"
                        wire:navigate
                    >
                        لوحة التحكم
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group
                    heading="المنتجات والمخزون"
                    class="grid"
                >
                    <flux:sidebar.item
                        :href="route('manage.catalog')"
                        :current="request()->routeIs('manage.catalog')"
                        wire:navigate
                    >
                        المنتجات
                    </flux:sidebar.item>

                    <flux:sidebar.item
                        :href="route('manage.stock')"
                        :current="request()->routeIs('manage.stock')"
                        wire:navigate
                    >
                        المخزون الحالي
                    </flux:sidebar.item>

                    <flux:sidebar.item
                        :href="route('manage.warehouses')"
                        :current="request()->routeIs('manage.warehouses')"
                        wire:navigate
                    >
                        المخازن
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group
                    heading="العمليات"
                    class="grid"
                >
                    <flux:sidebar.item
                        :href="route('manage.purchases')"
                        :current="request()->routeIs('manage.purchases')"
                        wire:navigate
                    >
                        المشتريات
                    </flux:sidebar.item>

                    <flux:sidebar.item
                        :href="route('manage.sales')"
                        :current="request()->routeIs('manage.sales')"
                        wire:navigate
                    >
                        المبيعات
                    </flux:sidebar.item>

                    <flux:sidebar.item
                        :href="route('manage.transfers')"
                        :current="request()->routeIs('manage.transfers')"
                        wire:navigate
                    >
                        تحويلات المخازن
                    </flux:sidebar.item>

                    <flux:sidebar.item
                        :href="route('manage.counts')"
                        :current="request()->routeIs('manage.counts')"
                        wire:navigate
                    >
                        الجرد
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group
                    heading="الحسابات"
                    class="grid"
                >
                    <flux:sidebar.item
                        :href="route('manage.suppliers')"
                        :current="request()->routeIs('manage.suppliers')"
                        wire:navigate
                    >
                        الموردون
                    </flux:sidebar.item>

                    <flux:sidebar.item
                        :href="route('manage.customers')"
                        :current="request()->routeIs('manage.customers')"
                        wire:navigate
                    >
                        العملاء
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group
                    heading="التقارير"
                    class="grid"
                >
                    <flux:sidebar.item
                        :href="route('manage.movements')"
                        :current="request()->routeIs('manage.movements')"
                        wire:navigate
                    >
                        حركات المخزون
                    </flux:sidebar.item>
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

            <div
                class="mx-2 mb-3 rounded-xl border border-zinc-200 bg-white p-3 dark:border-zinc-700 dark:bg-zinc-800"
            >
                <p
                    class="truncate text-sm font-semibold text-zinc-900 dark:text-white"
                >
                    {{ auth()->user()->name }}
                </p>

                <p
                    class="mt-1 truncate text-xs text-zinc-500"
                >
                    {{ auth()->user()->email }}
                </p>

                <p
                    class="mt-2 inline-flex rounded-lg bg-zinc-100 px-2 py-1 text-[11px] font-semibold uppercase text-zinc-600 dark:bg-zinc-700 dark:text-zinc-300"
                >
                    {{ auth()->user()->role ?? 'viewer' }}
                </p>
            </div>

            <x-desktop-user-menu
                class="hidden lg:block"
                :name="auth()->user()->name"
            />
        </flux:sidebar>

        <flux:header class="lg:hidden">
            <flux:sidebar.toggle
                class="lg:hidden"
                icon="bars-2"
                inset="left"
            />

            <flux:spacer />

            <flux:dropdown
                position="top"
                align="end"
            >
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <div
                        class="p-3 text-sm"
                    >
                        <div
                            class="font-semibold"
                        >
                            {{ auth()->user()->name }}
                        </div>

                        <div
                            class="mt-1 text-xs text-zinc-500"
                        >
                            {{ auth()->user()->email }}
                        </div>
                    </div>

                    <flux:menu.separator />

                    <flux:menu.item
                        :href="route('profile.edit')"
                        icon="cog"
                        wire:navigate
                    >
                        الإعدادات
                    </flux:menu.item>

                    <flux:menu.separator />

                    <form
                        method="POST"
                        action="{{ route('logout') }}"
                    >
                        @csrf

                        <flux:menu.item
                            as="button"
                            type="submit"
                            class="w-full cursor-pointer"
                        >
                            تسجيل الخروج
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>