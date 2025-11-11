<?php

namespace App\Filament\Resources\CarmisResource\Pages;

use App\Filament\Resources\CarmisResource;
use Filament\Resources\Pages\ListRecords;

class ListCarmis extends ListRecords
{
    /**
     * 卡密列表页
     *
     * Purpose: render Carmis index with table, filters, and actions.
     */
    protected static string $resource = CarmisResource::class;
}