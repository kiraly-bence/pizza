<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Label;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LabelController extends Controller
{
    public function index()
    {
        $labels = Label::withCount('products')->orderBy('name')->get();

        return Inertia::render('Admin/Labels', [
            'auth'   => ['user' => auth()->user()],
            'labels' => $labels,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', 'in:primary,secondary'],
        ]);
        Label::create($request->only('name', 'type'));
        return back();
    }

    public function update(Request $request, Label $label)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', 'in:primary,secondary'],
        ]);
        $label->update($request->only('name', 'type'));
        return back();
    }

    public function destroy(Label $label)
    {
        $label->delete();
        return back();
    }
}
