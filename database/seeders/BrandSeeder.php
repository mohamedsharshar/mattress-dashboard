<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        Brand::create([
            'name' => 'بيد يانسن',
            'name_en' => 'Bed Janssen',
            'phone' => null,
            'email' => 'sales@bedjanssen.com',
            'website' => 'www.bedjanssen.com',
            'tax_number' => '205-114-008',
            'address' => 'مدينة العبور - المنطقة الصناعية الأولى بلوك (13013)',
        ]);

        Brand::create([
            'name' => 'انجلندر',
            'name_en' => 'Englander',
            'phone' => '0244812733',
            'email' => 'sales@englander-eg.com',
            'website' => 'www.englander-eg.com',
            'tax_number' => '367-424-282',
            'address' => 'مدينة العبور - المنطقة الصناعية الأولى بلوك (13016) قطعة رقم (8)',
        ]);

        Brand::create([
            'name' => 'يانسن بريستيج',
            'name_en' => 'Janssen Prestige',
            'phone' => null,
            'email' => 'sales@bedjanssen.com',
            'website' => 'www.bedjanssen.com',
            'tax_number' => '205-114-008',
            'address' => 'مدينة العبور - المنطقة الصناعية الأولى بلوك (13013)',
        ]);
    }
}