<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Services\PasswordResetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Inertia\Inertia;
use Inertia\Response;

class PasswordResetController extends Controller
{
    public function __construct(private readonly PasswordResetService $passwordResetService) {}

    public function sendLink(ForgotPasswordRequest $request): RedirectResponse
    {
        $this->passwordResetService->sendResetLink($request->email);

        return back()->with('forgot_status', 'sent');
    }

    public function showReset(Request $request, string $token): Response
    {
        return Inertia::render('ResetPassword', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    public function reset(ResetPasswordRequest $request): RedirectResponse
    {
        $status = $this->passwordResetService->reset($request->validated());

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('home')->with('reset_success', true);
        }

        return back()->withErrors([
            'email' => match ($status) {
                Password::INVALID_TOKEN => 'Érvénytelen vagy lejárt hivatkozás. Kérj új jelszó-visszaállítási e-mailt.',
                Password::INVALID_USER  => 'Nem található fiók ezzel az e-mail címmel.',
                default                 => 'Valami hiba történt. Próbáld újra.',
            },
        ]);
    }
}
