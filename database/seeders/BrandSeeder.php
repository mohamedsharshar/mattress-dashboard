<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Bed Janssen
        |--------------------------------------------------------------------------
        */

        Brand::updateOrCreate(
            [
                'name_en' => 'Bed Janssen',
            ],
            [
                'name' => 'بيد يانسن',
                'logo_path' => null,
                'phone' => null,
                'email' => 'sales@bedjanssen.com',
                'website' => 'www.bedjanssen.com',
                'tax_number' => '205-114-008',
                'address' => 'مدينة العبور - المنطقة الصناعية الأولى بلوك (13013)',
                'is_active' => true,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Englander
        |--------------------------------------------------------------------------
        */

        Brand::updateOrCreate(
            [
                'name_en' => 'Englander',
            ],
            [
                'name' => 'انجلندر',
                'logo_path' => null,
                'phone' => '0244812733',
                'email' => 'sales@englander-eg.com',
                'website' => 'www.englander-eg.com',
                'tax_number' => '367-424-282',
                'address' => 'مدينة العبور - المنطقة الصناعية الأولى بلوك (13016) قطعة رقم (8)',
                'is_active' => true,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Janssen Prestige
        |--------------------------------------------------------------------------
        */

        Brand::updateOrCreate(
            [
                'name_en' => 'Janssen Prestige',
            ],
            [
                'name' => 'يانسن بريستيج',
                'logo_path' => null,
                'phone' => null,
                'email' => 'sales@bedjanssen.com',
                'website' => 'www.bedjanssen.com',
                'tax_number' => '205-114-008',
                'address' => 'مدينة العبور - المنطقة الصناعية الأولى بلوك (13013)',
                'is_active' => true,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Air Bed
        |--------------------------------------------------------------------------
        |
        | البيانات المتوفرة حاليًا من قوائم الأسعار المرسلة من العميل:
        |
        | Mobile:   010 6569 8029
        | Landline: 048 340 7104
        |
        | باقي البيانات غير موجودة في الملفات الحالية،
        | لذلك لا نقوم بافتراضها.
        |
        */

        Brand::updateOrCreate(
            [
                'name_en' => 'Air Bed',
            ],
            [
                'name' => 'اير بد',
                'logo_path' => null,

                /*
                 * الـ schema الحالي يحتوي phone واحد فقط،
                 * لذلك نخزن الرقمين مؤقتًا في نفس الحقل.
                 *
                 * لاحقًا في Phase 1 هنفصل بيانات الاتصال
                 * بشكل أفضل لو احتجنا.
                 */
                'phone' => '01065698029 / 0483407104',

                'email' => null,
                'website' => null,
                'tax_number' => null,
                'address' => null,

                'is_active' => true,
            ]
        );
    }
}