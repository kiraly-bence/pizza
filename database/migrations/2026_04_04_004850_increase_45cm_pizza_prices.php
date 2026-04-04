<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    const MARKUP = 1000;

    public function up(): void
    {
        $cat45 = DB::table('categories')->where('name', 'Pizzák (45cm)')->first();
        if (!$cat45) return;

        DB::table('products')
            ->where('category_id', $cat45->id)
            ->increment('price', self::MARKUP);
    }

    public function down(): void
    {
        $cat45 = DB::table('categories')->where('name', 'Pizzák (45cm)')->first();
        if (!$cat45) return;

        DB::table('products')
            ->where('category_id', $cat45->id)
            ->decrement('price', self::MARKUP);
    }
};
