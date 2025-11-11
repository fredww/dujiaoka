<?php

namespace App\Filament\Resources\GoodsGroupResource\Pages;

use App\Filament\Resources\GoodsGroupResource;
use Filament\Resources\Pages\ListRecords;

class ListGoodsGroups extends ListRecords
{
    /**
     * 商品分组列表页
     *
     * Purpose: render GoodsGroup index with table and actions.
     */
    protected static string $resource = GoodsGroupResource::class;
}