<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\PriceList;
use App\Models\ProductLine;
use App\Models\Size;
use Illuminate\Database\Seeder;

class BedJanssenFullMattressSeeder extends Seeder
{
    public function run(): void
    {
        $brand = Brand::where('name_en', 'Bed Janssen')->first();
        $category = Category::where('slug', 'mattresses')->first();

        $priceList = PriceList::create([
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'title' => 'مراتب بيد يانسن - القائمة الكاملة',
            'effective_date' => '2026-04-02',
            'round_addition_price' => 750,   // المرتبة الفارمة يضاف 750
            'quarter_addition_price' => 2200, // المرتبة القطر يضاف اليها 2200
        ]);

        // أعمدة الجدول بالترتيب زي ما هي في الفاتورة (اسم الصنف + السمك)
        $columns = [
            'mariout_cotton_22'  => ['name' => 'ماريوت بالقطن', 'thickness_cm' => 22],
            'mariout_cotton_17'  => ['name' => 'ماريوت بالقطن', 'thickness_cm' => 17],
            'mariout_22'         => ['name' => 'ماريوت',        'thickness_cm' => 22],
            'mariout_17'         => ['name' => 'ماريوت',        'thickness_cm' => 17],
            'katrakt_bilotop_29' => ['name' => 'كتراكت بيلوتوب', 'thickness_cm' => 29],
            'katrakt_31'         => ['name' => 'كتراكت',        'thickness_cm' => 31],
            'katrakt_27'         => ['name' => 'كتراكت',        'thickness_cm' => 27],
            'almany_cotton_25'   => ['name' => 'الماني قطن',    'thickness_cm' => 25],
            'sweet_dreams_24'    => ['name' => 'سويت دريمز صيفي وشيتوى', 'thickness_cm' => 24],
            'extra_gold_24'      => ['name' => 'اكسترا جولد',   'thickness_cm' => 24],
        ];

        // سعر المتر للمقاس المخصوص لكل عمود بنفس الترتيب
        $customMeterPrices = [3860, 3150, 3300, 2670, 3850, 3520, 3180, 2820, 2660, 2520];

        // إنشاء الـ product_lines وربطها بسعر المتر المخصوص
        $productLines = [];
        $i = 0;
        foreach ($columns as $key => $col) {
            $productLines[$key] = ProductLine::create([
                'price_list_id' => $priceList->id,
                'name' => $col['name'],
                'thickness_cm' => $col['thickness_cm'],
                'custom_meter_price' => $customMeterPrices[$i],
                'sort_order' => $i + 1,
            ]);
            $i++;
        }

        // صفوف المقاسات: label => [أسعار الأعمدة بنفس ترتيب $columns بالظبط]
        $rows = [
            '90*190/195/2'  => [6300, 5150, 5410, 4350, 6310, 5740, 5200, 4610, 4360, 4130],
            '100*190/195/2' => [7010, 5720, 6000, 4850, 7010, 6380, 5770, 5110, 4840, 4580],
            '120*190/195/2' => [8410, 6870, 7200, 5820, 8410, 7660, 6920, 6140, 5810, 5510],
            '140*190/195/2' => [9810, 8010, 8410, 6780, 9820, 8930, 8080, 7160, 6780, 6420],
            '150*190/195/2' => [10500, 8580, 9000, 7270, 10510, 9580, 8650, 7680, 7260, 6880],
            '160*190/195/2' => [11210, 9150, 9600, 7750, 11220, 10210, 9240, 8180, 7740, 7330],
            '170*190/195/2' => [11900, 9720, 10210, 8230, 11920, 10850, 9820, 8700, 8220, 7800],
            '180*190/195/2' => [12610, 10290, 10800, 8720, 12620, 11480, 10390, 9200, 8710, 8260],
            '200*200'       => [14010, 11450, 12000, 9680, 14020, 12760, 11540, 10240, 9670, 9170],
        ];

        $sortOrder = 1;
        foreach ($rows as $sizeLabel => $prices) {
            $size = Size::firstOrCreate(
                ['label' => $sizeLabel],
                ['sort_order' => $sortOrder++]
            );

            $i = 0;
            foreach ($columns as $key => $col) {
                $productLines[$key]->prices()->create([
                    'size_id' => $size->id,
                    'price' => $prices[$i],
                ]);
                $i++;
            }
        }
    }
}