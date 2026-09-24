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
        $brand = Brand::where('name_en', 'Bed Janssen')->first();
        $miltonCategory = Category::where('slug', 'milton-covers')->first();
        $blanketCategory = Category::where('slug', 'blankets')->first();
        $topperCategory = Category::where('slug', 'topper-mattresses')->first();

        // ============ 1) قائمة الميلتون ============
        $miltonList = PriceList::create([
            'brand_id' => $brand->id,
            'category_id' => $miltonCategory->id,
            'title' => 'ميلتون بيد يانسن',
            'effective_date' => '2026-04-02',
        ]);

        $miltonSizes = ['90', '100', '120', '140', '150', '160', '170', '180', '200'];

        $miltonColumns = [
            'arbaa_asatk' => [
                'name' => 'ميلتون فاير (اربع اساتك من الجوانب)',
                'custom_meter_price' => 220,
                'prices' => [360, 400, 480, 560, 600, 640, 680, 720, 800],
            ],
            'bshkir' => [
                'name' => 'ميلتون بشكير',
                'custom_meter_price' => 260,
                'prices' => [430, 470, 560, 660, 700, 750, 800, 840, 940],
            ],
        ];

        $this->seedMatrixColumns($miltonList, $miltonColumns, $miltonSizes, 200);

        // ============ 2) قائمة اللحف ============
        $blanketList = PriceList::create([
            'brand_id' => $brand->id,
            'category_id' => $blanketCategory->id,
            'title' => 'لحف بيد يانسن',
            'effective_date' => '2026-04-02',
        ]);

        $blankets = [
            ['لحاف فاير', '220*180', 940],
            ['لحاف فاير', '240*220', 1260],
            ['لحاف ميكرو فاير', '220*180', 2050],
            ['لحاف ميكرو فاير', '240*220', 2730],
        ];

        $sortOrder = 1;
        foreach ($blankets as [$name, $sizeLabel, $price]) {
            $productLine = ProductLine::create([
                'price_list_id' => $blanketList->id,
                'name' => $name,
                'sort_order' => $sortOrder++,
            ]);

            $size = Size::firstOrCreate(
                ['label' => $sizeLabel],
                ['sort_order' => 200]
            );

            $productLine->prices()->create([
                'size_id' => $size->id,
                'price' => $price,
            ]);
        }

        // ============ 3) قائمة المراتب التطرية (Topper) ============
        $topperList = PriceList::create([
            'brand_id' => $brand->id,
            'category_id' => $topperCategory->id,
            'title' => 'مراتب تطرية بيد يانسن - ارتفاع 5 سم',
            'effective_date' => '2026-04-02',
        ]);

        $topperSizes = ['90', '100', '120', '140', '150', '160', '170', '180', '200'];

        $topperColumns = [
            'fiber_800' => [
                'name' => 'تطرية فاير 800جم',
                'prices' => [1170, 1290, 1550, 1810, 1940, 2070, 2200, 2330, 2590],
            ],
            'microfiber_800' => [
                'name' => 'تطرية مايكرو فاير 800جم',
                'prices' => [2650, 2940, 3530, 4120, 4420, 4700, 5000, 5290, 5880],
            ],
            'memory_foam' => [
                'name' => 'تطرية ميموري فوم',
                'prices' => [4390, 4880, 5860, 6840, 7320, 7810, 8300, 8790, 9770],
            ],
        ];

        $this->seedMatrixColumns($topperList, $topperColumns, $topperSizes, 200);
    }

    /**
     * دالة مساعدة عشان منكررش نفس الكود في كل قائمة matrix
     */
    private function seedMatrixColumns(PriceList $priceList, array $columns, array $sizeLabels, int $sizeSortBase): void
    {
        $productLines = [];
        $i = 0;
        foreach ($columns as $key => $col) {
            $productLines[$key] = ProductLine::create([
                'price_list_id' => $priceList->id,
                'name' => $col['name'],
                'custom_meter_price' => $col['custom_meter_price'] ?? null,
                'sort_order' => $i + 1,
            ]);
            $i++;
        }

        foreach ($sizeLabels as $rowIndex => $sizeLabel) {
            $size = Size::firstOrCreate(
                ['label' => $sizeLabel],
                ['sort_order' => $sizeSortBase + $rowIndex]
            );

            $i = 0;
            foreach ($columns as $key => $col) {
                $productLines[$key]->prices()->create([
                    'size_id' => $size->id,
                    'price' => $col['prices'][$i],
                ]);
                $i++;
            }
        }
    }
}