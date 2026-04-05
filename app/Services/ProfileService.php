<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ProfileService
{
    public function updateAddress(User $user, array $data): void
    {
        $user->update($data);
    }

    public function updateProfile(User $user, string $name, string $email): bool
    {
        $emailChanged = $user->email !== $email;

        $user->update([
            'name' => $name,
            'email' => $email,
            'email_verified_at' => $emailChanged ? null : $user->email_verified_at,
        ]);

        if ($emailChanged) {
            $user->sendEmailVerificationNotification();
        }

        return $emailChanged;
    }

    public function updatePassword(User $user, string $password): void
    {
        $user->update(['password' => Hash::make($password)]);
    }
}
