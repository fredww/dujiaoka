<?php

namespace App\Filament\Resources\PayResource\Pages;

use App\Filament\Resources\PayResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePay extends CreateRecord
{
    /**
     * 创建支付渠道页
     *
     * Purpose: provide create form for payment gateways.
     */
    protected static string $resource = PayResource::class;
}