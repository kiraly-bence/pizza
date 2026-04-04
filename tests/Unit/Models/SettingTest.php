<?php

namespace Tests\Unit\Models;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_returns_default_when_key_does_not_exist(): void
    {
        $this->assertNull(Setting::get('nonexistent'));
        $this->assertEquals('default', Setting::get('nonexistent', 'default'));
    }

    public function test_set_creates_new_setting(): void
    {
        Setting::set('my_key', 'my_value');

        $this->assertDatabaseHas('settings', ['key' => 'my_key', 'value' => 'my_value']);
    }

    public function test_get_returns_stored_value(): void
    {
        Setting::set('my_key', 'hello');

        $this->assertEquals('hello', Setting::get('my_key'));
    }

    public function test_set_updates_existing_setting(): void
    {
        Setting::set('my_key', 'first');
        Setting::set('my_key', 'second');

        $this->assertEquals('second', Setting::get('my_key'));
        $this->assertDatabaseCount('settings', 1);
    }

    public function test_set_stores_integer_as_string(): void
    {
        Setting::set('number', 42);

        $this->assertEquals('42', Setting::get('number'));
    }

    public function test_set_stores_json_as_string(): void
    {
        $data = json_encode(['foo' => 'bar']);
        Setting::set('json_key', $data);

        $this->assertEquals($data, Setting::get('json_key'));
    }

    public function test_multiple_keys_stored_independently(): void
    {
        Setting::set('key_a', 'value_a');
        Setting::set('key_b', 'value_b');

        $this->assertEquals('value_a', Setting::get('key_a'));
        $this->assertEquals('value_b', Setting::get('key_b'));
    }
}
