<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Rename the existing "Pizzák" category to "Pizzák (32cm)"
        DB::table('categories')
            ->where('name', 'Pizzák')
            ->update(['name' => 'Pizzák (32cm)']);

        // Insert the new "Pizzák (45cm)" category after it
        $existing = DB::table('categories')->where('name', 'Pizzák (32cm)')->first();

        if ($existing) {
            // Shift all categories with sort_order > existing down by 1 to make room
            DB::table('categories')
                ->where('sort_order', '>', $existing->sort_order)
                ->increment('sort_order');

            DB::table('categories')->insert([
                'name'       => 'Pizzák (45cm)',
                'sort_order' => $existing->sort_order + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('categories')->where('name', 'Pizzák (45cm)')->delete();
        DB::table('categories')->where('name', 'Pizzák (32cm)')->update(['name' => 'Pizzák']);

        // Restore sort_orders (shift back down)
        DB::table('categories')
            ->where('sort_order', '>', 1)
            ->decrement('sort_order');
    }
};
