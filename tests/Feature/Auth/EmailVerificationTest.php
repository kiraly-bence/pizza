<?php

namespace Tests\Feature\Auth;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_verification_email_is_sent_after_registration(): void
    {
        Notification::fake();

        $this->post('/register', [
            'name'                  => 'Teszt Felhasználó',
            'email'                 => 'teszt@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $user = User::where('email', 'teszt@example.com')->first();
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_user_is_not_verified_after_registration(): void
    {
        $this->post('/register', [
            'name'                  => 'Teszt Felhasználó',
            'email'                 => 'teszt@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $user = User::where('email', 'teszt@example.com')->first();
        $this->assertNull($user->email_verified_at);
    }

    public function test_user_can_verify_email_with_valid_link(): void
    {
        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $this->actingAs($user)->get($verificationUrl);

        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_verification_redirects_to_home(): void
    {
        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        $response->assertRedirect('/');
    }

    public function test_verification_fails_with_invalid_hash(): void
    {
        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => 'invalid-hash']
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        $response->assertForbidden();
        $this->assertNull($user->fresh()->email_verified_at);
    }

    public function test_user_can_resend_verification_email(): void
    {
        Notification::fake();

        $user = User::factory()->unverified()->create();

        $this->actingAs($user)->post('/email/verification-notification');

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_resend_does_nothing_if_already_verified(): void
    {
        Notification::fake();

        $user = User::factory()->create(); // already verified

        $this->actingAs($user)->post('/email/verification-notification');

        Notification::assertNotSentTo($user, VerifyEmail::class);
    }

    public function test_unverified_user_cannot_place_order(): void
    {
        $user    = User::factory()->unverified()->create();
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

    public function test_verified_user_can_place_order(): void
    {
        $user    = User::factory()->create(); // verified by default
        $product = Product::factory()->create(['price' => 2000]);

        $response = $this->actingAs($user)->post('/orders', [
            'payment_method' => 'cash',
            'zip'            => '1000',
            'city'           => 'Budapest',
            'street'         => 'Fő utca 1.',
            'items'          => [['id' => $product->id, 'quantity' => 1]],
        ]);

        $response->assertRedirect('/');
        $this->assertDatabaseCount('orders', 1);
    }
}
