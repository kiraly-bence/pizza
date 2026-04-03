<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function validate(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $coupon = Coupon::where('code', strtoupper($request->code))->first();

        if (!$coupon || !$coupon->isValidForUser(auth()->id())) {
            return response()->json([
                'valid'   => false,
                'message' => 'Érvénytelen vagy lejárt kuponkód.',
            ]);
        }

        return response()->json([
            'valid'          => true,
            'discount_type'  => $coupon->discount_type,
            'discount_value' => $coupon->discount_value,
        ]);
    }
}
