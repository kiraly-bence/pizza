<?php

namespace Database\Seeders;

use App\Models\Label;
use Illuminate\Database\Seeder;

class LabelSeeder extends Seeder
{
    public function run(): void
    {
        $labels = [
            ['name' => 'Vegetáriánus',  'type' => 'secondary'],
            ['name' => 'Csípős',        'type' => 'secondary'],
            ['name' => 'Gluténmentes',  'type' => 'secondary'],
            ['name' => '⭐ Új',         'type' => 'primary'],
            ['name' => '🔥 Legjobb',   'type' => 'primary'],
            ['name' => '🍂 Szezonális', 'type' => 'primary'],
        ];

        foreach ($labels as $label) {
            Label::create($label);
        }
    }
}
