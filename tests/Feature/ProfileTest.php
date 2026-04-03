<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_profile(): void
    {
        $response = $this->get('/profil');

        $response->assertRedirect('/');
    }

    public function test_user_can_view_profile(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/profil');

        $response->assertOk();
    }

    public function test_user_can_update_address(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/profil/cim', [
            'zip'    => '1234',
            'city'   => 'Budapest',
            'street' => 'Kossuth tér 1.',
            'note'   => '2. emelet',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id'     => $user->id,
            'zip'    => '1234',
            'city'   => 'Budapest',
            'street' => 'Kossuth tér 1.',
            'note'   => '2. emelet',
        ]);
    }

    public function test_address_update_accepts_empty_fields(): void
    {
        // All address fields are nullable — empty submission is valid
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/profil/cim', []);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
    }

    public function test_guest_cannot_update_address(): void
    {
        $response = $this->post('/profil/cim', [
            'zip'    => '1234',
            'city'   => 'Budapest',
            'street' => 'Kossuth tér 1.',
        ]);

        $response->assertRedirect('/');
    }
}
