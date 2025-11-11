<?php

namespace App\Filament\Resources\GoodsGroupResource\Pages;

use App\Filament\Resources\GoodsGroupResource;
use Filament\Resources\Pages\ViewRecord;

class ViewGoodsGroup extends ViewRecord
{
    /**
     * 商品分组查看页
     *
     * Purpose: view GoodsGroup record details (read-only).
     */
    protected static string $resource = GoodsGroupResource::class;
}