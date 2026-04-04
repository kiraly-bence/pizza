<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Label;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $this->copyImages();

        $cat = fn (string $name) => Category::where('name', $name)->firstOrFail();
        $ing = fn (string $name) => Ingredient::where('name', $name)->firstOrFail()->id;
        $label = fn (string $name) => Label::where('name', $name)->firstOrFail()->id;

        // ── Pizzák (32cm) ────────────────────────────────────────────────────
        $pizza32 = [
            [
                'name' => 'Margherita',
                'description' => 'Klasszikus olasz pizza paradicsomszósszal, friss mozzarellával és bazsalikommal.',
                'image' => 'products/margherita.jpg',
                'price' => 2490,
                'sort_order' => 1,
                'ingredients' => ['paradicsom alap', 'mozzarella', 'bazsalikom'],
                'labels' => ['Vegetáriánus'],
            ],
            [
                'name' => 'Diavola',
                'description' => 'Csípős szalámi, jalapeño paprika és füstölt mozzarella az igazi pikáns élményért.',
                'image' => 'products/diavola.jpg',
                'price' => 2890,
                'sort_order' => 2,
                'ingredients' => ['paradicsom alap', 'mozzarella', 'szalámi', 'jalapeño'],
                'labels' => ['🔥 Legjobb', 'Csípős'],
            ],
            [
                'name' => 'Quattro Formaggi',
                'description' => 'Négy sajt harmonikus keveréke: mozzarella, gorgonzola, parmezán és pecorino.',
                'image' => 'products/quattro-formaggi.jpg',
                'price' => 3190,
                'sort_order' => 3,
                'ingredients' => ['paradicsom alap', 'mozzarella', 'gorgonzola', 'parmezán', 'pecorino'],
                'labels' => ['Vegetáriánus'],
            ],
            [
                'name' => 'Prosciutto e Funghi',
                'description' => 'Pármai sonka és friss erdei gombák, tejszínes alapon tálalva.',
                'image' => 'products/prosciutto-e-funghi.jpg',
                'price' => 3090,
                'sort_order' => 4,
                'ingredients' => ['tejszínes alap', 'mozzarella', 'sonka', 'gomba'],
                'labels' => [],
            ],
            [
                'name' => 'BBQ Csirke',
                'description' => 'Grillezett csirkemell, füstölt BBQ szósz, lilahagyma és mozzarella.',
                'image' => 'products/bbq-csirke.jpg',
                'price' => 3290,
                'sort_order' => 5,
                'ingredients' => ['BBQ alap', 'mozzarella', 'csirkemell', 'lilahagyma'],
                'labels' => ['⭐ Új'],
            ],
            [
                'name' => 'Tonno e Cipolla',
                'description' => 'Tonhal, vöröshagyma, kapribogyó és olívaolaj — a mediterrán ízek kedvelőinek.',
                'image' => 'products/tonno-e-cipolla.jpg',
                'price' => 2990,
                'sort_order' => 6,
                'ingredients' => ['paradicsom alap', 'mozzarella', 'tonhal', 'vöröshagyma', 'kapribogyó'],
                'labels' => [],
            ],
        ];

        foreach ($pizza32 as $data) {
            $product = Product::create([
                'category_id' => $cat('Pizzák (32cm)')->id,
                'name' => $data['name'],
                'description' => $data['description'],
                'image' => $data['image'],
                'price' => $data['price'],
                'sort_order' => $data['sort_order'],
            ]);
            $product->ingredients()->attach(array_map($ing, $data['ingredients']));
            if ($data['labels']) {
                $product->labels()->attach(array_map($label, $data['labels']));
            }
        }

        // ── Hamburgerek ──────────────────────────────────────────────────────
        $burgers = [
            [
                'name' => 'Sajtburger',
                'description' => 'Szaftos marha húspogácsa dupla cheddar sajttal, friss salátával és paradicsomos szósszal.',
                'image' => 'products/sajtburger.jpg',
                'price' => 2890,
                'sort_order' => 1,
                'ingredients' => ['darált marha', 'cheddar', 'salátalevél', 'paradicsom', 'uborka', 'ketchup', 'majonéz'],
                'labels' => ['🔥 Legjobb'],
            ],
            [
                'name' => 'Bacon Burger',
                'description' => 'Ropogós bacon, cheddar sajt, karamellizált hagyma és BBQ szósz.',
                'image' => 'products/bacon-burger.jpg',
                'price' => 3190,
                'sort_order' => 2,
                'ingredients' => ['darált marha', 'bacon', 'cheddar', 'lilahagyma', 'BBQ alap', 'majonéz'],
                'labels' => [],
            ],
            [
                'name' => 'Crispy Csirke Burger',
                'description' => 'Ropogósra sült csirkemell, coleslaw saláta, jalapeño és csípős majonéz.',
                'image' => 'products/crispy-csirke-burger.jpg',
                'price' => 2990,
                'sort_order' => 3,
                'ingredients' => ['csirkemell', 'salátalevél', 'jalapeño', 'majonéz'],
                'labels' => ['⭐ Új', 'Csípős'],
            ],
        ];

        foreach ($burgers as $data) {
            $product = Product::create([
                'category_id' => $cat('Hamburgerek')->id,
                'name' => $data['name'],
                'description' => $data['description'],
                'image' => $data['image'],
                'price' => $data['price'],
                'sort_order' => $data['sort_order'],
            ]);
            $product->ingredients()->attach(array_map($ing, $data['ingredients']));
            if ($data['labels']) {
                $product->labels()->attach(array_map($label, $data['labels']));
            }
        }

        // ── Köretek ──────────────────────────────────────────────────────────
        $sides = [
            [
                'name' => 'Sült krumpli',
                'description' => 'Aranybarna, ropogós hasábburgonya tengeri sóval megszórva.',
                'image' => 'products/sult-krumpli.jpg',
                'price' => 790,
                'sort_order' => 1,
                'ingredients' => ['burgonya'],
                'labels' => ['Vegetáriánus', 'Gluténmentes'],
            ],
            [
                'name' => 'Fokhagymás pirítós',
                'description' => 'Friss kenyér fokhagymás vajjal megkenve, aranybarnára pirítva.',
                'image' => 'products/fokhagymas-piritos.jpg',
                'price' => 590,
                'sort_order' => 2,
                'ingredients' => ['fokhagyma', 'bazsalikom'],
                'labels' => ['Vegetáriánus'],
            ],
        ];

        foreach ($sides as $data) {
            $product = Product::create([
                'category_id' => $cat('Köretek')->id,
                'name' => $data['name'],
                'description' => $data['description'],
                'image' => $data['image'],
                'price' => $data['price'],
                'sort_order' => $data['sort_order'],
            ]);
            $product->ingredients()->attach(array_map($ing, $data['ingredients']));
            if ($data['labels']) {
                $product->labels()->attach(array_map($label, $data['labels']));
            }
        }

        // ── Szószok ──────────────────────────────────────────────────────────
        $sauces = [
            [
                'name' => 'Tzatziki',
                'description' => 'Görög joghurtos-uborkas mártogatós, fokhagymával és mentával.',
                'image' => 'products/tzatziki.jpg',
                'price' => 490,
                'sort_order' => 1,
                'ingredients' => ['tejföl', 'uborka', 'fokhagyma'],
                'labels' => ['Vegetáriánus', 'Gluténmentes'],
            ],
            [
                'name' => 'Füstölt BBQ szósz',
                'description' => 'Házi készítésű füstölt BBQ mártogatós, enyhén csípős.',
                'image' => 'products/fustolt-bbq-szosz.jpg',
                'price' => 390,
                'sort_order' => 2,
                'ingredients' => [],
                'labels' => ['Vegetáriánus'],
            ],
        ];

        foreach ($sauces as $data) {
            $product = Product::create([
                'category_id' => $cat('Szószok')->id,
                'name' => $data['name'],
                'description' => $data['description'],
                'image' => $data['image'],
                'price' => $data['price'],
                'sort_order' => $data['sort_order'],
            ]);
            if ($data['ingredients']) {
                $product->ingredients()->attach(array_map($ing, $data['ingredients']));
            }
            if ($data['labels']) {
                $product->labels()->attach(array_map($label, $data['labels']));
            }
        }

        // ── Saláták ──────────────────────────────────────────────────────────
        $salads = [
            [
                'name' => 'Caesar saláta',
                'description' => 'Ropogós római saláta, parmezán forgácsok, krutonnal és Caesar öntettel.',
                'image' => 'products/caesar-salata.jpg',
                'price' => 1890,
                'sort_order' => 1,
                'ingredients' => ['salátalevél', 'parmezán', 'csirkemell', 'majonéz'],
                'labels' => ['🍂 Szezonális'],
            ],
            [
                'name' => 'Görög saláta',
                'description' => 'Paradicsom, uborka, lilahagyma, olívabogyó és fetasajt, olívaolajjal meglocsolva.',
                'image' => 'products/gorog-salata.jpg',
                'price' => 1690,
                'sort_order' => 2,
                'ingredients' => ['paradicsom', 'uborka', 'lilahagyma', 'olívabogyó'],
                'labels' => ['Vegetáriánus', 'Gluténmentes'],
            ],
        ];

        foreach ($salads as $data) {
            $product = Product::create([
                'category_id' => $cat('Saláták')->id,
                'name' => $data['name'],
                'description' => $data['description'],
                'image' => $data['image'],
                'price' => $data['price'],
                'sort_order' => $data['sort_order'],
            ]);
            $product->ingredients()->attach(array_map($ing, $data['ingredients']));
            if ($data['labels']) {
                $product->labels()->attach(array_map($label, $data['labels']));
            }
        }

        // ── Italok ───────────────────────────────────────────────────────────
        $drinks = [
            ['name' => 'Coca-Cola 0.5L',               'image' => 'products/coca-cola.png',          'price' => 590, 'sort_order' => 1],
            ['name' => 'Sprite 0.5L',                  'image' => 'products/sprite.png',              'price' => 590, 'sort_order' => 2],
            ['name' => 'Cappy 0.5L',                   'image' => 'products/cappy.png',               'price' => 590, 'sort_order' => 3],
            ['name' => 'Ice Tea 0.5L',                 'image' => 'products/ice-tea.png',             'price' => 590, 'sort_order' => 4],
            ['name' => 'NaturAqua Szénsavmentes 0.5L', 'image' => 'products/naturaqua-still.png',     'price' => 390, 'sort_order' => 5],
            ['name' => 'NaturAqua Szénsavas 0.5L',     'image' => 'products/naturaqua-sparkling.png', 'price' => 390, 'sort_order' => 6],
        ];

        foreach ($drinks as $data) {
            Product::create([
                'category_id' => $cat('Italok')->id,
                'name' => $data['name'],
                'image' => $data['image'],
                'price' => $data['price'],
                'sort_order' => $data['sort_order'],
            ]);
        }
    }

    private function copyImages(): void
    {
        $source = database_path('seeders/images');

        Storage::disk('public')->makeDirectory('products');

        foreach (glob("$source/*") as $file) {
            Storage::disk('public')->put(
                'products/'.basename($file),
                file_get_contents($file)
            );
        }
    }
}
