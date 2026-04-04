<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use Illuminate\Database\Seeder;

class IngredientSeeder extends Seeder
{
    public function run(): void
    {
        $ingredients = [
            'paradicsom alap', 'tejszínes alap', 'BBQ alap',
            'mozzarella', 'gorgonzola', 'parmezán', 'pecorino', 'cheddar',
            'szalámi', 'sonka', 'csirkemell', 'tonhal', 'bacon', 'darált marha',
            'gomba', 'jalapeño', 'lilahagyma', 'vöröshagyma', 'paprika', 'kukorica',
            'kapribogyó', 'olívabogyó', 'bazsalikom', 'rukkola', 'paradicsom',
            'uborka', 'salátalevél', 'ketchup', 'majonéz', 'mustár',
            'burgonya', 'fokhagyma', 'tejföl', 'tojás',
        ];

        foreach ($ingredients as $name) {
            Ingredient::create(['name' => $name]);
        }
    }
}
