<?php

namespace App\Filament\Resources\GoodsGroupResource\Pages;

use App\Filament\Resources\GoodsGroupResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGoodsGroup extends CreateRecord
{
    /**
     * 商品分组创建页
     *
     * Purpose: create GoodsGroup records.
     */
    protected static string $resource = GoodsGroupResource::class;
}