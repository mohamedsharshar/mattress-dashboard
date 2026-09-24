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
        $brand = Brand::where('name_en', 'Bed Janssen')->first();
        $category = Category::where('slug', 'pillows')->first();

        $priceList = PriceList::create([
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'title' => 'خدادية ومخدات بيد يانسن',
            'effective_date' => '2026-04-02',
        ]);

        // كل صنف هنا = product_line واحد بمقاس وسعر واحد بس
        // الشكل: ['اسم الصنف', 'المقاس', السعر]
        $items = [
            ['خدادية ميموري فوم استندر', '60*40', 850],
            ['خدادية ميموري فوم كونتور', '60*30', 1200],
            ['خدادية ميموري جيل', '60*40', 1500],
            ['خدادية فاير (اوشن)', '70*50', 250],
            ['خدادية مايكرو فاير (هيفن)', '70*50', 750],
            ['مخدة فاير', '100*40', 340],
            ['مخدة فاير', '120*40', 410],
            ['مخدة فاير', '140*40', 480],
            ['مخدة فاير', '150*40', 510],
            ['مخدة فاير', '160*40', 540],
            ['مخدة فاير', '170*40', 580],
            ['مخدة فاير', '180*40', 610],
            ['مخدة فاير', '200*40', 680],
            ['مخدة مايكرو فاير', '100*40', 1030],
            ['مخدة مايكرو فاير', '120*40', 1230],
            ['مخدة مايكرو فاير', '140*40', 1440],
            ['مخدة مايكرو فاير', '150*40', 1540],
            ['مخدة مايكرو فاير', '160*40', 1640],
            ['مخدة مايكرو فاير', '170*40', 1740],
            ['مخدة مايكرو فاير', '180*40', 1840],
            ['مخدة مايكرو فاير', '200*40', 2050],
        ];

        $sortOrder = 1;
        foreach ($items as $item) {
            [$name, $sizeLabel, $price] = $item;

            $productLine = ProductLine::create([
                'price_list_id' => $priceList->id,
                'name' => $name,
                'sort_order' => $sortOrder++,
            ]);

            $size = Size::firstOrCreate(
                ['label' => $sizeLabel],
                ['sort_order' => 100] // مقاسات الاكسسوارات مش زي مقاسات المراتب، بنحطها آخر الترتيب
            );

            $productLine->prices()->create([
                'size_id' => $size->id,
                'price' => $price,
            ]);
        }
    }
}