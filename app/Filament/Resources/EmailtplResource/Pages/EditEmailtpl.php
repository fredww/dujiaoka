<?php

namespace App\Filament\Resources\EmailtplResource\Pages;

use App\Filament\Resources\EmailtplResource;
use Filament\Resources\Pages\EditRecord;

class EditEmailtpl extends EditRecord
{
    /**
     * 邮件模板编辑页
     *
     * Purpose: edit Emailtpl records.
     */
    protected static string $resource = EmailtplResource::class;
}