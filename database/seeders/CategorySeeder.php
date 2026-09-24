<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'مراتب', 'slug' => 'mattresses', 'sort_order' => 1],
            ['name' => 'مخدات وخدادية', 'slug' => 'pillows', 'sort_order' => 2],
            ['name' => 'لحف', 'slug' => 'blankets', 'sort_order' => 3],
            ['name' => 'ميلتون', 'slug' => 'milton-covers', 'sort_order' => 4],
            ['name' => 'مراتب تطرية', 'slug' => 'topper-mattresses', 'sort_order' => 5],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}