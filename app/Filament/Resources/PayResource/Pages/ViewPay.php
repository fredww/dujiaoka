<?php

namespace App\Filament\Resources\PayResource\Pages;

use App\Filament\Resources\PayResource;
use Filament\Resources\Pages\ViewRecord;

class ViewPay extends ViewRecord
{
    /**
     * 支付渠道查看页
     *
     * Purpose: view payment gateway record details (read-only).
     */
    protected static string $resource = PayResource::class;
}