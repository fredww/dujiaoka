<?php

namespace App\Filament\Resources\CouponResource\Pages;

use App\Filament\Resources\CouponResource;
use Filament\Resources\Pages\EditRecord;

class EditCoupon extends EditRecord
{
    /**
     * 优惠券编辑页
     *
     * Purpose: edit Coupon records.
     */
    protected static string $resource = CouponResource::class;
}