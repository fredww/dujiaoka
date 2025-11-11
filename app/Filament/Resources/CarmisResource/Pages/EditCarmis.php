<?php

namespace App\Filament\Resources\CarmisResource\Pages;

use App\Filament\Resources\CarmisResource;
use Filament\Resources\Pages\EditRecord;

class EditCarmis extends EditRecord
{
    /**
     * 卡密编辑页
     *
     * Purpose: edit Carmis records.
     */
    protected static string $resource = CarmisResource::class;
}