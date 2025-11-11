<?php

namespace App\Filament\Resources\GoodsResource\Pages;

use App\Filament\Resources\GoodsResource;
use Filament\Resources\Pages\EditRecord;

class EditGoods extends EditRecord
{
    /**
     * 商品编辑页
     *
     * Purpose: edit Goods records.
     */
    protected static string $resource = GoodsResource::class;
}