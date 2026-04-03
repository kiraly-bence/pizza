<?php

namespace App\Services;

use App\Models\User;

class ProfileService
{
    public function updateAddress(User $user, array $data): void
    {
        $user->update($data);
    }
}
