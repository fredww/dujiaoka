<?php

namespace App\Filament\Resources\PayResource\Pages;

use App\Filament\Resources\PayResource;
use Filament\Resources\Pages\EditRecord;

class EditPay extends EditRecord
{
    /**
     * 编辑支付渠道页
     *
     * Purpose: provide edit form for payment gateways.
     */
    protected static string $resource = PayResource::class;
}