<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\PriceList;
use App\Models\ProductLine;
use App\Models\Size;
use Illuminate\Database\Seeder;

class BedJanssenPillowsSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Base Data
        |--------------------------------------------------------------------------
        */

        $brand = Brand::where('name_en', 'Bed Janssen')->firstOrFail();

        $category = Category::where(
            'slug',
            'pillows'
        )->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | Price List
        |--------------------------------------------------------------------------
        */

        $priceList = PriceList::updateOrCreate(
            [
                'brand_id' => $brand->id,
                'category_id' => $category->id,
                'title' => 'خدادية ومخدات بيد يانسن',
            ],
            [
                'effective_date' => '2026-04-02',
                'is_active' => true,
            ]
        );

        /*
         * لو الـ seeder اتشغل أكتر من مرة
         * نمسح المنتجات القديمة للقائمة ونبنيها من جديد.
         *
         * product_line_prices هتتمسح تلقائي
         * بسبب cascadeOnDelete.
         */
        $priceList->productLines()->delete();

        /*
        |--------------------------------------------------------------------------
        | 1. Individual Cushions
        |--------------------------------------------------------------------------
        |
        | الأصناف دي كل واحد منهم له مقاس واحد.
        |
        */

        $individualProducts = [
            [
                'name' => 'خدادية ميموري فوم استندر',
                'size' => '60*40',
                'price' => 850,
            ],
            [
                'name' => 'خدادية ميموري فوم كونتور',
                'size' => '60*30',
                'price' => 1200,
            ],
            [
                'name' => 'خدادية ميموري جيل',
                'size' => '60*40',
                'price' => 1500,
            ],
            [
                'name' => 'خدادية فاير (اوشن)',
                'size' => '70*50',
                'price' => 250,
            ],
            [
                'name' => 'خدادية مايكرو فاير (هيفن)',
                'size' => '70*50',
                'price' => 750,
            ],
        ];

        $sortOrder = 1;

        foreach ($individualProducts as $product) {
            $productLine = ProductLine::create([
                'price_list_id' => $priceList->id,
                'name' => $product['name'],
                'sort_order' => $sortOrder++,
                'is_active' => true,
            ]);

            $size = Size::firstOrCreate(
                [
                    'label' => $product['size'],
                ],
                [
                    'sort_order' => 100,
                ]
            );

            $productLine->prices()->create([
                'size_id' => $size->id,
                'price' => $product['price'],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 2. Fiber Pillow
        |--------------------------------------------------------------------------
        |
        | بدل ما نعمل ProductLine لكل مقاس،
        | نعمل ProductLine واحدة وأسعار متعددة.
        |
        */

        $fiberPillow = ProductLine::create([
            'price_list_id' => $priceList->id,
            'name' => 'مخدة فاير',
            'sort_order' => $sortOrder++,
            'is_active' => true,
        ]);

        $fiberPrices = [
            '100*40' => 340,
            '120*40' => 410,
            '140*40' => 480,
            '150*40' => 510,
            '160*40' => 540,
            '170*40' => 580,
            '180*40' => 610,
            '200*40' => 680,
        ];

        $this->seedPrices(
            $fiberPillow,
            $fiberPrices,
            110
        );

        /*
        |--------------------------------------------------------------------------
        | 3. Micro Fiber Pillow
        |--------------------------------------------------------------------------
        */

        $microFiberPillow = ProductLine::create([
            'price_list_id' => $priceList->id,
            'name' => 'مخدة مايكرو فاير',
            'sort_order' => $sortOrder,
            'is_active' => true,
        ]);

        $microFiberPrices = [
            '100*40' => 1030,
            '120*40' => 1230,
            '140*40' => 1440,
            '150*40' => 1540,
            '160*40' => 1640,
            '170*40' => 1740,
            '180*40' => 1840,
            '200*40' => 2050,
        ];

        $this->seedPrices(
            $microFiberPillow,
            $microFiberPrices,
            110
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helper: Seed Product Prices
    |--------------------------------------------------------------------------
    */

    private function seedPrices(
        ProductLine $productLine,
        array $prices,
        int $sizeSortBase
    ): void {
        $index = 0;

        foreach ($prices as $sizeLabel => $price) {
            $size = Size::firstOrCreate(
                [
                    'label' => $sizeLabel,
                ],
                [
                    'sort_order' => $sizeSortBase + $index,
                ]
            );

            $productLine->prices()->create([
                'size_id' => $size->id,
                'price' => $price,
            ]);

            $index++;
        }
    }
}