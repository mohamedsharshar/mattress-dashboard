<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\PriceList;
use App\Models\ProductLine;
use App\Models\Size;
use Illuminate\Database\Seeder;

class AirBedMattressSeeder extends Seeder
{
    public function run(): void
    {
        $brand = Brand::where('name_en', 'Air Bed')->firstOrFail();

        $category = Category::where(
            'slug',
            'mattresses'
        )->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | Common Sizes
        |--------------------------------------------------------------------------
        */

        $sizes = [
            '100*200',
            '110*200',
            '120*200',
            '130*200',
            '140*200',
            '150*200',
            '160*200',
            '170*200',
            '180*200',
            '190*200',
            '200*200',
        ];

        /*
        |--------------------------------------------------------------------------
        | 1. Air Bed - Medical Mattresses
        |--------------------------------------------------------------------------
        |
        | Price List Date: 28/03/2026
        |
        */

        $medicalList = PriceList::updateOrCreate(
            [
                'brand_id' => $brand->id,
                'category_id' => $category->id,
                'title' => 'اير بد - المراتب الطبية',
            ],
            [
                'effective_date' => '2026-03-28',
                'is_active' => true,
            ]
        );

        /*
         * Prevent duplicated data if the seeder runs again.
         */
        $medicalList->productLines()->delete();

        $medicalProducts = [
            [
                'name' => 'MEDICAL 15 CM',
                'thickness_cm' => 15,
                'description' => null,
                'prices' => [
                    4134,
                    4547,
                    4960,
                    5374,
                    5787,
                    6201,
                    6614,
                    7027,
                    7441,
                    7854,
                    8268,
                ],
            ],

            [
                'name' => 'MEDICAL 20 CM',
                'thickness_cm' => 20,
                'description' => null,
                'prices' => [
                    5200,
                    5720,
                    6240,
                    6760,
                    7280,
                    7800,
                    8320,
                    8840,
                    9360,
                    9880,
                    10400,
                ],
            ],

            [
                'name' => 'MEDICAL 25 CM',
                'thickness_cm' => 25,
                'description' => null,
                'prices' => [
                    5700,
                    6270,
                    6840,
                    7410,
                    7980,
                    8550,
                    9120,
                    9690,
                    10260,
                    10830,
                    11400,
                ],
            ],

            [
                'name' => 'MEDICAL 25 CM 1S',
                'thickness_cm' => 25,
                'description' => '1S',
                'prices' => [
                    5980,
                    6578,
                    7176,
                    7774,
                    8372,
                    8970,
                    9568,
                    10166,
                    10764,
                    11362,
                    11960,
                ],
            ],

            [
                'name' => 'MEDICAL 27 CM 1S',
                'thickness_cm' => 27,
                'description' => '1S',
                'prices' => [
                    6400,
                    7040,
                    7680,
                    8320,
                    8960,
                    9600,
                    10240,
                    10880,
                    11520,
                    12160,
                    12800,
                ],
            ],

            [
                'name' => 'MEDICAL 30 CM 2S',
                'thickness_cm' => 30,
                'description' => '2S',
                'prices' => [
                    7400,
                    8140,
                    8880,
                    9620,
                    10360,
                    11100,
                    11840,
                    12580,
                    13320,
                    14060,
                    14800,
                ],
            ],
        ];

        $this->seedMattressMatrix(
            $medicalList,
            $medicalProducts,
            $sizes
        );

        /*
        |--------------------------------------------------------------------------
        | 2. Air Bed - Springs & Latex Mattresses
        |--------------------------------------------------------------------------
        |
        | Price List Date: 28/03/2026
        |
        */

        $springsList = PriceList::updateOrCreate(
            [
                'brand_id' => $brand->id,
                'category_id' => $category->id,
                'title' => 'اير بد - مراتب السوست واللاتكس',
            ],
            [
                'effective_date' => '2026-03-28',
                'is_active' => true,
            ]
        );

        $springsList->productLines()->delete();

        $springProducts = [
            [
                'name' => 'GOLD 25 CM',
                'thickness_cm' => 25,
                'description' => null,
                'prices' => [
                    3930,
                    4323,
                    4716,
                    5109,
                    5502,
                    5895,
                    6288,
                    6681,
                    7074,
                    7467,
                    7860,
                ],
            ],

            [
                'name' => 'VEDORA 27 CM',
                'thickness_cm' => 27,
                'description' => null,
                'prices' => [
                    4470,
                    4917,
                    5364,
                    5811,
                    6258,
                    6705,
                    7152,
                    7599,
                    8046,
                    8493,
                    8940,
                ],
            ],

            [
                'name' => 'DELUXE 30 CM',
                'thickness_cm' => 30,
                'description' => null,
                'prices' => [
                    5170,
                    5687,
                    6204,
                    6721,
                    7238,
                    7755,
                    8272,
                    8789,
                    9306,
                    9823,
                    10340,
                ],
            ],

            [
                'name' => 'BUTTERFLY 35 CM',
                'thickness_cm' => 35,
                'description' => null,
                'prices' => [
                    6480,
                    7128,
                    7776,
                    8424,
                    9072,
                    9720,
                    10368,
                    11016,
                    11664,
                    12312,
                    12960,
                ],
            ],

            [
                'name' => 'POCKET 27 CM',
                'thickness_cm' => 27,
                'description' => null,
                'prices' => [
                    5360,
                    5896,
                    6432,
                    6968,
                    7504,
                    8040,
                    8576,
                    9112,
                    9648,
                    10184,
                    10720,
                ],
            ],

            [
                'name' => 'POCKET 30 CM',
                'thickness_cm' => 30,
                'description' => null,
                'prices' => [
                    6020,
                    6622,
                    7224,
                    7826,
                    8428,
                    9030,
                    9632,
                    10234,
                    10836,
                    11438,
                    12040,
                ],
            ],
        ];

        $this->seedMattressMatrix(
            $springsList,
            $springProducts,
            $sizes
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helper: Mattress Price Matrix
    |--------------------------------------------------------------------------
    */

    private function seedMattressMatrix(
    PriceList $priceList,
    array $products,
    array $sizes
): void {
    foreach ($products as $productIndex => $product) {
        /*
         * كل موديل لازم يكون له سعر واحد لكل مقاس.
         */
        if (count($product['prices']) !== count($sizes)) {
            throw new \RuntimeException(
                "Invalid price count for {$product['name']}"
            );
        }

        $productLine = ProductLine::create([
            'price_list_id' => $priceList->id,
            'name' => $product['name'],
            'thickness_cm' => $product['thickness_cm'],
            'description' => $product['description'],
            'sort_order' => $productIndex + 1,
            'is_active' => true,
        ]);

        foreach ($sizes as $sizeIndex => $sizeLabel) {
            /*
             * Air Bed sizes are regular width x length values:
             *
             * 100*200
             * 110*200
             * ...
             * 200*200
             *
             * لذلك نقدر نستخرج العرض والطول فعليًا.
             */

            [$width, $length] = array_map(
                'intval',
                explode('*', $sizeLabel)
            );

            /*
             * مهم جدًا:
             *
             * updateOrCreate بدل firstOrCreate
             * لأن بعض المقاسات مثل 200*200
             * موجودة بالفعل من Brands أخرى.
             *
             * وبالتالي نحدث width / length / sort_order
             * بدل الاحتفاظ بقيم قديمة.
             */

            $size = Size::updateOrCreate(
                [
                    'label' => $sizeLabel,
                ],
                [
                    'width_cm' => $width,
                    'length_cm' => $length,
                    'sort_order' => 700 + $sizeIndex,
                ]
            );

            $productLine->prices()->create([
                'size_id' => $size->id,
                'price' => $product['prices'][$sizeIndex],
            ]);
        }
    }
}
}