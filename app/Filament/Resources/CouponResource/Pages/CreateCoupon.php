<?php

namespace App\Filament\Resources\CouponResource\Pages;

use App\Filament\Resources\CouponResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCoupon extends CreateRecord
{
    /**
     * 优惠券创建页
     *
     * Purpose: create Coupon records.
     */
    protected static string $resource = CouponResource::class;
}