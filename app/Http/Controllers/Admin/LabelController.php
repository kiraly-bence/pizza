<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveLabelRequest;
use App\Models\Label;
use App\Services\Admin\LabelService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class LabelController extends Controller
{
    public function __construct(private readonly LabelService $labelService) {}

    public function index(): Response
    {
        return Inertia::render('Admin/Labels', [
            'labels' => $this->labelService->all(),
        ]);
    }

    public function store(SaveLabelRequest $request): RedirectResponse
    {
        $this->labelService->create($request->validated());

        return back();
    }

    public function update(SaveLabelRequest $request, Label $label): RedirectResponse
    {
        $this->labelService->update($label, $request->validated());

        return back();
    }

    public function destroy(Label $label): RedirectResponse
    {
        $this->labelService->delete($label);

        return back();
    }
}
