<?php

namespace App\Filament\Resources\GoodsResource\Pages;

use App\Filament\Resources\GoodsResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGoods extends CreateRecord
{
    /**
     * 商品创建页
     *
     * Purpose: create Goods records.
     */
    protected static string $resource = GoodsResource::class;
}