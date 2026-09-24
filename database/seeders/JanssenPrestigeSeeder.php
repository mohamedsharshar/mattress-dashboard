<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\PriceList;
use App\Models\ProductLine;
use App\Models\Size;
use Illuminate\Database\Seeder;

class JanssenPrestigeSeeder extends Seeder
{
    public function run(): void
    {
        $brand = Brand::where('name_en', 'Janssen Prestige')->first();
        $category = Category::where('slug', 'mattresses')->first();

        $priceList = PriceList::create([
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'title' => 'يانسن بريستيج - بوكيت قطن',
            'effective_date' => '2026-04-02',
            'round_addition_price' => 750,
            'quarter_addition_price' => 2200,
        ]);

        // تأكدنا بصريًا: "بوكيت قطن" عمودين منفصلين (30 و27) مش عمود واحد
        $columns = [
            ['name' => 'يانسن بيدك', 'thickness_cm' => 25, 'custom_meter_price' => 6575],
            ['name' => 'جوري',        'thickness_cm' => 27, 'custom_meter_price' => 6740],
            ['name' => 'كازاك',       'thickness_cm' => 27, 'custom_meter_price' => 5095],
            ['name' => 'رويالتى',     'thickness_cm' => 35, 'custom_meter_price' => 6540],
            ['name' => 'ميدي بيدك',  'thickness_cm' => 28, 'custom_meter_price' => 5390],
            ['name' => 'بوكيت قطن',  'thickness_cm' => 30, 'custom_meter_price' => 4595],
            ['name' => 'بوكيت قطن',  'thickness_cm' => 27, 'custom_meter_price' => 4180],
            ['name' => 'اسكاندى',    'thickness_cm' => 25, 'custom_meter_price' => 4030],
            ['name' => 'بلوماس',      'thickness_cm' => 20, 'custom_meter_price' => 3250],
            ['name' => 'توت',         'thickness_cm' => 25, 'custom_meter_price' => 2945],
            ['name' => 'كليوبترا',    'thickness_cm' => 24, 'custom_meter_price' => 2600],
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

        $rows = [
            '90*190/195/200'  => [13020,11030,8340,10700,8820,7510,6840,6590,5330,4820,4250],
            '100*190/195/200' => [14460,12250,9260,11890,9800,8350,7600,7330,5910,5350,4730],
            '120*190/195/200' => [17360,14700,11110,14270,11760,10010,9110,8790,7100,6430,5680],
            '140*190/195/200' => [20260,17150,12960,16650,13720,11690,10640,10250,8290,7500,6610],
            '150*190/195/200' => [21710,18380,13890,17840,14710,12530,11400,10980,8880,8040,7090],
            '160*190/195/200' => [23160,19600,14810,19020,15680,13350,12150,11710,9460,8570,7560],
            '170*190/195/200' => [24600,20830,15740,20210,16670,14190,12910,12450,10060,9110,8040],
            '180*190/195/200' => [26050,22050,16660,21400,17650,15030,13680,13180,10650,9650,8510],
            '200*200'         => [28940,24500,18530,23780,19610,16690,15190,14640,11840,10720,9460],
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