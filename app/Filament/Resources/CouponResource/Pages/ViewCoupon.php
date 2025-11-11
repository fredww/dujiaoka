<?php

namespace App\Filament\Resources\CouponResource\Pages;

use App\Filament\Resources\CouponResource;
use Filament\Resources\Pages\ViewRecord;

class ViewCoupon extends ViewRecord
{
    /**
     * 优惠券查看页
     *
     * Purpose: view Coupon record details (read-only).
     */
    protected static string $resource = CouponResource::class;
}