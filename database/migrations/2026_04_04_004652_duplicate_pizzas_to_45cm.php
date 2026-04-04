<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $cat32 = DB::table('categories')->where('name', 'Pizzák (32cm)')->first();
        $cat45 = DB::table('categories')->where('name', 'Pizzák (45cm)')->first();

        if (!$cat32 || !$cat45) return;

        $products = DB::table('products')->where('category_id', $cat32->id)->get();

        foreach ($products as $product) {
            $newId = DB::table('products')->insertGetId([
                'category_id'  => $cat45->id,
                'name'         => $product->name,
                'description'  => $product->description,
                'image'        => $product->image,
                'price'        => $product->price,
                'sale_price'   => $product->sale_price,
                'sort_order'   => $product->sort_order,
                'is_available' => $product->is_available,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);

            $ingredients = DB::table('ingredient_product')->where('product_id', $product->id)->pluck('ingredient_id');
            foreach ($ingredients as $ingredientId) {
                DB::table('ingredient_product')->insert(['product_id' => $newId, 'ingredient_id' => $ingredientId]);
            }

            $labels = DB::table('label_product')->where('product_id', $product->id)->pluck('label_id');
            foreach ($labels as $labelId) {
                DB::table('label_product')->insert(['product_id' => $newId, 'label_id' => $labelId]);
            }
        }
    }

    public function down(): void
    {
        $cat45 = DB::table('categories')->where('name', 'Pizzák (45cm)')->first();

        if ($cat45) {
            $ids = DB::table('products')->where('category_id', $cat45->id)->pluck('id');
            DB::table('ingredient_product')->whereIn('product_id', $ids)->delete();
            DB::table('label_product')->whereIn('product_id', $ids)->delete();
            DB::table('products')->where('category_id', $cat45->id)->delete();
        }
    }
};
