<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveCouponRequest;
use App\Models\Coupon;
use App\Services\Admin\CouponService;
use Inertia\Inertia;

class CouponController extends Controller
{
    public function __construct(private readonly CouponService $couponService) {}

    public function index(): \Inertia\Response
    {
        return Inertia::render('Admin/Coupons', [
            'auth'    => ['user' => auth()->user()],
            'coupons' => $this->couponService->all(),
        ]);
    }

    public function store(SaveCouponRequest $request): \Illuminate\Http\RedirectResponse
    {
        $this->couponService->create($request->validated());

        return back();
    }

    public function update(SaveCouponRequest $request, Coupon $coupon): \Illuminate\Http\RedirectResponse
    {
        $this->couponService->update($coupon, $request->validated());

        return back();
    }

    public function destroy(Coupon $coupon): \Illuminate\Http\RedirectResponse
    {
        $this->couponService->delete($coupon);

        return back();
    }

    public function toggle(Coupon $coupon): \Illuminate\Http\RedirectResponse
    {
        $this->couponService->toggle($coupon);

        return back();
    }
}
