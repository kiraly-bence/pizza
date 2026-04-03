<?php

namespace App\Services\Admin;

use App\Models\User;
use Illuminate\Support\Collection;

class UserService
{
    public function all(): Collection
    {
        return User::withCount('orders')->latest()->get()->map(fn($u) => [
            'id'           => $u->id,
            'name'         => $u->name,
            'email'        => $u->email,
            'role'         => $u->role,
            'orders_count' => $u->orders_count,
            'created_at'   => $u->created_at->format('Y. m. d.'),
        ]);
    }

    public function updateRole(User $user, string $role, int $currentUserId): void
    {
        if ($user->id === $currentUserId && $role !== 'admin') {
            throw new \RuntimeException('Saját admin jogosultságodat nem vonhatod meg.');
        }

        $user->update(['role' => $role]);
    }
}
