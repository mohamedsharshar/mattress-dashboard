<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\PriceList;
use App\Models\ProductLine;
use App\Models\Size;
use Illuminate\Database\Seeder;

class BedJanssenKingCottonSeeder extends Seeder
{
    public function run(): void
    {
        $brand = Brand::where('name_en', 'Bed Janssen')->first();
        $category = Category::where('slug', 'mattresses')->first();

        // إنشاء قائمة السعر
        $priceList = PriceList::create([
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'title' => 'المرتبة الكينج سوست و قطن',
            'effective_date' => '2026-04-02',
        ]);

        // إنشاء الصنف (موديل واحد بس في الجدول ده)
        $productLine = ProductLine::create([
            'price_list_id' => $priceList->id,
            'name' => 'المرتبة الكينج سوست و قطن',
            'sort_order' => 1,
        ]);

        // بيانات المقاسات والأسعار زي ما هي بالفاتورة بالظبط
        $rows = [
            ['label' => '100*190/195/200', 'price' => 3990],
            ['label' => '120*190/195/200', 'price' => 4790],
        ];

        foreach ($rows as $index => $row) {
            // نستخدم firstOrCreate عشان لو المقاس ده اتعمل قبل كده من قائمة تانية منكررهوش
            $size = Size::firstOrCreate(
                ['label' => $row['label']],
                ['sort_order' => $index + 1]
            );

            $productLine->prices()->create([
                'size_id' => $size->id,
                'price' => $row['price'],
            ]);
        }
    }
}