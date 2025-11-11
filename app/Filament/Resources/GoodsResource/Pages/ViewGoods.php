<?php

namespace App\Filament\Resources\GoodsResource\Pages;

use App\Filament\Resources\GoodsResource;
use Filament\Resources\Pages\ViewRecord;

class ViewGoods extends ViewRecord
{
    /**
     * 商品查看页
     *
     * Purpose: view Goods record details (read-only).
     */
    protected static string $resource = GoodsResource::class;
}