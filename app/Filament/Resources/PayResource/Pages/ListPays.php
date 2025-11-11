<?php

namespace App\Filament\Resources\PayResource\Pages;

use App\Filament\Resources\PayResource;
use Filament\Resources\Pages\ListRecords;

class ListPays extends ListRecords
{
    /**
     * 支付渠道列表页
     *
     * Purpose: render Pay index with table, filters, and actions.
     */
    protected static string $resource = PayResource::class;
}