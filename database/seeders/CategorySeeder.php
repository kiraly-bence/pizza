<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Pizzák (32cm)', 'sort_order' => 1],
            ['name' => 'Pizzák (45cm)', 'sort_order' => 2],
            ['name' => 'Hamburgerek',   'sort_order' => 3],
            ['name' => 'Köretek',       'sort_order' => 4],
            ['name' => 'Szószok',       'sort_order' => 5],
            ['name' => 'Saláták',       'sort_order' => 6],
            ['name' => 'Italok',        'sort_order' => 7],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
