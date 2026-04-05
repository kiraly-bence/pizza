<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
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
            'zip' => '1234',
            'city' => 'Budapest',
            'street' => 'Kossuth tér 1.',
            'note' => '2. emelet',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'zip' => '1234',
            'city' => 'Budapest',
            'street' => 'Kossuth tér 1.',
            'note' => '2. emelet',
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
            'zip' => '1234',
            'city' => 'Budapest',
            'street' => 'Kossuth tér 1.',
        ]);

        $response->assertRedirect('/');
    }

    public function test_user_can_update_name_and_email(): void
    {
        $user = User::factory()->create(['name' => 'Régi Név', 'email' => 'regi@example.com']);

        $response = $this->actingAs($user)->post('/profil/adatok', [
            'name' => 'Új Név',
            'email' => 'uj@example.com',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Új Név', 'email' => 'uj@example.com']);
    }

    public function test_email_change_clears_verification_and_sends_notification(): void
    {
        Notification::fake();
        $user = User::factory()->create(['email' => 'regi@example.com', 'email_verified_at' => now()]);

        $this->actingAs($user)->post('/profil/adatok', [
            'name' => $user->name,
            'email' => 'uj@example.com',
        ]);

        $this->assertNull($user->fresh()->email_verified_at);
        Notification::assertSentTo($user->fresh(), VerifyEmail::class);
    }

    public function test_keeping_same_email_does_not_clear_verification(): void
    {
        $verifiedAt = now()->subDay();
        $user = User::factory()->create(['email_verified_at' => $verifiedAt]);

        $this->actingAs($user)->post('/profil/adatok', [
            'name' => 'Új Név',
            'email' => $user->email,
        ]);

        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_email_must_be_unique(): void
    {
        $existing = User::factory()->create(['email' => 'foglalt@example.com']);
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/profil/adatok', [
            'name' => $user->name,
            'email' => 'foglalt@example.com',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_profile_update_requires_name_and_email(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/profil/adatok', []);

        $response->assertSessionHasErrors(['name', 'email']);
    }

    public function test_user_can_change_password(): void
    {
        $user = User::factory()->create(['password' => Hash::make('old-password')]);

        $response = $this->actingAs($user)->post('/profil/jelszo', [
            'current_password' => 'old-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertRedirect();
        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
    }

    public function test_wrong_current_password_is_rejected(): void
    {
        $user = User::factory()->create(['password' => Hash::make('correct-password')]);

        $response = $this->actingAs($user)->post('/profil/jelszo', [
            'current_password' => 'wrong-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertSessionHasErrors('current_password');
    }

    public function test_password_confirmation_must_match(): void
    {
        $user = User::factory()->create(['password' => Hash::make('old-password')]);

        $response = $this->actingAs($user)->post('/profil/jelszo', [
            'current_password' => 'old-password',
            'password' => 'new-password',
            'password_confirmation' => 'different-password',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_password_must_be_at_least_8_characters(): void
    {
        $user = User::factory()->create(['password' => Hash::make('old-password')]);

        $response = $this->actingAs($user)->post('/profil/jelszo', [
            'current_password' => 'old-password',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_guest_cannot_update_profile(): void
    {
        $response = $this->post('/profil/adatok', ['name' => 'Test', 'email' => 'test@example.com']);

        $response->assertRedirect('/');
    }

    public function test_guest_cannot_change_password(): void
    {
        $response = $this->post('/profil/jelszo', [
            'current_password' => 'password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertRedirect('/');
    }
}
