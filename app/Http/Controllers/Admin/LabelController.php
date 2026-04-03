<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveLabelRequest;
use App\Models\Label;
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

    public function store(SaveLabelRequest $request)
    {
        Label::create($request->validated());

        return back();
    }

    public function update(SaveLabelRequest $request, Label $label)
    {
        $label->update($request->validated());

        return back();
    }

    public function destroy(Label $label)
    {
        $label->delete();

        return back();
    }
}
