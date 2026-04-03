<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Label;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // --- Labels ---
        $labelVege      = Label::create(['name' => 'Vegetáriánus', 'type' => 'secondary']);
        $labelSpicy     = Label::create(['name' => 'Csípős',       'type' => 'secondary']);
        $labelNew       = Label::create(['name' => '⭐ Új',        'type' => 'primary']);
        $labelBest      = Label::create(['name' => '🔥 Legjobb',  'type' => 'primary']);
        $labelSeasonal  = Label::create(['name' => '🍂 Szezonális','type' => 'primary']);
        $labelGlutenFree= Label::create(['name' => 'Gluténmentes', 'type' => 'secondary']);

        // --- Ingredients ---
        $ingredients = collect([
            'paradicsom alap', 'tejszínes alap', 'BBQ alap',
            'mozzarella', 'gorgonzola', 'parmezán', 'pecorino', 'cheddar',
            'szalámi', 'sonka', 'csirkemell', 'tonhal', 'bacon', 'darált marha',
            'gomba', 'jalapeño', 'lilahagyma', 'vöröshagyma', 'paprika', 'kukorica',
            'kapribogyó', 'olívabogyó', 'bazsalikom', 'rukkola', 'paradicsom',
            'uborka', 'salátalevél', 'ketchup', 'majonéz', 'mustár',
            'burgonya', 'fokhagyma', 'tejföl', 'tojás',
        ])->mapWithKeys(fn($name) => [$name => Ingredient::create(['name' => $name])]);

        // --- Categories & Products ---

        // PIZZÁK
        $pizzak = Category::create(['name' => 'Pizzák', 'sort_order' => 1]);

        $margherita = Product::create([
            'category_id' => $pizzak->id,
            'name'        => 'Margherita',
            'description' => 'Klasszikus olasz pizza paradicsomszósszal, friss mozzarellával és bazsalikommal.',
            'image'       => 'products/margherita.jpg',
            'price'       => 2490,
            'sort_order'  => 1,
        ]);
        $margherita->ingredients()->attach([
            $ingredients['paradicsom alap']->id,
            $ingredients['mozzarella']->id,
            $ingredients['bazsalikom']->id,
        ]);
        $margherita->labels()->attach([$labelVege->id]);

        $diavola = Product::create([
            'category_id' => $pizzak->id,
            'name'        => 'Diavola',
            'description' => 'Csípős szalámi, jalapeño paprika és füstölt mozzarella az igazi pikáns élményért.',
            'image'       => 'products/diavola.jpg',
            'price'       => 2890,
            'sort_order'  => 2,
        ]);
        $diavola->ingredients()->attach([
            $ingredients['paradicsom alap']->id,
            $ingredients['mozzarella']->id,
            $ingredients['szalámi']->id,
            $ingredients['jalapeño']->id,
        ]);
        $diavola->labels()->attach([$labelBest->id, $labelSpicy->id]);

        $quattro = Product::create([
            'category_id' => $pizzak->id,
            'name'        => 'Quattro Formaggi',
            'description' => 'Négy sajt harmonikus keveréke: mozzarella, gorgonzola, parmezán és pecorino.',
            'image'       => 'products/quattro-formaggi.jpg',
            'price'       => 3190,
            'sort_order'  => 3,
        ]);
        $quattro->ingredients()->attach([
            $ingredients['paradicsom alap']->id,
            $ingredients['mozzarella']->id,
            $ingredients['gorgonzola']->id,
            $ingredients['parmezán']->id,
            $ingredients['pecorino']->id,
        ]);
        $quattro->labels()->attach([$labelVege->id]);

        $prosciutto = Product::create([
            'category_id' => $pizzak->id,
            'name'        => 'Prosciutto e Funghi',
            'description' => 'Pármai sonka és friss erdei gombák, tejszínes alapon tálalva.',
            'image'       => 'products/prosciutto-e-funghi.jpg',
            'price'       => 3090,
            'sort_order'  => 4,
        ]);
        $prosciutto->ingredients()->attach([
            $ingredients['tejszínes alap']->id,
            $ingredients['mozzarella']->id,
            $ingredients['sonka']->id,
            $ingredients['gomba']->id,
        ]);

        $bbqCsirke = Product::create([
            'category_id' => $pizzak->id,
            'name'        => 'BBQ Csirke',
            'description' => 'Grillezett csirkemell, füstölt BBQ szósz, lilahagyma és mozzarella.',
            'image'       => 'products/bbq-csirke.jpg',
            'price'       => 3290,
            'sort_order'  => 5,
        ]);
        $bbqCsirke->ingredients()->attach([
            $ingredients['BBQ alap']->id,
            $ingredients['mozzarella']->id,
            $ingredients['csirkemell']->id,
            $ingredients['lilahagyma']->id,
        ]);
        $bbqCsirke->labels()->attach([$labelNew->id]);

        $tonno = Product::create([
            'category_id' => $pizzak->id,
            'name'        => 'Tonno e Cipolla',
            'description' => 'Tonhal, vöröshagyma, kapribogyó és olívaolaj — a mediterrán ízek kedvelőinek.',
            'image'       => 'products/tonno-e-cipolla.jpg',
            'price'       => 2990,
            'sort_order'  => 6,
        ]);
        $tonno->ingredients()->attach([
            $ingredients['paradicsom alap']->id,
            $ingredients['mozzarella']->id,
            $ingredients['tonhal']->id,
            $ingredients['vöröshagyma']->id,
            $ingredients['kapribogyó']->id,
        ]);

        // HAMBURGEREK
        $hamburgerek = Category::create(['name' => 'Hamburgerek', 'sort_order' => 2]);

        $sajtburger = Product::create([
            'category_id' => $hamburgerek->id,
            'name'        => 'Sajtburger',
            'description' => 'Szaftos marha húspogácsa dupla cheddar sajttal, friss salátával és paradicsomos szósszal.',
            'image'       => 'products/sajtburger.jpg',
            'price'       => 2890,
            'sort_order'  => 1,
        ]);
        $sajtburger->ingredients()->attach([
            $ingredients['darált marha']->id,
            $ingredients['cheddar']->id,
            $ingredients['salátalevél']->id,
            $ingredients['paradicsom']->id,
            $ingredients['uborka']->id,
            $ingredients['ketchup']->id,
            $ingredients['majonéz']->id,
        ]);
        $sajtburger->labels()->attach([$labelBest->id]);

        $baconburger = Product::create([
            'category_id' => $hamburgerek->id,
            'name'        => 'Bacon Burger',
            'description' => 'Ropogós bacon, cheddar sajt, karamellizált hagyma és BBQ szósz.',
            'image'       => 'products/bacon-burger.jpg',
            'price'       => 3190,
            'sort_order'  => 2,
        ]);
        $baconburger->ingredients()->attach([
            $ingredients['darált marha']->id,
            $ingredients['bacon']->id,
            $ingredients['cheddar']->id,
            $ingredients['lilahagyma']->id,
            $ingredients['BBQ alap']->id,
            $ingredients['majonéz']->id,
        ]);

        $csirkeburger = Product::create([
            'category_id' => $hamburgerek->id,
            'name'        => 'Crispy Csirke Burger',
            'description' => 'Ropogósra sült csirkemell, coleslaw saláta, jalapeño és csípős majonéz.',
            'image'       => 'products/crispy-csirke-burger.jpg',
            'price'       => 2990,
            'sort_order'  => 3,
        ]);
        $csirkeburger->ingredients()->attach([
            $ingredients['csirkemell']->id,
            $ingredients['salátalevél']->id,
            $ingredients['jalapeño']->id,
            $ingredients['majonéz']->id,
        ]);
        $csirkeburger->labels()->attach([$labelNew->id, $labelSpicy->id]);

        // KÖRETEK
        $koretek = Category::create(['name' => 'Köretek', 'sort_order' => 3]);

        $sultKrumpli = Product::create([
            'category_id' => $koretek->id,
            'name'        => 'Sült krumpli',
            'description' => 'Aranybarna, ropogós hasábburgonya tengeri sóval megszórva.',
            'image'       => 'products/sult-krumpli.jpg',
            'price'       => 790,
            'sort_order'  => 1,
        ]);
        $sultKrumpli->ingredients()->attach([
            $ingredients['burgonya']->id,
        ]);
        $sultKrumpli->labels()->attach([$labelVege->id, $labelGlutenFree->id]);

        $fokhagymasSult = Product::create([
            'category_id' => $koretek->id,
            'name'        => 'Fokhagymás pirítós',
            'description' => 'Friss kenyér fokhagymás vajjal megkenve, aranybarnára pirítva.',
            'image'       => 'products/fokhagymas-piritos.jpg',
            'price'       => 590,
            'sort_order'  => 2,
        ]);
        $fokhagymasSult->ingredients()->attach([
            $ingredients['fokhagyma']->id,
            $ingredients['bazsalikom']->id,
        ]);
        $fokhagymasSult->labels()->attach([$labelVege->id]);

        // SZÓSZOK
        $szoszok = Category::create(['name' => 'Szószok', 'sort_order' => 4]);

        $tzatziki = Product::create([
            'category_id' => $szoszok->id,
            'name'        => 'Tzatziki',
            'description' => 'Görög joghurtos-uborkas mártogatós, fokhagymával és mentával.',
            'image'       => 'products/tzatziki.jpg',
            'price'       => 490,
            'sort_order'  => 1,
        ]);
        $tzatziki->ingredients()->attach([
            $ingredients['tejföl']->id,
            $ingredients['uborka']->id,
            $ingredients['fokhagyma']->id,
        ]);
        $tzatziki->labels()->attach([$labelVege->id, $labelGlutenFree->id]);

        $bbqSzosz = Product::create([
            'category_id' => $szoszok->id,
            'name'        => 'Füstölt BBQ szósz',
            'description' => 'Házi készítésű füstölt BBQ mártogatós, enyhén csípős.',
            'image'       => 'products/fustolt-bbq-szosz.jpg',
            'price'       => 390,
            'sort_order'  => 2,
        ]);
        $bbqSzosz->labels()->attach([$labelVege->id]);

        // SALÁTÁK
        $salatak = Category::create(['name' => 'Saláták', 'sort_order' => 5]);

        $caesarSalata = Product::create([
            'category_id' => $salatak->id,
            'name'        => 'Caesar saláta',
            'description' => 'Ropogós római saláta, parmezán forgácsok, krutonnal és Caesar öntettel.',
            'image'       => 'products/caesar-salata.jpg',
            'price'       => 1890,
            'sort_order'  => 1,
        ]);
        $caesarSalata->ingredients()->attach([
            $ingredients['salátalevél']->id,
            $ingredients['parmezán']->id,
            $ingredients['csirkemell']->id,
            $ingredients['majonéz']->id,
        ]);
        $caesarSalata->labels()->attach([$labelSeasonal->id]);

        $gorogSalata = Product::create([
            'category_id' => $salatak->id,
            'name'        => 'Görög saláta',
            'description' => 'Paradicsom, uborka, lilahagyma, olívabogyó és fetasajt, olívaolajjal meglocsolva.',
            'image'       => 'products/gorog-salata.jpg',
            'price'       => 1690,
            'sort_order'  => 2,
        ]);
        $gorogSalata->ingredients()->attach([
            $ingredients['paradicsom']->id,
            $ingredients['uborka']->id,
            $ingredients['lilahagyma']->id,
            $ingredients['olívabogyó']->id,
        ]);
        $gorogSalata->labels()->attach([$labelVege->id, $labelGlutenFree->id]);

        // --- Settings ---
        $this->call(SettingsSeeder::class);
    }
}
