<?php

namespace Tests\Feature\Admin;

use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_settings_page(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/admin/settings');

        $response->assertOk();
    }

    public function test_settings_page_shows_default_fees_when_db_is_empty(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/admin/settings');

        $response->assertOk();
        // Defaults come from OrderService constants when no DB row exists
        $data = $response->original->getData()['page']['props']['fees'];
        $this->assertEquals(OrderService::DELIVERY_FEE, $data['delivery_fee']);
        $this->assertEquals(OrderService::SERVICE_FEE,  $data['service_fee']);
    }

    public function test_admin_can_update_fees(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/admin/settings', [
            'delivery_fee' => 1500,
            'service_fee'  => 299,
        ]);

        $response->assertRedirect();
        $this->assertEquals('1500', Setting::get('delivery_fee'));
        $this->assertEquals('299',  Setting::get('service_fee'));
    }

    public function test_admin_can_set_fees_to_zero(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/admin/settings', [
            'delivery_fee' => 0,
            'service_fee'  => 0,
        ]);

        $this->assertEquals('0', Setting::get('delivery_fee'));
        $this->assertEquals('0', Setting::get('service_fee'));
    }

    public function test_fees_cannot_be_negative(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/admin/settings', [
            'delivery_fee' => -100,
            'service_fee'  => 199,
        ]);

        $response->assertSessionHasErrors('delivery_fee');
    }

    public function test_fees_must_be_integers(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/admin/settings', [
            'delivery_fee' => 'abc',
            'service_fee'  => 199,
        ]);

        $response->assertSessionHasErrors('delivery_fee');
    }

    public function test_non_admin_cannot_view_settings(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/settings');

        $response->assertForbidden();
    }

    public function test_non_admin_cannot_update_settings(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/admin/settings', [
            'delivery_fee' => 1500,
            'service_fee'  => 299,
        ]);

        $response->assertForbidden();
        $this->assertNull(Setting::get('delivery_fee'));
    }

    public function test_order_uses_db_configured_delivery_fee(): void
    {
        Setting::set('delivery_fee', 1500);
        Setting::set('service_fee', 299);

        $user    = User::factory()->create();
        $product = Product::factory()->create(['price' => 2000]);

        $this->actingAs($user)->post('/orders', [
            'payment_method' => 'cash',
            'zip'            => '1000',
            'city'           => 'Budapest',
            'street'         => 'Fő utca 1.',
            'items'          => [['id' => $product->id, 'quantity' => 1]],
        ]);

        $order = \App\Models\Order::first();
        $this->assertEquals(1500, $order->delivery_fee);
        $this->assertEquals(299,  $order->service_fee);
        $this->assertEquals(2000 + 1500 + 299, $order->total);
    }

    public function test_checkout_page_reflects_db_configured_fees(): void
    {
        Setting::set('delivery_fee', 1200);
        Setting::set('service_fee', 250);

        $user     = User::factory()->create();
        $response = $this->actingAs($user)->get('/checkout');

        $response->assertOk();
        $data = $response->original->getData()['page']['props'];
        $this->assertEquals(1200, $data['deliveryFee']);
        $this->assertEquals(250,  $data['serviceFee']);
    }

    public function test_updating_settings_overwrites_previous_values(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/admin/settings', [
            'delivery_fee' => 1000,
            'service_fee'  => 200,
        ]);

        $this->actingAs($admin)->post('/admin/settings', [
            'delivery_fee' => 2000,
            'service_fee'  => 400,
        ]);

        $this->assertEquals('2000', Setting::get('delivery_fee'));
        $this->assertEquals('400',  Setting::get('service_fee'));
    }

    public function test_admin_can_save_opening_hours(): void
    {
        $admin = User::factory()->admin()->create();
        $hours = $this->defaultHoursPayload();

        $response = $this->actingAs($admin)->post('/admin/settings/hours', ['hours' => $hours]);

        $response->assertRedirect();
        $stored = json_decode(Setting::get('opening_hours'), true);
        $this->assertEquals('10:00', $stored[1]['open']);
        $this->assertEquals('22:00', $stored[1]['close']);
    }

    public function test_admin_can_mark_day_as_closed(): void
    {
        $admin = User::factory()->admin()->create();
        $hours = $this->defaultHoursPayload();
        $hours[0]['closed'] = true; // Sunday closed

        $this->actingAs($admin)->post('/admin/settings/hours', ['hours' => $hours]);

        $stored = json_decode(Setting::get('opening_hours'), true);
        $this->assertTrue($stored[0]['closed']);
    }

    public function test_non_admin_cannot_save_opening_hours(): void
    {
        $user  = User::factory()->create();
        $hours = $this->defaultHoursPayload();

        $response = $this->actingAs($user)->post('/admin/settings/hours', ['hours' => $hours]);

        $response->assertForbidden();
    }

    public function test_admin_can_pause_orders(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/admin/settings/pause');

        $this->assertEquals('1', Setting::get('orders_paused'));
    }

    public function test_admin_can_resume_orders(): void
    {
        Setting::set('orders_paused', '1');
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/admin/settings/pause');

        $this->assertEquals('0', Setting::get('orders_paused'));
    }

    public function test_non_admin_cannot_toggle_pause(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/admin/settings/pause');

        $response->assertForbidden();
    }

    public function test_order_is_rejected_when_paused(): void
    {
        Setting::set('orders_paused', '1');

        $user    = User::factory()->create();
        $product = Product::factory()->create(['price' => 2000]);

        $response = $this->actingAs($user)->post('/orders', [
            'payment_method' => 'cash',
            'zip'            => '1000',
            'city'           => 'Budapest',
            'street'         => 'Fő utca 1.',
            'items'          => [['id' => $product->id, 'quantity' => 1]],
        ]);

        $response->assertSessionHasErrors('store');
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_order_is_rejected_outside_opening_hours(): void
    {
        $closedHours = $this->defaultHoursPayload();
        foreach ($closedHours as &$day) {
            $day['open']  = '00:00';
            $day['close'] = '00:01'; // effectively always closed
        }
        Setting::set('opening_hours', json_encode($closedHours));

        $user    = User::factory()->create();
        $product = Product::factory()->create(['price' => 2000]);

        $response = $this->actingAs($user)->post('/orders', [
            'payment_method' => 'cash',
            'zip'            => '1000',
            'city'           => 'Budapest',
            'street'         => 'Fő utca 1.',
            'items'          => [['id' => $product->id, 'quantity' => 1]],
        ]);

        $response->assertSessionHasErrors('store');
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_order_is_accepted_when_within_opening_hours(): void
    {
        $openHours = $this->defaultHoursPayload();
        foreach ($openHours as &$day) {
            $day['open']  = '00:00';
            $day['close'] = '23:59';
        }
        Setting::set('opening_hours', json_encode($openHours));

        $user    = User::factory()->create();
        $product = Product::factory()->create(['price' => 2000]);

        $response = $this->actingAs($user)->post('/orders', [
            'payment_method' => 'cash',
            'zip'            => '1000',
            'city'           => 'Budapest',
            'street'         => 'Fő utca 1.',
            'items'          => [['id' => $product->id, 'quantity' => 1]],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseCount('orders', 1);
    }

    public function test_is_open_returns_true_when_no_hours_configured(): void
    {
        $service = app(\App\Services\Admin\SettingService::class);

        $this->assertTrue($service->isOpen());
    }

    private function defaultHoursPayload(): array
    {
        $hours = [];
        for ($i = 0; $i <= 6; $i++) {
            $hours[$i] = ['open' => '10:00', 'close' => '22:00', 'closed' => false];
        }
        return $hours;
    }
}
