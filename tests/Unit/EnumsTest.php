<?php

namespace Tests\Unit;

use App\Enums\DiscountType;
use App\Enums\LabelType;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\UserRole;
use Tests\TestCase;

class EnumsTest extends TestCase
{
    public function test_order_status_values_returns_all_cases(): void
    {
        $values = OrderStatus::values();

        $this->assertContains('pending',    $values);
        $this->assertContains('confirmed',  $values);
        $this->assertContains('preparing',  $values);
        $this->assertContains('delivering', $values);
        $this->assertContains('delivered',  $values);
        $this->assertContains('cancelled',  $values);
        $this->assertCount(6, $values);
    }

    public function test_user_role_values_returns_all_cases(): void
    {
        $values = UserRole::values();

        $this->assertContains('user',  $values);
        $this->assertContains('admin', $values);
        $this->assertCount(2, $values);
    }

    public function test_discount_type_values_returns_all_cases(): void
    {
        $values = DiscountType::values();

        $this->assertContains('percentage', $values);
        $this->assertContains('fixed',      $values);
        $this->assertCount(2, $values);
    }

    public function test_payment_method_values_returns_all_cases(): void
    {
        $values = PaymentMethod::values();

        $this->assertContains('cash', $values);
        $this->assertContains('card', $values);
        $this->assertCount(2, $values);
    }

    public function test_label_type_values_returns_all_cases(): void
    {
        $values = LabelType::values();

        $this->assertContains('primary',   $values);
        $this->assertContains('secondary', $values);
        $this->assertCount(2, $values);
    }

    public function test_order_status_values_returns_strings(): void
    {
        foreach (OrderStatus::values() as $value) {
            $this->assertIsString($value);
        }
    }

    public function test_order_status_from_value(): void
    {
        $this->assertSame(OrderStatus::Pending,    OrderStatus::from('pending'));
        $this->assertSame(OrderStatus::Cancelled,  OrderStatus::from('cancelled'));
        $this->assertSame(OrderStatus::Delivered,  OrderStatus::from('delivered'));
    }

    public function test_user_role_from_value(): void
    {
        $this->assertSame(UserRole::User,  UserRole::from('user'));
        $this->assertSame(UserRole::Admin, UserRole::from('admin'));
    }
}
