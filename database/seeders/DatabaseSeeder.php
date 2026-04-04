<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            LabelSeeder::class,
            IngredientSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            AdminSeeder::class,
            SettingsSeeder::class,
        ]);
    }
}
