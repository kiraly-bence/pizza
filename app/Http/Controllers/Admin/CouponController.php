<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveCouponRequest;
use App\Models\Coupon;
use App\Services\Admin\CouponService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CouponController extends Controller
{
    public function __construct(private readonly CouponService $couponService) {}

    public function index(): Response
    {
        return Inertia::render('Admin/Coupons', [
            'coupons' => $this->couponService->all(),
        ]);
    }

    public function store(SaveCouponRequest $request): RedirectResponse
    {
        $this->couponService->create($request->validated());

        return back();
    }

    public function update(SaveCouponRequest $request, Coupon $coupon): RedirectResponse
    {
        $this->couponService->update($coupon, $request->validated());

        return back();
    }

    public function destroy(Coupon $coupon): RedirectResponse
    {
        $this->couponService->delete($coupon);

        return back();
    }

    public function toggle(Coupon $coupon): RedirectResponse
    {
        $this->couponService->toggle($coupon);

        return back();
    }
}
