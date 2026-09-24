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
        /*
        |--------------------------------------------------------------------------
        | Base Data
        |--------------------------------------------------------------------------
        */

        $brand = Brand::where('name_en', 'Englander')->firstOrFail();

        $pillowsCategory = Category::where('slug', 'pillows')->firstOrFail();
        $blanketsCategory = Category::where('slug', 'blankets')->firstOrFail();
        $toppersCategory = Category::where('slug', 'topper-mattresses')->firstOrFail();
        $miltonCategory = Category::where('slug', 'milton-covers')->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | 1. Pillows & Cushions
        |--------------------------------------------------------------------------
        */

        $pillowsList = PriceList::updateOrCreate(
            [
                'brand_id' => $brand->id,
                'category_id' => $pillowsCategory->id,
                'title' => 'خدادية ومخدات انجلندر',
            ],
            [
                'effective_date' => '2026-04-02',
                'is_active' => true,
            ]
        );

        $pillowsList->productLines()->delete();

        /*
         * المنتجات من 1 إلى 14 في ملف السعر
         * ليس لها مقاس مستقل.
         *
         * لذلك نحفظ المواصفة في description
         * ونستخدم "بدون مقاس" كسجل تقني فقط لكي يناسب
         * الـ schema الحالي الذي يتطلب size_id.
         */

        $noSize = Size::firstOrCreate(
            ['label' => 'بدون مقاس'],
            ['sort_order' => 9999]
        );

        $individualPillows = [
            [
                'name' => 'خدادية ميموري فوم ستاندر',
                'description' => 'مصنعة من مادة الميموري فوم - شكل مستوي',
                'price' => 850,
            ],
            [
                'name' => 'خدادية ميموري فوم كونتور',
                'description' => 'مصنعة من مادة الميموري فوم - شكل الرقبة',
                'price' => 1200,
            ],
            [
                'name' => 'خدادية ميموري فوم فالور',
                'description' => 'مصنعة من مادة الميموري فوم - شكل تجويفة الكتف',
                'price' => 1200,
            ],
            [
                'name' => 'خدادية ميموري جيل',
                'description' => 'مصنعة من مادة الميموري فوم + طبقة جيل',
                'price' => 1500,
            ],
            [
                'name' => 'خدادية لاتكس بيبي',
                'description' => 'مصنعة من مادة المطاط الطبيعي',
                'price' => 950,
            ],
            [
                'name' => 'خدادية لاتكس استاندر',
                'description' => 'مصنعة من مادة المطاط الطبيعي - شكل مستوي',
                'price' => 1700,
            ],
            [
                'name' => 'خدادية لاتكس كونتور',
                'description' => 'مصنعة من مادة المطاط الطبيعي - شكل الرقبة',
                'price' => 1700,
            ],
            [
                'name' => 'خدادية لاتكس فريم',
                'description' => 'مصنعة من مادة المطاط الطبيعي',
                'price' => 1900,
            ],
            [
                'name' => 'خدادية لاتكس جيل',
                'description' => 'مصنعة من مادة المطاط الطبيعي + طبقة جيل',
                'price' => 2100,
            ],
            [
                'name' => 'خدادية ستار',
                'description' => 'مصنعة من الفايبر العادي - هولو',
                'price' => 250,
            ],
            [
                'name' => 'خدادية سوبر ستار',
                'description' => 'مصنعة من الفايبر العادي - هولو - قماش مخصوص',
                'price' => 280,
            ],
            [
                'name' => 'خدادية باراديس',
                'description' => 'مصنعة من الفايبر رول - شرائح',
                'price' => 290,
            ],
            [
                'name' => 'خدادية ليال',
                'description' => 'مصنعة من الفايبر بولز - حبيبات',
                'price' => 320,
            ],
            [
                'name' => 'خدادية ناعومي',
                'description' => 'مصنعة من المايكرو فايبر - بديل الريش',
                'price' => 750,
            ],
        ];

        $sortOrder = 1;

        foreach ($individualPillows as $item) {
            $productLine = ProductLine::create([
                'price_list_id' => $pillowsList->id,
                'name' => $item['name'],
                'description' => $item['description'],
                'sort_order' => $sortOrder++,
                'is_active' => true,
            ]);

            $productLine->prices()->create([
                'size_id' => $noSize->id,
                'price' => $item['price'],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Fiber Pillow
        |--------------------------------------------------------------------------
        */

        $fiberPillow = ProductLine::create([
            'price_list_id' => $pillowsList->id,
            'name' => 'مخدة فايبر',
            'description' => 'مصنعة من الفايبر العادي',
            'sort_order' => $sortOrder++,
            'is_active' => true,
        ]);

        $this->seedPrices(
            $fiberPillow,
            [
                '100*50' => 340,
                '120*50' => 410,
                '140*50' => 480,
                '150*50' => 510,
                '160*50' => 540,
                '170*50' => 580,
                '180*50' => 610,
                '200*50' => 680,
            ],
            300
        );

        /*
        |--------------------------------------------------------------------------
        | Micro Fiber Pillow
        |--------------------------------------------------------------------------
        */

        $microFiberPillow = ProductLine::create([
            'price_list_id' => $pillowsList->id,
            'name' => 'مخدة ميكرو فايبر',
            'description' => 'مصنعة من المايكرو فايبر - بديل الريش',
            'sort_order' => $sortOrder++,
            'is_active' => true,
        ]);

        $this->seedPrices(
            $microFiberPillow,
            [
                '100*50' => 1030,
                '120*50' => 1230,
                '140*50' => 1440,
                '150*50' => 1540,
                '160*50' => 1640,
                '170*50' => 1740,
                '180*50' => 1840,
                '200*50' => 2050,
            ],
            300
        );

        /*
        |--------------------------------------------------------------------------
        | Memory Foam Contour Pillow
        |--------------------------------------------------------------------------
        */

        $memoryContour = ProductLine::create([
            'price_list_id' => $pillowsList->id,
            'name' => 'مخدة ميموري فوم كونتور',
            'description' => 'مصنعة من مادة الميموري فوم - شكل الرقبة',
            'sort_order' => $sortOrder,
            'is_active' => true,
        ]);

        $this->seedPrices(
            $memoryContour,
            [
                '100*50' => 1650,
                '120*50' => 1980,
                '140*50' => 2310,
                '150*50' => 2480,
                '160*50' => 2640,
                '170*50' => 2810,
                '180*50' => 2970,
                '200*50' => 3300,
            ],
            300
        );

        /*
        |--------------------------------------------------------------------------
        | 2. Blankets
        |--------------------------------------------------------------------------
        */

        $blanketsList = PriceList::updateOrCreate(
            [
                'brand_id' => $brand->id,
                'category_id' => $blanketsCategory->id,
                'title' => 'لحف انجلندر',
            ],
            [
                'effective_date' => '2026-04-02',
                'is_active' => true,
            ]
        );

        $blanketsList->productLines()->delete();

        $blankets = [
            [
                'name' => 'لحاف فايبر روول',
                'prices' => [
                    '220*180' => 940,
                    '240*220' => 1260,
                ],
            ],
            [
                'name' => 'لحاف ميكرو فايبر',
                'prices' => [
                    '220*180' => 2050,
                    '240*220' => 2730,
                ],
            ],
        ];

        foreach ($blankets as $index => $blanket) {
            $productLine = ProductLine::create([
                'price_list_id' => $blanketsList->id,
                'name' => $blanket['name'],
                'sort_order' => $index + 1,
                'is_active' => true,
            ]);

            $this->seedPrices(
                $productLine,
                $blanket['prices'],
                400
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 3. Mattress Toppers
        |--------------------------------------------------------------------------
        */

        $toppersList = PriceList::updateOrCreate(
            [
                'brand_id' => $brand->id,
                'category_id' => $toppersCategory->id,
                'title' => 'مراتب تطرية انجلندر',
            ],
            [
                'effective_date' => '2026-04-02',
                'is_active' => true,
            ]
        );

        $toppersList->productLines()->delete();

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

        $toppers = [
            [
                'name' => 'مرتبة تطرية فايبر 800جم',
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
                'name' => 'مرتبة تطرية مايكرو فايبر 800جم',
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
            [
                'name' => 'مرتبة تطرية لاتكس طبيعي',
                'thickness_cm' => 6,
                'prices' => [
                    4900,
                    5440,
                    6530,
                    7620,
                    8160,
                    8710,
                    9250,
                    9800,
                    10880,
                ],
            ],
        ];

        foreach ($toppers as $index => $topper) {
            $productLine = ProductLine::create([
                'price_list_id' => $toppersList->id,
                'name' => $topper['name'],
                'thickness_cm' => $topper['thickness_cm'],
                'sort_order' => $index + 1,
                'is_active' => true,
            ]);

            foreach ($topperSizes as $sizeIndex => $sizeLabel) {
                $size = Size::firstOrCreate(
                    ['label' => $sizeLabel],
                    ['sort_order' => 500 + $sizeIndex]
                );

                $productLine->prices()->create([
                    'size_id' => $size->id,
                    'price' => $topper['prices'][$sizeIndex],
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 4. Milton Covers
        |--------------------------------------------------------------------------
        */

        $miltonList = PriceList::updateOrCreate(
            [
                'brand_id' => $brand->id,
                'category_id' => $miltonCategory->id,
                'title' => 'ميلتون انجلندر',
            ],
            [
                'effective_date' => '2026-04-02',
                'is_active' => true,
            ]
        );

        $miltonList->productLines()->delete();

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

        $miltonProducts = [
            [
                'name' => 'ميلتون فايبر (اربع استك من الجوانب)',
                'custom_meter_price' => 180,
                'prices' => [
                    290,
                    330,
                    390,
                    460,
                    490,
                    520,
                    550,
                    590,
                    650,
                ],
            ],
            [
                'name' => 'ميلتون فايبر استك داير (جوانب قماش)',
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

        foreach ($miltonProducts as $index => $milton) {
            $productLine = ProductLine::create([
                'price_list_id' => $miltonList->id,
                'name' => $milton['name'],
                'custom_meter_price' => $milton['custom_meter_price'],
                'sort_order' => $index + 1,
                'is_active' => true,
            ]);

            foreach ($miltonSizes as $sizeIndex => $sizeLabel) {
                $size = Size::firstOrCreate(
                    ['label' => $sizeLabel],
                    ['sort_order' => 600 + $sizeIndex]
                );

                $productLine->prices()->create([
                    'size_id' => $size->id,
                    'price' => $milton['prices'][$sizeIndex],
                ]);
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Helper
    |--------------------------------------------------------------------------
    */

    private function seedPrices(
        ProductLine $productLine,
        array $prices,
        int $sortBase
    ): void {
        $index = 0;

        foreach ($prices as $sizeLabel => $price) {
            $size = Size::firstOrCreate(
                ['label' => $sizeLabel],
                ['sort_order' => $sortBase + $index]
            );

            $productLine->prices()->create([
                'size_id' => $size->id,
                'price' => $price,
            ]);

            $index++;
        }
    }
}