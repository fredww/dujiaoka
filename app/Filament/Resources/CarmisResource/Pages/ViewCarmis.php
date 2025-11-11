<?php

namespace App\Filament\Resources\CarmisResource\Pages;

use App\Filament\Resources\CarmisResource;
use Filament\Resources\Pages\ViewRecord;

class ViewCarmis extends ViewRecord
{
    /**
     * 卡密查看页
     *
     * Purpose: view Carmis record details (read-only).
     */
    protected static string $resource = CarmisResource::class;
}