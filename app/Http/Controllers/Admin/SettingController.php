<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveContactRequest;
use App\Http\Requests\Admin\SaveOpeningHoursRequest;
use App\Http\Requests\Admin\SaveSettingsRequest;
use App\Services\Admin\SettingService;
use Inertia\Inertia;

class SettingController extends Controller
{
    public function __construct(private SettingService $settingService) {}

    public function index()
    {
        return Inertia::render('Admin/Settings', [
            'auth'         => ['user' => auth()->user()],
            'fees'         => $this->settingService->fees(),
            'openingHours' => $this->settingService->openingHours(),
            'paused'       => $this->settingService->isPaused(),
            'contact'      => $this->settingService->contactInfo(),
        ]);
    }

    public function update(SaveSettingsRequest $request)
    {
        $data = $request->validated();
        $this->settingService->updateFees($data['delivery_fee'], $data['service_fee']);

        return back()->with('success', 'Beállítások mentve.');
    }

    public function updateHours(SaveOpeningHoursRequest $request)
    {
        $this->settingService->updateOpeningHours($request->validated()['hours']);

        return back()->with('success', 'Nyitvatartás mentve.');
    }

    public function updateContact(SaveContactRequest $request)
    {
        $data = $request->validated();
        $this->settingService->updateContactInfo($data['phone'], $data['email'], $data['address']);

        return back()->with('success', 'Kapcsolati adatok mentve.');
    }

    public function togglePause()
    {
        $this->settingService->setPaused(!$this->settingService->isPaused());

        return back();
    }
}
