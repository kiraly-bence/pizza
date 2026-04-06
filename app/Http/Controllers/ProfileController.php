<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateAddressRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Services\ProfileService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function __construct(private readonly ProfileService $profileService) {}

    public function index(): Response
    {
        $user = auth()->user();

        return Inertia::render('Profile', [
            'savedAddress' => [
                'zip' => $user->zip,
                'city' => $user->city,
                'street' => $user->street,
                'note' => $user->note,
            ],
        ]);
    }

    public function updateAddress(UpdateAddressRequest $request): RedirectResponse
    {
        $this->profileService->updateAddress(auth()->user(), $request->validated());

        return back()->with('address_saved', true);
    }

    public function updateProfile(UpdateProfileRequest $request): RedirectResponse
    {
        $emailChanged = $this->profileService->updateProfile(
            auth()->user(),
            $request->validated('name'),
            $request->validated('email'),
        );

        return back()->with('profile_saved', true)->with('email_changed', $emailChanged);
    }

    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $this->profileService->updatePassword(auth()->user(), $request->validated('password'));

        return back()->with('password_saved', true);
    }
}
