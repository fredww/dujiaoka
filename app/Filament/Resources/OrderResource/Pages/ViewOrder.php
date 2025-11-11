<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    /**
     * 订单查看页
     *
     * Purpose: view Order record details (read-only).
     */
    protected static string $resource = OrderResource::class;
}