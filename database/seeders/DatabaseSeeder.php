<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::updateOrCreate(
            [
                'email' => 'test@example.com',
            ],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Legacy Source Data
        |--------------------------------------------------------------------------
        */

        $this->call([
            BrandSeeder::class,
            CategorySeeder::class,

            // Bed Janssen
            BedJanssenKingCottonSeeder::class,
            BedJanssenFullMattressSeeder::class,
            BedJanssenPillowsSeeder::class,
            BedJanssenMiltonBlanketsToppersSeeder::class,

            // Englander
            EnglanderMattressSeeder::class,
            EnglanderAccessoriesSeeder::class,

            // Janssen Prestige
            JanssenPrestigeSeeder::class,

            // Air Bed
            AirBedMattressSeeder::class,
        ]);

        /*
        |--------------------------------------------------------------------------
        | New Inventory Catalog
        |--------------------------------------------------------------------------
        |
        | Converts:
        |
        | ProductLine
        | ProductLinePrice
        |
        | into:
        |
        | Product
        | ProductVariant
        | ProductSku
        | PriceListSkuPrice
        |
        */

        $this->call([
            CatalogSeeder::class,
        ]);
    }
}