<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\PriceList;
use App\Models\ProductLine;
use App\Models\Size;
use Illuminate\Database\Seeder;

class EnglanderMattressSeeder extends Seeder
{
    public function run(): void
    {
        $brand = Brand::where('name_en', 'Englander')->first();
        $category = Category::where('slug', 'mattresses')->first();

        $priceList = PriceList::create([
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'title' => 'مراتب انجلندر - القائمة الكاملة',
            'effective_date' => '2026-03-31',
            'round_addition_price' => 850,
            'quarter_addition_price' => 2350,
        ]);

        // الترتيب اتأكد بصريًا من الـ PDF الأصلي (18 عمود بالظبط)
        $columns = [
            ['name' => 'بريليانت',          'thickness_cm' => 38, 'custom_meter_price' => 12470],
            ['name' => 'لولا',               'thickness_cm' => 34, 'custom_meter_price' => 10710],
            ['name' => 'اميريكان سبيريت',   'thickness_cm' => 34, 'custom_meter_price' => 7596],
            ['name' => 'سيتي انجلندر',      'thickness_cm' => 25, 'custom_meter_price' => 4345],
            ['name' => 'سيتي انجلندر',      'thickness_cm' => 20, 'custom_meter_price' => 3650],
            ['name' => 'سيتي انجلندر',      'thickness_cm' => 15, 'custom_meter_price' => 3100],
            ['name' => 'هني مون',            'thickness_cm' => 27, 'custom_meter_price' => 9585],
            ['name' => 'فيسكوبيدك',         'thickness_cm' => 27, 'custom_meter_price' => 6535],
            ['name' => 'دريمز',              'thickness_cm' => 26, 'custom_meter_price' => 5125],
            ['name' => 'فيكتوريا',           'thickness_cm' => 25, 'custom_meter_price' => 4545],
            ['name' => 'مارفي',              'thickness_cm' => 20, 'custom_meter_price' => 3940],
            ['name' => 'كارس بيلوتوب',      'thickness_cm' => 29, 'custom_meter_price' => 4075],
            ['name' => 'كارس',               'thickness_cm' => 27, 'custom_meter_price' => 3465],
            ['name' => 'ليدى',               'thickness_cm' => 25, 'custom_meter_price' => 3150],
            ['name' => 'سيزونال اكسترا',    'thickness_cm' => 30, 'custom_meter_price' => 3515],
            ['name' => 'سيزونال',            'thickness_cm' => 25, 'custom_meter_price' => 2910],
            ['name' => 'سوبر كلاسيك',       'thickness_cm' => 28, 'custom_meter_price' => 3210],
            ['name' => 'كلاسيك',             'thickness_cm' => 24, 'custom_meter_price' => 2630],
        ];

        $productLines = [];
        foreach ($columns as $i => $col) {
            $productLines[] = ProductLine::create([
                'price_list_id' => $priceList->id,
                'name' => $col['name'],
                'thickness_cm' => $col['thickness_cm'],
                'custom_meter_price' => $col['custom_meter_price'],
                'sort_order' => $i + 1,
            ]);
        }

        // 18 قيمة لكل صف، بنفس ترتيب الأعمدة فوق بالظبط
        $rows = [
            '90*190/195/2'  => [20410,17520,12430,7100,5980,5060,15690,10690,8390,7440,6450,6660,5670,5150,5750,4760,5260,4300],
            '100*190/195/2' => [22670,19470,13810,7900,6640,5630,17430,11880,9320,8260,7160,7410,6300,5730,6390,5290,5840,4780],
            '120*190/195/2' => [27210,23360,16580,9480,7960,6760,20920,14260,11180,9910,8610,8880,7570,6870,7670,6350,7020,5740],
            '140*190/195/2' => [31740,27260,19330,11050,9290,7880,24400,16630,13050,11560,10040,10360,8830,8020,8960,7410,8180,6700],
            '150*190/195/2' => [34020,29210,20710,11840,9950,8450,26150,17820,13980,12390,10750,11110,9450,8590,9590,7940,8760,7180],
            '160*190/195/2' => [36280,31150,22100,12640,10620,9010,27890,19000,14910,13210,11470,11850,10090,9170,10240,8460,9350,7660],
            '170*190/195/2' => [38550,33100,23480,13430,11280,9580,29630,20190,15850,14040,12180,12580,10720,9730,10870,8990,9940,8130],
            '180*190/195/2' => [40810,35040,24870,14220,11940,10140,31370,21390,16780,14870,12900,13330,11350,10300,11510,9520,10510,8610],
            '200*200'       => [45350,38950,27620,15790,13270,11260,34850,23760,18640,16520,14340,14800,12620,11450,12790,10580,11680,9570],
        ];

        $sortOrder = 1;
        foreach ($rows as $sizeLabel => $prices) {
            $size = Size::firstOrCreate(
                ['label' => $sizeLabel],
                ['sort_order' => $sortOrder++]
            );

            foreach ($productLines as $i => $productLine) {
                $productLine->prices()->create([
                    'size_id' => $size->id,
                    'price' => $prices[$i],
                ]);
            }
        }
    }
}