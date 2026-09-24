<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\PriceList;
use App\Models\ProductLine;
use App\Models\Size;
use Illuminate\Database\Seeder;

class BedJanssenMiltonBlanketsToppersSeeder extends Seeder
{
    public function run(): void
    {
        $brand = Brand::where('name_en', 'Bed Janssen')->firstOrFail();

        $miltonCategory = Category::where('slug', 'milton-covers')->firstOrFail();
        $blanketCategory = Category::where('slug', 'blankets')->firstOrFail();
        $topperCategory = Category::where('slug', 'topper-mattresses')->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | Milton
        |--------------------------------------------------------------------------
        */

        $miltonList = $this->resetPriceList(
            brandId: $brand->id,
            categoryId: $miltonCategory->id,
            title: 'ميلتون بيد يانسن',
        );

        $miltonSizes = [
            '90',
            '100',
            '120',
            '140',
            '150',
            '160',
            '170',
            '180',
            '200',
        ];

        $miltonColumns = [
            [
                'name' => 'ميلتون فاير (اربع استك من الجوانب)',
                'custom_meter_price' => 220,
                'prices' => [
                    360,
                    400,
                    480,
                    560,
                    600,
                    640,
                    680,
                    720,
                    800,
                ],
            ],
            [
                'name' => 'ميلتون بشكير',
                'custom_meter_price' => 260,
                'prices' => [
                    430,
                    470,
                    560,
                    660,
                    700,
                    750,
                    800,
                    840,
                    940,
                ],
            ],
        ];

        $this->seedMatrix(
            $miltonList,
            $miltonColumns,
            $miltonSizes,
            200
        );

        /*
        |--------------------------------------------------------------------------
        | Blankets
        |--------------------------------------------------------------------------
        */

        $blanketList = $this->resetPriceList(
            brandId: $brand->id,
            categoryId: $blanketCategory->id,
            title: 'لحف بيد يانسن',
        );

        $blankets = [
            [
                'name' => 'لحاف فاير',
                'sizes' => [
                    '220*180' => 940,
                    '240*220' => 1260,
                ],
            ],
            [
                'name' => 'لحاف ميكرو فاير',
                'sizes' => [
                    '220*180' => 2050,
                    '240*220' => 2730,
                ],
            ],
        ];

        foreach ($blankets as $index => $blanket) {
            $productLine = ProductLine::create([
                'price_list_id' => $blanketList->id,
                'name' => $blanket['name'],
                'sort_order' => $index + 1,
            ]);

            foreach ($blanket['sizes'] as $sizeLabel => $price) {
                $size = Size::firstOrCreate(
                    ['label' => $sizeLabel],
                    ['sort_order' => 200]
                );

                $productLine->prices()->create([
                    'size_id' => $size->id,
                    'price' => $price,
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Toppers
        |--------------------------------------------------------------------------
        */

        $topperList = $this->resetPriceList(
            brandId: $brand->id,
            categoryId: $topperCategory->id,
            title: 'مراتب تطرية بيد يانسن - ارتفاع 5 سم',
        );

        $topperSizes = [
            '90',
            '100',
            '120',
            '140',
            '150',
            '160',
            '170',
            '180',
            '200',
        ];

        $topperColumns = [
            [
                'name' => 'مرتبة تطرية فاير 800جم',
                'thickness_cm' => 5,
                'prices' => [
                    1170,
                    1290,
                    1550,
                    1810,
                    1940,
                    2070,
                    2200,
                    2330,
                    2590,
                ],
            ],
            [
                'name' => 'مرتبة تطرية مايكرو فاير 800جم',
                'thickness_cm' => 5,
                'prices' => [
                    2650,
                    2940,
                    3530,
                    4120,
                    4420,
                    4700,
                    5000,
                    5290,
                    5880,
                ],
            ],
            [
                'name' => 'مرتبة تطرية ميموري فوم',
                'thickness_cm' => 5,
                'prices' => [
                    4390,
                    4880,
                    5860,
                    6840,
                    7320,
                    7810,
                    8300,
                    8790,
                    9770,
                ],
            ],
        ];

        $this->seedMatrix(
            $topperList,
            $topperColumns,
            $topperSizes,
            300
        );
    }

    private function resetPriceList(
        int $brandId,
        int $categoryId,
        string $title
    ): PriceList {
        $priceList = PriceList::updateOrCreate(
            [
                'brand_id' => $brandId,
                'category_id' => $categoryId,
                'title' => $title,
            ],
            [
                'effective_date' => '2026-04-02',
                'is_active' => true,
            ]
        );

        $priceList->productLines()->delete();

        return $priceList;
    }

    private function seedMatrix(
        PriceList $priceList,
        array $columns,
        array $sizeLabels,
        int $sizeSortBase
    ): void {
        $productLines = [];

        foreach ($columns as $index => $column) {
            $productLines[$index] = ProductLine::create([
                'price_list_id' => $priceList->id,
                'name' => $column['name'],
                'thickness_cm' => $column['thickness_cm'] ?? null,
                'custom_meter_price' => $column['custom_meter_price'] ?? null,
                'sort_order' => $index + 1,
            ]);
        }

        foreach ($sizeLabels as $sizeIndex => $sizeLabel) {
            $size = Size::firstOrCreate(
                ['label' => $sizeLabel],
                ['sort_order' => $sizeSortBase + $sizeIndex]
            );

            foreach ($columns as $columnIndex => $column) {
                $productLines[$columnIndex]
                    ->prices()
                    ->create([
                        'size_id' => $size->id,
                        'price' => $column['prices'][$sizeIndex],
                    ]);
            }
        }
    }
}