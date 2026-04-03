<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        return Inertia::render('Profile', [
            'auth' => ['user' => $user],
            'savedAddress' => [
                'zip'    => $user->zip,
                'city'   => $user->city,
                'street' => $user->street,
                'note'   => $user->note,
            ],
        ]);
    }

    public function updateAddress(Request $request)
    {
        $validated = $request->validate([
            'zip'    => ['nullable', 'string', 'max:10'],
            'city'   => ['nullable', 'string', 'max:100'],
            'street' => ['nullable', 'string', 'max:255'],
            'note'   => ['nullable', 'string', 'max:255'],
        ], [
            'zip.max'    => 'Az irányítószám legfeljebb 10 karakter lehet.',
            'city.max'   => 'A város neve legfeljebb 100 karakter lehet.',
            'street.max' => 'Az utca, házszám legfeljebb 255 karakter lehet.',
        ]);

        auth()->user()->update($validated);

        return back()->with('address_saved', true);
    }
}
