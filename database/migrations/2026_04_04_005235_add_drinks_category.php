<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $drinks = [
        ['name' => 'Coca-Cola 0.5L',               'image' => 'products/coca-cola.png',          'price' => 590, 'sort_order' => 1],
        ['name' => 'Sprite 0.5L',                  'image' => 'products/sprite.png',              'price' => 590, 'sort_order' => 2],
        ['name' => 'Cappy 0.5L',                   'image' => 'products/cappy.png',               'price' => 590, 'sort_order' => 3],
        ['name' => 'Ice Tea 0.5L',                 'image' => 'products/ice-tea.png',             'price' => 590, 'sort_order' => 4],
        ['name' => 'NaturAqua Szénsavmentes 0.5L', 'image' => 'products/naturaqua-still.png',     'price' => 390, 'sort_order' => 5],
        ['name' => 'NaturAqua Szénsavas 0.5L',     'image' => 'products/naturaqua-sparkling.png', 'price' => 390, 'sort_order' => 6],
    ];

    public function up(): void
    {
        $sortOrder = DB::table('categories')->max('sort_order') + 1;

        $categoryId = DB::table('categories')->insertGetId([
            'name'       => 'Italok',
            'sort_order' => $sortOrder,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach ($this->drinks as $drink) {
            DB::table('products')->insert([
                'category_id'  => $categoryId,
                'name'         => $drink['name'],
                'image'        => $drink['image'],
                'price'        => $drink['price'],
                'sort_order'   => $drink['sort_order'],
                'is_available' => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }
    }

    public function down(): void
    {
        $category = DB::table('categories')->where('name', 'Italok')->first();
        if (!$category) return;

        DB::table('products')->where('category_id', $category->id)->delete();
        DB::table('categories')->where('id', $category->id)->delete();
    }
};
