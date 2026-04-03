<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'name.required'      => 'A név megadása kötelező.',
            'email.required'     => 'Az e-mail cím megadása kötelező.',
            'email.email'        => 'Érvényes e-mail címet adj meg.',
            'email.unique'       => 'Ez az e-mail cím már foglalt.',
            'password.required'  => 'A jelszó megadása kötelező.',
            'password.confirmed' => 'A két jelszó nem egyezik meg.',
            'password.min'       => 'A jelszónak legalább 8 karakter hosszúnak kell lennie.',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'user',
        ]);

        Auth::login($user);

        return redirect()->route('home');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required'    => 'Az e-mail cím megadása kötelező.',
            'email.email'       => 'Érvényes e-mail címet adj meg.',
            'password.required' => 'A jelszó megadása kötelező.',
        ]);

        if (!Auth::attempt(['email' => $validated['email'], 'password' => $validated['password']])) {
            return back()->withErrors([
                'email' => 'Hibás e-mail cím vagy jelszó.',
            ])->withInput($request->only('email'));
        }

        $request->session()->regenerate();

        if (Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('home');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
