<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateAddressRequest;
use App\Services\ProfileService;
use Inertia\Inertia;

class ProfileController extends Controller
{
    public function __construct(private ProfileService $profileService) {}

    public function index()
    {
        $user = auth()->user();

        return Inertia::render('Profile', [
            'auth'         => ['user' => $user],
            'savedAddress' => [
                'zip'    => $user->zip,
                'city'   => $user->city,
                'street' => $user->street,
                'note'   => $user->note,
            ],
        ]);
    }

    public function updateAddress(UpdateAddressRequest $request)
    {
        $this->profileService->updateAddress(auth()->user(), $request->validated());

        return back()->with('address_saved', true);
    }
}
