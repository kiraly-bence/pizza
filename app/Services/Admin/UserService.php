<?php

namespace App\Services\Admin;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Collection;
use RuntimeException;

class UserService
{
    public function all(): Collection
    {
        return User::withCount('orders')->latest()->get()->map(fn($u) => [
            'id'           => $u->id,
            'name'         => $u->name,
            'email'        => $u->email,
            'role'         => $u->role->value,
            'banned_at'    => $u->banned_at?->format('Y. m. d. H:i'),
            'orders_count' => $u->orders_count,
            'created_at'   => $u->created_at->format('Y. m. d.'),
        ]);
    }

    public function updateRole(User $user, string $role, int $currentUserId): void
    {
        if ($user->id === $currentUserId && $role !== UserRole::Admin->value) {
            throw new RuntimeException('Saját admin jogosultságodat nem vonhatod meg.');
        }

        $user->update(['role' => $role]);
    }

    public function ban(User $user, int $currentUserId): void
    {
        if ($user->id === $currentUserId) {
            throw new RuntimeException('Saját magadat nem tilthatod le.');
        }

        if ($user->isAdmin()) {
            throw new RuntimeException('Admin felhasználót nem lehet letiltani.');
        }

        $user->update(['banned_at' => now()]);
    }

    public function unban(User $user): void
    {
        $user->update(['banned_at' => null]);
    }
}
