<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService) {}

    public function register(RegisterRequest $request)
    {
        $this->authService->register($request->validated());

        return redirect()->route('home');
    }

    public function login(LoginRequest $request)
    {
        $result = $this->authService->attempt($request->only('email', 'password'), $request);

        if ($result === 'banned') {
            return back()->withErrors(['email' => 'Ez a fiók le van tiltva.'])
                         ->withInput($request->only('email'));
        }

        if (!$result) {
            return back()->withErrors(['email' => 'Hibás e-mail cím vagy jelszó.'])
                         ->withInput($request->only('email'));
        }

        return redirect()->route('home');
    }

    public function logout(Request $request)
    {
        $this->authService->logout($request);

        return redirect()->route('home');
    }
}
