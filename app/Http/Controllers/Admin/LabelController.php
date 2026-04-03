<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveLabelRequest;
use App\Models\Label;
use App\Services\Admin\LabelService;
use Inertia\Inertia;

class LabelController extends Controller
{
    public function __construct(private LabelService $labelService) {}

    public function index()
    {
        return Inertia::render('Admin/Labels', [
            'auth'   => ['user' => auth()->user()],
            'labels' => $this->labelService->all(),
        ]);
    }

    public function store(SaveLabelRequest $request)
    {
        $this->labelService->create($request->validated());

        return back();
    }

    public function update(SaveLabelRequest $request, Label $label)
    {
        $this->labelService->update($label, $request->validated());

        return back();
    }

    public function destroy(Label $label)
    {
        $this->labelService->delete($label);

        return back();
    }
}
