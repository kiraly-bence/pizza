<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        Setting::set('delivery_fee', 990);
        Setting::set('service_fee', 199);

        Setting::set('contact_phone',   '+36 1 234 5678');
        Setting::set('contact_email',   'info@csepelpizza.hu');
        Setting::set('contact_address', '1211 Budapest, Csepel utca 1.');

        // 0 = Hétfő … 6 = Vasárnap
        Setting::set('opening_hours', json_encode([
            0 => ['open' => '11:00', 'close' => '22:00', 'closed' => false], // Hétfő
            1 => ['open' => '11:00', 'close' => '22:00', 'closed' => false], // Kedd
            2 => ['open' => '11:00', 'close' => '22:00', 'closed' => false], // Szerda
            3 => ['open' => '11:00', 'close' => '22:00', 'closed' => false], // Csütörtök
            4 => ['open' => '11:00', 'close' => '23:00', 'closed' => false], // Péntek
            5 => ['open' => '11:00', 'close' => '23:00', 'closed' => false], // Szombat
            6 => ['open' => '12:00', 'close' => '21:00', 'closed' => false], // Vasárnap
        ]));
    }
}
