<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $category = DB::table('categories')->where('name', 'Pizzák (45cm)')->first();

        if ($category) {
            DB::table('order_items')
                ->whereIn('product_id', DB::table('products')->where('category_id', $category->id)->pluck('id'))
                ->update(['product_id' => null]);

            DB::table('products')->where('category_id', $category->id)->delete();
            DB::table('categories')->where('id', $category->id)->delete();
        }
    }

    public function down(): void
    {
        // Intentionally irreversible — re-run the seeder to restore 45cm pizzas.
    }
};
