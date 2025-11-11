<?php

namespace App\Filament\Resources\GoodsResource\Pages;

use App\Filament\Resources\GoodsResource;
use Filament\Resources\Pages\ListRecords;

class ListGoods extends ListRecords
{
    /**
     * 商品列表页
     *
     * Purpose: render Goods index with table and actions.
     */
    protected static string $resource = GoodsResource::class;
}