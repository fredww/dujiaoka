<?php

namespace App\Filament\Resources\CouponResource\Pages;

use App\Filament\Resources\CouponResource;
use Filament\Resources\Pages\ListRecords;

class ListCoupons extends ListRecords
{
    /**
     * 优惠券列表页
     *
     * Purpose: render Coupon index with table, filters, and actions.
     */
    protected static string $resource = CouponResource::class;
}