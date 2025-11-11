<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    /**
     * 订单列表页
     *
     * Purpose: render Order index with table, filters, and actions.
     */
    protected static string $resource = OrderResource::class;
}