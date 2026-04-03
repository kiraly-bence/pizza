<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUserRoleRequest;
use App\Models\User;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        $users = User::withCount('orders')->latest()->get()->map(fn($u) => [
            'id'           => $u->id,
            'name'         => $u->name,
            'email'        => $u->email,
            'role'         => $u->role,
            'orders_count' => $u->orders_count,
            'created_at'   => $u->created_at->format('Y. m. d.'),
        ]);

        return Inertia::render('Admin/Users', [
            'auth'  => ['user' => auth()->user()],
            'users' => $users,
        ]);
    }

    public function updateRole(UpdateUserRoleRequest $request, User $user)
    {
        if ($user->id === auth()->id() && $request->role !== 'admin') {
            return back()->withErrors(['role' => 'Saját admin jogosultságodat nem vonhatod meg.']);
        }

        $user->update($request->validated());

        return back();
    }
}
