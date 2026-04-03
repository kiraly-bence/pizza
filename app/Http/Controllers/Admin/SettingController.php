<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveSettingsRequest;
use App\Services\Admin\SettingService;
use Inertia\Inertia;

class SettingController extends Controller
{
    public function __construct(private SettingService $settingService) {}

    public function index()
    {
        return Inertia::render('Admin/Settings', [
            'auth' => ['user' => auth()->user()],
            'fees' => $this->settingService->fees(),
        ]);
    }

    public function update(SaveSettingsRequest $request)
    {
        $data = $request->validated();
        $this->settingService->updateFees($data['delivery_fee'], $data['service_fee']);

        return back()->with('success', 'Beállítások mentve.');
    }
}
