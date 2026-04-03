<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
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

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => ['required', 'in:user,admin'],
        ]);

        // Prevent removing your own admin role
        if ($user->id === auth()->id() && $request->role !== 'admin') {
            return back()->withErrors(['role' => 'Saját admin jogosultságodat nem vonhatod meg.']);
        }

        $user->update(['role' => $request->role]);

        return back();
    }
}
