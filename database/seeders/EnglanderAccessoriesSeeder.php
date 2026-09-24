<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\PriceList;
use App\Models\ProductLine;
use App\Models\Size;
use Illuminate\Database\Seeder;

class EnglanderAccessoriesSeeder extends Seeder
{
    public function run(): void
    {
        $brand = Brand::where('name_en', 'Englander')->first();
        $pillowsCategory = Category::where('slug', 'pillows')->first();
        $miltonCategory = Category::where('slug', 'milton-covers')->first();
        $blanketCategory = Category::where('slug', 'blankets')->first();
        $topperCategory = Category::where('slug', 'topper-mattresses')->first();

        // ============ 1) خدادية ومخدات ============
        $pillowsList = PriceList::create([
            'brand_id' => $brand->id,
            'category_id' => $pillowsCategory->id,
            'title' => 'خدادية ومخدات انجلندر',
            'effective_date' => '2026-04-02',
        ]);

        $pillowItems = [
            ['خدادية ميموري فوم ستاندر', 'مستوي', 850],
            ['خدادية ميموري فوم كونتور', 'شكل الرقبة', 1200],
            ['خدادية ميموري فوم فالور', 'شكل تجويفة الكتف', 1200],
            ['خدادية ميموري جيل', 'ميموري + جيل', 1500],
            ['خدادية لاتكس بيبي', 'مطاط طبيعي', 950],
            ['خدادية لاتكس استاندر', 'مستوي', 1700],
            ['خدادية لاتكس كونتور', 'شكل الرقبة', 1700],
            ['خدادية لاتكس فريم', 'مطاط طبيعي', 1900],
            ['خدادية لاتكس جيل', 'لاتكس + جيل', 2100],
            ['خدادية ستار', 'فايبر هولو', 250],
            ['خدادية سوبر ستار', 'فايبر هولو قماش مخصوص', 280],
            ['خدادية باراديس', 'فايبر رول شرائح', 290],
            ['خدادية ليال', 'فايبر بولز حبيبات', 320],
            ['خدادية ناعومي', 'مايكرو فايبر بديل الريش', 750],
            ['مخدة فايبر', '100*50', 340],
            ['مخدة فايبر', '120*50', 410],
            ['مخدة فايبر', '140*50', 480],
            ['مخدة فايبر', '150*50', 510],
            ['مخدة فايبر', '160*50', 540],
            ['مخدة فايبر', '170*50', 580],
            ['مخدة فايبر', '180*50', 610],
            ['مخدة فايبر', '200*50', 680],
            ['مخدة ميكرو فايبر', '100*50', 1030],
            ['مخدة ميكرو فايبر', '120*50', 1230],
            ['مخدة ميكرو فايبر', '140*50', 1440],
            ['مخدة ميكرو فايبر', '150*50', 1540],
            ['مخدة ميكرو فايبر', '160*50', 1640],
            ['مخدة ميكرو فايبر', '170*50', 1740],
            ['مخدة ميكرو فايبر', '180*50', 1840],
            ['مخدة ميكرو فايبر', '200*50', 2050],
            ['مخدة ميموري فوم كونتور', '100*50', 1650],
            ['مخدة ميموري فوم كونتور', '120*50', 1980],
            ['مخدة ميموري فوم كونتور', '140*50', 2310],
            ['مخدة ميموري فوم كونتور', '150*50', 2480],
            ['مخدة ميموري فوم كونتور', '160*50', 2640],
            ['مخدة ميموري فوم كونتور', '170*50', 2810],
            ['مخدة ميموري فوم كونتور', '180*50', 2970],
            ['مخدة ميموري فوم كونتور', '200*50', 3300],
        ];

        $this->seedFlatItems($pillowsList, $pillowItems);

        // ============ 2) لحف + مراتب تطرية ============
        $blanketList = PriceList::create([
            'brand_id' => $brand->id,
            'category_id' => $blanketCategory->id,
            'title' => 'لحف انجلندر',
            'effective_date' => '2026-04-02',
        ]);

        $blanketItems = [
            ['لحاف فايبر روول', '220*180', 940],
            ['لحاف فايبر روول', '240*220', 1260],
            ['لحاف ميكرو فايبر', '220*180', 2050],
            ['لحاف ميكرو فايبر', '240*220', 2730],
        ];

        $this->seedFlatItems($blanketList, $blanketItems);

        $topperList = PriceList::create([
            'brand_id' => $brand->id,
            'category_id' => $topperCategory->id,
            'title' => 'مراتب تطرية انجلندر',
            'effective_date' => '2026-04-02',
        ]);

        $topperSizes = ['90', '100', '120', '140', '150', '160', '170', '180', '200'];

        $topperColumns = [
            'fiber_800' => [
                'name' => 'تطرية فايبر800جم ارتفاع 5سم',
                'prices' => [1170, 1290, 1550, 1810, 1940, 2070, 2200, 2330, 2590],
            ],
            'microfiber_800' => [
                'name' => 'تطرية مايكروفايبر800جم ارتفاع 5سم',
                'prices' => [2650, 2940, 3530, 4120, 4420, 4700, 5000, 5290, 5880],
            ],
            'memory_foam' => [
                'name' => 'تطرية ميموري فوم ارتفاع 5سم',
                'prices' => [4390, 4880, 5860, 6840, 7320, 7810, 8300, 8790, 9770],
            ],
            'latex' => [
                'name' => 'تطرية لاتكس طبيعي ارتفاع 6سم',
                'prices' => [4900, 5440, 6530, 7620, 8160, 8710, 9250, 9800, 10880],
            ],
        ];

        $this->seedMatrixColumns($topperList, $topperColumns, $topperSizes, 200);

        // ============ 3) ميلتون ============
        $miltonList = PriceList::create([
            'brand_id' => $brand->id,
            'category_id' => $miltonCategory->id,
            'title' => 'ميلتون انجلندر',
            'effective_date' => '2026-04-02',
        ]);

        $miltonSizes = ['90', '100', '120', '140', '150', '160', '170', '180', '200'];

        $miltonColumns = [
            'arbaa_asatk' => [
                'name' => 'ميلتون فايبر (اربع اساتك من الجوانب)',
                'custom_meter_price' => 180,
                'prices' => [290, 330, 390, 460, 490, 520, 550, 590, 650],
            ],
            'astk_dayer' => [
                'name' => 'ميلتون فايبر استك داير (جوانب قماش)',
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
    }

    private function seedFlatItems(PriceList $priceList, array $items): void
    {
        $sortOrder = 1;
        foreach ($items as [$name, $sizeLabel, $price]) {
            $productLine = ProductLine::create([
                'price_list_id' => $priceList->id,
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
    }

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