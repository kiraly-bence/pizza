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
        if (!$this->authService->attempt($request->only('email', 'password'), $request)) {
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
