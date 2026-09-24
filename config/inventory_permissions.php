<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Roles
    |--------------------------------------------------------------------------
    */

    'roles' => [

        'admin' => [
            '*',
        ],

        'manager' => [
            'catalog.view',

            'warehouses.view',
            'warehouses.manage',

            'suppliers.view',
            'suppliers.manage',

            'customers.view',
            'customers.manage',

            'purchases.view',
            'purchases.create',
            'purchases.receive',

            'sales.view',
            'sales.create',
            'sales.complete',
            'sales.reserve',

            'transfers.view',
            'transfers.create',
            'transfers.complete',

            'counts.view',
            'counts.create',
            'counts.complete',

            'returns.sales',
            'returns.purchase',

            'payments.create',
        ],

        'warehouse' => [
            'catalog.view',

            'warehouses.view',

            'suppliers.view',

            'purchases.view',
            'purchases.receive',

            'transfers.view',
            'transfers.create',
            'transfers.complete',

            'counts.view',
            'counts.create',
            'counts.complete',

            'returns.purchase',
        ],

        'sales' => [
            'catalog.view',

            'warehouses.view',

            'customers.view',
            'customers.manage',

            'sales.view',
            'sales.create',
            'sales.complete',
            'sales.reserve',

            'returns.sales',

            'payments.create',
        ],

        'viewer' => [
            'catalog.view',

            'warehouses.view',

            'suppliers.view',

            'customers.view',

            'purchases.view',

            'sales.view',

            'transfers.view',

            'counts.view',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Route → Permission Map
    |--------------------------------------------------------------------------
    */

    'routes' => [

        'inventory.warehouses.index'
            => 'warehouses.view',

        'inventory.warehouses.store'
            => 'warehouses.manage',

        'inventory.warehouses.update'
            => 'warehouses.manage',


        'inventory.suppliers.index'
            => 'suppliers.view',

        'inventory.suppliers.store'
            => 'suppliers.manage',

        'inventory.suppliers.update'
            => 'suppliers.manage',


        'inventory.customers.index'
            => 'customers.view',

        'inventory.customers.store'
            => 'customers.manage',

        'inventory.customers.update'
            => 'customers.manage',


        'inventory.catalog.index'
            => 'catalog.view',

        'inventory.catalog.show'
            => 'catalog.view',


        'inventory.purchases.index'
            => 'purchases.view',

        'inventory.purchases.store'
            => 'purchases.create',

        'inventory.purchases.show'
            => 'purchases.view',

        'inventory.purchases.receive'
            => 'purchases.receive',


        'inventory.sales.index'
            => 'sales.view',

        'inventory.sales.store'
            => 'sales.create',

        'inventory.sales.show'
            => 'sales.view',

        'inventory.sales.reserve'
            => 'sales.reserve',

        'inventory.sales.release-reservation'
            => 'sales.reserve',

        'inventory.sales.complete'
            => 'sales.complete',


        'inventory.transfers.index'
            => 'transfers.view',

        'inventory.transfers.store'
            => 'transfers.create',

        'inventory.transfers.show'
            => 'transfers.view',

        'inventory.transfers.complete'
            => 'transfers.complete',


        'inventory.counts.index'
            => 'counts.view',

        'inventory.counts.store'
            => 'counts.create',

        'inventory.counts.show'
            => 'counts.view',

        'inventory.counts.complete'
            => 'counts.complete',


        'inventory.sales-returns.store'
            => 'returns.sales',

        'inventory.sales-returns.show'
            => 'returns.sales',

        'inventory.sales-returns.complete'
            => 'returns.sales',


        'inventory.purchase-returns.store'
            => 'returns.purchase',

        'inventory.purchase-returns.show'
            => 'returns.purchase',

        'inventory.purchase-returns.complete'
            => 'returns.purchase',


        'inventory.payments.store'
            => 'payments.create',
    ],
];