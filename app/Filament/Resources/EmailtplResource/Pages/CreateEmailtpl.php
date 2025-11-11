<?php

namespace App\Filament\Resources\EmailtplResource\Pages;

use App\Filament\Resources\EmailtplResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEmailtpl extends CreateRecord
{
    /**
     * 邮件模板创建页
     *
     * Purpose: create Emailtpl records.
     */
    protected static string $resource = EmailtplResource::class;
}