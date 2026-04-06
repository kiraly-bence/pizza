<?php

namespace App\Http\Requests\Admin;

use App\Enums\DiscountType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'max_uses_per_user' => $this->max_uses_per_user ?: null,
            'expires_at' => $this->expires_at ?: null,
        ]);
    }

    public function rules(): array
    {
        $coupon = $this->route('coupon');

        return [
            'code' => ['required', 'string', 'max:50', Rule::unique('coupons', 'code')->ignore($coupon)],
            'discount_type' => ['required', Rule::in(DiscountType::values())],
            'discount_value' => ['required', 'numeric', 'min:0.01', ...($this->discount_type === 'percentage' ? ['max:100'] : [])],
            'max_uses_per_user' => ['nullable', 'integer', 'min:1'],
            'expires_at' => ['nullable', 'date', 'after:now'],
            'is_active' => ['boolean'],
        ];
    }
}
