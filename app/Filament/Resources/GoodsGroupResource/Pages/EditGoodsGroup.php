<?php

namespace App\Filament\Resources\GoodsGroupResource\Pages;

use App\Filament\Resources\GoodsGroupResource;
use Filament\Resources\Pages\EditRecord;

class EditGoodsGroup extends EditRecord
{
    /**
     * 商品分组编辑页
     *
     * Purpose: edit GoodsGroup records.
     */
    protected static string $resource = GoodsGroupResource::class;
}