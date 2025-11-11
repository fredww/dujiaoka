<?php

namespace App\Filament\Resources\CarmisResource\Pages;

use App\Filament\Resources\CarmisResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCarmis extends CreateRecord
{
    /**
     * 卡密创建页
     *
     * Purpose: create Carmis records.
     */
    protected static string $resource = CarmisResource::class;
}